<?php

namespace App\Http\Controllers\Hub;

use App\Helpers\PlatformLogoHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\HubAppRepository;
use App\Services\Hub\EvixHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ApplicationController extends AppBaseController
{
    public function __construct(
        protected HubAppRepository $hubAppRepository,
        protected EvixHubService $hubService
    ) {}

    /**
     * Display a listing of applications directly fetched from Hub manifest.
     */
    public function index(Request $request): View
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);

        // Fetch live manifest directly from Hub
        $manifest = $this->hubService->fetchManifest();

        // Fetch already activated applications in this ERP from hub_app repository
        $activatedApps = $this->hubAppRepository->getActivatedAppsForOrg($tenantId);

        $category = $request->get('category', 'all');
        $status = $request->get('status', 'all');
        $search = strtolower(trim((string) $request->get('search')));

        $integrations = [];
        $categoriesSet = [];

        foreach ($manifest as $code => $item) {
            $appCode = $item['code'] ?? $code;
            $appCategory = $item['category'] ?? 'other';
            $categoriesSet[$appCategory] = true;

            $hubApp = $activatedApps->get($appCode);
            $isActive = $hubApp ? (bool) $hubApp->is_active : false;
            $connectionStatus = $hubApp ? $hubApp->connection_status : 'disconnected';
            $lastConnectedAt = $hubApp ? $hubApp->last_connected_at : null;

            // Resolve high quality official logo
            $logoUrl = $item['logo_url'] ?: PlatformLogoHelper::getLogo($appCode);

            // Apply Category Filter
            if ($category && $category !== 'all' && $appCategory !== $category && ($item['type'] ?? '') !== $category) {
                continue;
            }

            // Apply Status Filter
            if ($status && $status !== 'all') {
                if ($status === 'active' && ! $isActive) {
                    continue;
                }
                if ($status === 'inactive' && $isActive) {
                    continue;
                }
                if ($status === 'connected' && $connectionStatus !== 'active' && $connectionStatus !== 'connected') {
                    continue;
                }
            }

            // Apply Search Filter
            if ($search !== '') {
                $name = strtolower($item['name'] ?? '');
                $desc = strtolower($item['description_ar'] ?? ($item['description'] ?? ''));
                if (! str_contains($name, $search) && ! str_contains($desc, $search) && ! str_contains($appCode, $search)) {
                    continue;
                }
            }

            $integrations[] = (object) [
                'code' => $appCode,
                'name' => $item['name'] ?? ucfirst($appCode),
                'category' => $appCategory,
                'type' => $item['type'] ?? $appCategory,
                'description' => $item['description_ar'] ?? ($item['description'] ?? ''),
                'logo_url' => $logoUrl,
                'activation_type' => $item['activation_type'] ?? ($item['auth_type'] ?? 'api_key'),
                'supports_oauth' => ! empty($item['supports_oauth']),
                'oauth_url' => $item['oauth_url'] ?? null,
                'webhook_url' => $item['webhook_url'] ?? null,
                'doc_url' => $item['doc_url'] ?? null,
                'supported_features' => $item['supported_features'] ?? [],
                'fields' => $item['fields'] ?? [],
                'is_active' => $isActive,
                'connection_status' => $connectionStatus,
                'last_connected_at' => $lastConnectedAt,
                'hub_app' => $hubApp,
            ];
        }

        // Statistics
        $stats = [
            'total' => count($manifest),
            'active' => $activatedApps->where('is_active', true)->count(),
            'connected' => $activatedApps->where('is_active', true)->whereIn('connection_status', ['active', 'connected'])->count(),
            'need_config' => count($manifest) - $activatedApps->where('is_active', true)->count(),
        ];

        $availableCategories = array_keys($categoriesSet);
        $hubStatus = $this->hubService->getHubStatus();

        return view('applications.index', compact('integrations', 'stats', 'hubStatus', 'category', 'status', 'search', 'availableCategories'));
    }

    /**
     * Get platform dynamic details for modal popup on the same page.
     */
    public function details(string $code): JsonResponse
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);

        $platform = $this->hubService->getPlatformDetail($code);
        if (! $platform) {
            return $this->sendError('المنصة غير موجودة في Hub Manifest', 404);
        }

        $hubApp = $this->hubAppRepository->findByCode($code, $tenantId);
        $schema = $platform['fields'] ?? [];
        $maskedCredentials = $hubApp ? $hubApp->getMaskedCredentials($schema) : [];
        $webhookUrl = $platform['webhook_url'] ?? ($hubApp?->webhook_url ?: url("/api/v1/webhooks/{$code}"));
        $logoUrl = $platform['logo_url'] ?? PlatformLogoHelper::getLogo($code);

        return $this->sendResponse([
            'code' => $code,
            'name' => $platform['name'] ?? ucfirst($code),
            'category' => $platform['category'] ?? 'other',
            'category_name' => __('models/applications.categories.'.($platform['category'] ?? 'other')),
            'description' => $platform['description_ar'] ?? ($platform['description'] ?? ''),
            'logo_url' => $logoUrl,
            'version' => $platform['version'] ?? '2.0.0',
            'activation_type' => $platform['activation_type'] ?? 'api_key',
            'supports_oauth' => ! empty($platform['supports_oauth']),
            'oauth_url' => $platform['oauth_url'] ?? null,
            'doc_url' => $platform['doc_url'] ?? null,
            'supported_features' => $platform['supported_features'] ?? [],
            'fields' => $schema,
            'is_active' => $hubApp ? (bool) $hubApp->is_active : false,
            'connection_status' => $hubApp ? $hubApp->connection_status : 'disconnected',
            'environment' => $hubApp ? ($hubApp->settings['environment'] ?? 'production') : 'production',
            'masked_credentials' => $maskedCredentials,
            'settings' => $hubApp ? ($hubApp->settings ?? []) : [],
            'webhook_url' => $webhookUrl,
            'last_connected_at' => $hubApp && $hubApp->last_connected_at ? $hubApp->last_connected_at->diffForHumans() : null,
        ], 'تم جلب تفاصيل المنصة بنجاح');
    }

    /**
     * Display integration configuration & dynamic schema fields for a platform from manifest (Full Page).
     */
    public function show(string $code): View
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);

        $platform = $this->hubService->getPlatformDetail($code);
        if (! $platform) {
            abort(404, 'المنصة المطلوبة غير متوفرة في Hub Manifest.');
        }

        $hubApp = $this->hubAppRepository->findByCode($code, $tenantId);
        $schema = $platform['fields'] ?? [];
        $maskedCredentials = $hubApp ? $hubApp->getMaskedCredentials($schema) : [];
        $webhookUrl = $platform['webhook_url'] ?? ($hubApp?->webhook_url ?: url("/api/v1/webhooks/{$code}"));
        $platform['logo_url'] = $platform['logo_url'] ?? PlatformLogoHelper::getLogo($code);

        return view('applications.show', compact('platform', 'code', 'hubApp', 'maskedCredentials', 'webhookUrl', 'schema'));
    }

    /**
     * Activate platform on Hub and save to local hub_app table.
     */
    public function activate(Request $request, string $code): JsonResponse|RedirectResponse
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);
        $platform = $this->hubService->getPlatformDetail($code);

        $schema = $platform['fields'] ?? [];
        $existingApp = $this->hubAppRepository->findByCode($code, $tenantId);

        $existingCredentials = $existingApp ? $existingApp->credentials : [];
        $existingSettings = $existingApp ? ($existingApp->settings ?? []) : [];

        $inputCredentials = $request->input('credentials', []);
        $inputSettings = $request->input('settings', []);

        $newCredentials = [];
        $newSettings = [];

        foreach ($schema as $field) {
            $key = $field['key'] ?? ($field['name'] ?? null);
            $type = $field['type'] ?? 'text';

            if (! $key) {
                continue;
            }

            if ($type === 'switch' || $type === 'checkbox') {
                $newSettings[$key] = ! empty($inputSettings[$key]) && ($inputSettings[$key] === '1' || $inputSettings[$key] === true || $inputSettings[$key] === 'on');
            } else {
                $submittedVal = $inputCredentials[$key] ?? null;

                // Check if user left masked value unchanged
                if ($submittedVal !== null && (str_contains($submittedVal, '••••') || $submittedVal === '')) {
                    if (isset($existingCredentials[$key]) && $submittedVal !== '') {
                        $newCredentials[$key] = $existingCredentials[$key];
                    }
                } elseif ($submittedVal !== null) {
                    $newCredentials[$key] = trim((string) $submittedVal);
                }
            }
        }

        if ($request->has('environment')) {
            $newSettings['environment'] = $request->input('environment');
        }

        try {
            $result = $this->hubService->activatePlatform(
                $code,
                $newCredentials,
                array_merge($existingSettings, $newSettings),
                $tenantId
            );

            $message = $result['message'] ?? 'تم تفعيل وربط المنصة بنجاح.';

            if ($request->ajax() || $request->wantsJson()) {
                return $this->sendResponse([
                    'is_active' => true,
                    'code' => $code,
                    'hub_app' => $result['hub_app'] ?? null,
                ], $message);
            }

            flash()->success($message);

            return redirect()->route('applications.index');
        } catch (Throwable $e) {
            $errMsg = 'حدث خطأ أثناء التفعيل: '.$e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->sendError($errMsg, 500);
            }

            flash()->error($errMsg);

            return redirect()->route('applications.index');
        }
    }

    /**
     * Deactivate platform in local hub_app table.
     */
    public function deactivate(Request $request, string $code): JsonResponse
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);
        $result = $this->hubService->deactivatePlatform($code, $tenantId);

        return $this->sendSuccess($result['message'] ?? 'تم تعطيل التطبيق بنجاح.');
    }

    /**
     * Toggle status switch.
     */
    public function toggleStatus(Request $request, string $code): JsonResponse
    {
        $tenantId = auth()->user()?->org_id ?: config('evix_hub.tenant_id', 1);
        $isActive = $request->boolean('is_active');

        if ($isActive) {
            $existingApp = $this->hubAppRepository->findByCode($code, $tenantId);

            $credentials = $existingApp ? $existingApp->credentials : [];
            $settings = $existingApp ? ($existingApp->settings ?? []) : [];

            $result = $this->hubService->activatePlatform($code, $credentials, $settings, $tenantId);

            return $this->sendResponse(['is_active' => true, 'code' => $code], $result['message'] ?? 'تم تفعيل التطبيق بنجاح.');
        } else {
            $result = $this->hubService->deactivatePlatform($code, $tenantId);

            return $this->sendResponse(['is_active' => false, 'code' => $code], 'تم تعطيل التطبيق بنجاح.');
        }
    }

    /**
     * Sync/Refresh manifest cache from Hub.
     */
    public function syncFromHub(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $manifest = $this->hubService->fetchManifest(true);
            $count = count($manifest);
            $message = __('models/applications.sync_success', ['count' => $count]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->sendResponse(['total' => $count], $message);
            }

            flash()->success($message);

            return redirect()->route('applications.index');
        } catch (Throwable $e) {
            $errorMsg = 'فشل تحديث البيانات من Hub: '.$e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->sendError($errorMsg, 500);
            }

            flash()->error($errorMsg);

            return redirect()->route('applications.index');
        }
    }
}
