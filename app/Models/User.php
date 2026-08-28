<?php

namespace App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\StoreApp\Store;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use \App\Traits\BelongsToBranch;

    use HasApiTokens;
    use HasRoles, ImageUploaderTrait, Notifiable, SoftDeletes;

    public $table = 'users';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'email',
        'user_type',
        'phone',
        'email_verified_at',
        'password',
        'photo',
        'status', // '1 = inactive, default(2) = active
        'remember_token',
        'emp_flage',
        'job_number',
        'org_id',
        'branch_id',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:191',
        'phone' => 'required|numeric|unique:users,phone',
        'email' => 'required|email|max:191|unique:users',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'status' => 'required|in:1,2',
        'user_type' => 'nullable|in:admin,supervisor,driver,service_center',
        'branch_id' => 'required|exists:branches,id',
    ];

    const STATUS_INACTIVE = 1;

    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    // phone mutator to clean hidden Unicode characters
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value ? preg_replace('/[^\d\+]/', '', $value) : $value;
    }

    // photo
    public function setPhotoAttribute($file)
    {
        try {
            if ($file) {
                $fileName = $this->createFileName($file);

                $this->originalImage($file, $fileName);

                $this->thumbImage($file, $fileName, 200, 200);

                $this->attributes['photo'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['photo'] = $file;
        }
    }

    public function getPhotoOriginalPathAttribute()
    {
        return $this->photo ? asset('uploads/images/original/'.$this->photo) : asset('admin_assets/media/avatars/blanksmall.jpg');
    }

    public function getPhotoOriginalavatarPathAttribute()
    {
        return $this->photo ? asset('uploads/images/original/'.$this->photo) : asset('admin_assets/media/avatars/300-3.jpg');
    }

    public function getPhotoThumbnailPathAttribute()
    {
        return $this->photo ? asset('uploads/images/thumbnail/'.$this->photo) : asset('uploads/images/original/avatar.webp');
    }
    // photo

    // append and accessor status_text input
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    // append and accessor status_icon input
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];

        return $this->status == 1 ? 'badge badge-success' : 'badge badge-danger';
    }

    /**
     * Set the User's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function deviceSessions()
    {
        return $this->hasMany(\App\Models\DeviceSession::class);
    }

    /**
     * Scope a query to only include inactive Admins.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope a query to only include active Admins.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // employee
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function getHasEmployeeAttribute()
    {
        return $this->employee()->exists();
    }

    // // employee
    public function store()
    {
        return $this->hasOne(Store::class, 'manager_user_id');
    }



    public static function normalizeSaudiPhone(?string $input): string
    {
        $digits = preg_replace('/\D/', '', (string) $input);

        if (str_starts_with($digits, '966') && strlen($digits) >= 12) {
            return '0'.substr($digits, 3);
        }

        if (str_starts_with($digits, '5') && strlen($digits) === 9) {
            return '0'.$digits;
        }

        return $digits;
    }
}
