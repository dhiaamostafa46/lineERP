<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Database\Factories\HrTaskDetailFactory;

class HrTaskDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_task_details'; // Define the table name explicitly

    protected $fillable = [
        'hr_task_id',
        'description',
        'employee_id',
        'userID',
        'file',
    ];


    /**
     * Relationship to HrTask.
     */
    public function task()
    {
        return $this->belongsTo(HrTask::class, 'hr_task_id');
    }

    /**
     * Relationship to HrEmployee.
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }


    public function User()
    {
        return $this->belongsTo(User::class ,'userID');
    }




    public static function rules()
    {
        return [
            'hr_task_id' => 'required',
            'description' => 'required',

        ];
    }


    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary'; // Default badge if status is unknown
    }


}
