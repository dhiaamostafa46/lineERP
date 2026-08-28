<?php

namespace App\Services\Hub;

use App\Models\Hub\HubApp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class EvixHubService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiSecret;

    protected int $tenantId;

    protected string $environment;

    protected int $timeout;

    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('evix_hub.base_url', 'https://hub.evixdev.com'), '/');
        $this->apiKey = (string) config('evix_hub.api_key', '');
        $this->apiSecret = (string) config('evix_hub.api_secret', '');
        $this->tenantId = (int) config('evix_hub.tenant_id', 1);
        $this->environment = (string) config('evix_hub.environment', 'production');
        $this->timeout = (int) config('evix_hub.timeout', 30);
        $this->cacheTtl = (int) config('evix_hub.cache_ttl', 3600);
    }

    /**
     * Get default headers for Hub API requests.
     */
    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Hub-Api-Key' => $this->apiKey,
            'X-Hub-Api-Secret' => $this->apiSecret,
            'X-Hub-Environment' => $this->environment,
            'X-Client-App' => config('app.name', 'EvixERP'),
            'X-Client-Url' => config('app.url', 'https://dev.evixdev.com'),
        ];
    }

    /**
     * Fetch integrations manifest directly from Evix Hub endpoint:
     * https://hub.evixdev.com/api/v1/erp/integrations/manifest
     */
    public function fetchManifest(bool $forceRefresh = false): array
    {
        $cacheKey = 'evix_hub_manifest_catalog';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                $endpoint = $this->baseUrl.config('evix_hub.endpoints.manifest', '/api/v1/erp/integrations/manifest');

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get($endpoint);

                if ($response->successful()) {
                    $body = $response->json();
                    if (isset($body['manifest']) && is_array($body['manifest'])) {
                        return $body['manifest'];
                    }
                    if (isset($body['directory']) && is_array($body['directory'])) {
                        return $body['directory'];
                    }
                }
            } catch (Throwable $e) {
                Log::error('EvixHubService: Failed to retrieve integrations manifest from Hub.', [
                    'message' => $e->getMessage(),
                    'endpoint' => $this->baseUrl.'/api/v1/erp/integrations/manifest',
                ]);
            }

            return [];
        });
    }

    /**
     * Get specific platform details from the manifest.
     */
    public function getPlatformDetail(string $code): ?array
    {
        $manifest = $this->fetchManifest();

        return $manifest[$code] ?? null;
    }

    /**
     * Activate integration on Hub and persist in local hub_app table.
     */
    public function activatePlatform(string $code, array $credentials = [], array $settings = [], ?int $tenantId = null): array
    {
        $tenantId = $tenantId ?: (auth()->user()?->org_id ?: $this->tenantId);
        $manifestItem = $this->getPlatformDetail($code);

        $endpoint = $this->baseUrl.config('evix_hub.endpoints.activate', '/api/v1/erp/integrations/activate');

        $hubResponse = null;
        $connectionId = null;
        $webhookUrl = $manifestItem['webhook_url'] ?? null;
        $callbackUrl = $manifestItem['callback_url'] ?? null;
        $status = 'active';

        try {
            $payload = [
                'tenant_id' => (string) $tenantId,
                'platform_code' => $code,
                'credentials' => $credentials,
                'settings' => $settings,
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $hubResponse = $response->json();
                $connectionId = $hubResponse['connection_id'] ?? null;
                $webhookUrl = $hubResponse['webhook_url'] ?? $webhookUrl;
                $callbackUrl = $hubResponse['callback_url'] ?? $callbackUrl;
                $status = $hubResponse['status'] ?? 'active';
            }
        } catch (Throwable $e) {
            Log::warning('EvixHubService: Could not reach Hub activate endpoint directly, saving locally.', [
                'code' => $code,
                'error' => $e->getMessage(),
            ]);
        }

        // Save or update in local database hub_app table
        $hubApp = HubApp::updateOrCreate(
            [
                'app_code' => $code,
                'org_id' => $tenantId,
            ],
            [
                'name' => $manifestItem['name'] ?? ucfirst($code),
                'category' => $manifestItem['category'] ?? 'other',
                'credentials' => $credentials,
                'settings' => $settings,
                'is_active' => true,
                'connection_id' => $connectionId,
                'connection_status' => $status,
                'webhook_url' => $webhookUrl,
                'callback_url' => $callbackUrl,
                'last_connected_at' => now(),
            ]
        );

        return [
            'success' => true,
            'message' => $hubResponse['message'] ?? 'تم تفعيل وربط التطبيق بنجاح.',
            'hub_app' => $hubApp,
            'hub_response' => $hubResponse,
        ];
    }

    /**
     * Deactivate integration in local hub_app table.
     */
    public function deactivatePlatform(string $code, ?int $tenantId = null): array
    {
        $tenantId = $tenantId ?: (auth()->user()?->org_id ?: $this->tenantId);

        $hubApp = HubApp::where('app_code', $code)
            ->where('org_id', $tenantId)
            ->first();

        if ($hubApp) {
            $hubApp->update([
                'is_active' => false,
                'connection_status' => 'inactive',
            ]);
        }

        return [
            'success' => true,
            'message' => 'تم تعطيل التطبيق بنجاح.',
            'is_active' => false,
        ];
    }

    /**
     * Get Hub connectivity info.
     */
    public function getHubStatus(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'manifest_endpoint' => $this->baseUrl.'/api/v1/erp/integrations/manifest',
            'environment' => $this->environment,
            'tenant_id' => $this->tenantId,
            'is_configured' => ! empty($this->baseUrl),
        ];
    }
}
