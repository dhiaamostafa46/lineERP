<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class HubApp extends Model
{
    use HasFactory;

    public $table = 'hub_app';

    protected $fillable = [
        'org_id',
        'app_code',
        'name',
        'category',
        'credentials',
        'settings',
        'is_active',
        'connection_id',
        'connection_status',
        'webhook_url',
        'callback_url',
        'last_connected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    // Status Constants
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_CONNECTED = 'connected';

    public const STATUS_DISCONNECTED = 'disconnected';

    public const STATUS_ERROR = 'error';

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'app_code' => 'required|string|max:100',
            'org_id' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Mutator to safely encrypt credentials.
     *
     * @param  array|string|null  $value
     */
    public function setCredentialsAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['credentials'] = null;

            return;
        }

        try {
            if (is_array($value)) {
                $this->attributes['credentials'] = Crypt::encryptString(json_encode($value));
            } else {
                $this->attributes['credentials'] = Crypt::encryptString((string) $value);
            }
        } catch (Throwable $e) {
            $this->attributes['credentials'] = null;
        }
    }

    /**
     * Accessor to decrypt credentials.
     *
     * @param  string|null  $value
     */
    public function getCredentialsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        try {
            $decrypted = Crypt::decryptString($value);
            $decoded = json_decode($decrypted, true);

            return is_array($decoded) ? $decoded : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get masked credentials for display in the UI without exposing sensitive keys.
     */
    public function getMaskedCredentials(array $schema = []): array
    {
        $credentials = $this->credentials;
        $masked = [];

        $secretKeys = [];
        foreach ($schema as $field) {
            $type = $field['type'] ?? 'text';
            $key = $field['key'] ?? ($field['name'] ?? '');
            if (in_array($type, ['password', 'secret', 'textarea'])) {
                $secretKeys[] = $key;
            }
        }

        foreach ($credentials as $key => $val) {
            if (empty($val)) {
                $masked[$key] = '';

                continue;
            }

            if (in_array($key, $secretKeys) || str_contains(strtolower($key), 'secret') || str_contains(strtolower($key), 'token') || str_contains(strtolower($key), 'key') || str_contains(strtolower($key), 'pass')) {
                $len = strlen((string) $val);
                if ($len <= 8) {
                    $masked[$key] = '••••••••';
                } else {
                    $prefix = substr((string) $val, 0, 3);
                    $suffix = substr((string) $val, -3);
                    $masked[$key] = $prefix.'••••••••'.$suffix;
                }
            } else {
                $masked[$key] = $val;
            }
        }

        return $masked;
    }

    /**
     * Status Badge HTML Helper
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active && in_array($this->connection_status, [self::STATUS_ACTIVE, self::STATUS_CONNECTED])) {
            return '<span class="badge badge-light-success fw-bold">'.__('lang.active').'</span>';
        }

        return '<span class="badge badge-light-danger fw-bold">'.__('lang.inactive').'</span>';
    }

    /**
     * Scope for active applications.
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by organization.
     */
    public function scopeForOrg($query, $orgId)
    {
        return $query->where('org_id', $orgId);
    }
}
