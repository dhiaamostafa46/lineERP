<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Database\Factories\HrEndServiceFactory;

class HrEndService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_end_services';

    protected $fillable = [
        'employee_id',
        'end',
        'description',
        'reason',
        'reward_amount',
        'approved',
        'status',
    ];

    protected $casts = [
        'end' => 'date',
        'approved' => 'boolean',
        'reward_amount' => 'decimal:2',
        'status' => 'integer',
    ];


    public static function rules()
    {
        return [
            'employee_id' => 'required',
            'end' => 'required',
            'reason' => 'required',
            'reward_amount' => 'required',
        ];
    }
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id')->withTrashed();
    }

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;


    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected')
        ];
    }



      // تعريف الثوابت للأسباب
      const REASON_DURATION_END = 1;
    const REASON_UNLAWFUL_TERMINATION = 2;
    const REASON_ARTICLE_80 = 3;
    const REASON_FORCE_MAJEURE = 4;
    const REASON_WOMAN_POST_DELIVERY = 5;
    const REASON_WOMAN_POST_MARRIAGE = 6;
    const REASON_ARTICLE_81 = 7;
    const REASON_RESIGNATION = 8;
    const REASON_AGREEMENT = 9;
    const REASON_WORKER_DISABILITY = 10;
    const REASON_EMPLOYER_DEATH = 11;
    const REASON_WORKER_DEATH = 12;
    const REASON_BUSINESS_TRANSFER = 13;
    const REASON_RETIREMENT = 14;
    const REASON_NOTICE_ARTICLE_75 = 15;
    const REASON_TRIAL_PERIOD = 16;

      // دالة لإرجاع الأسباب
      public static function reasons()
      {
          return [
              self::REASON_DURATION_END =>   __('hr::models/hr_end_service.reasonList.termination_duration_end'),
              self::REASON_UNLAWFUL_TERMINATION =>  __('hr::models/hr_end_service.reasonList.termination_unlawful_termination'),
              self::REASON_ARTICLE_80 =>  __('hr::models/hr_end_service.reasonList.termination_article_80'),
              self::REASON_FORCE_MAJEURE =>  __('hr::models/hr_end_service.reasonList.termination_force_majeure'),
              self::REASON_WOMAN_POST_DELIVERY => __('hr::models/hr_end_service.reasonList.termination_woman_post_delivery'),
              self::REASON_WOMAN_POST_MARRIAGE => __('hr::models/hr_end_service.reasonList.termination_woman_post_marriage'),
              self::REASON_ARTICLE_81 =>  __('hr::models/hr_end_service.reasonList.termination_article_81'),
              self::REASON_RESIGNATION =>  __('hr::models/hr_end_service.reasonList.resignation'),
              self::REASON_AGREEMENT =>  __('hr::models/hr_end_service.reasonList.termination_agreement'),
              self::REASON_WORKER_DISABILITY =>__('hr::models/hr_end_service.reasonList.termination_worker_disability'),
              self::REASON_EMPLOYER_DEATH => __('hr::models/hr_end_service.reasonList.termination_employer_death'),
              self::REASON_WORKER_DEATH =>  __('hr::models/hr_end_service.reasonList.termination_worker_death'),
              self::REASON_BUSINESS_TRANSFER =>  __('hr::models/hr_end_service.reasonList.termination_business_transfer'),
              self::REASON_RETIREMENT => __('hr::models/hr_end_service.reasonList.termination_unlawful_termination'),
              self::REASON_NOTICE_ARTICLE_75 => __('hr::models/hr_end_service.reasonList.termination_notice_article_75'),
              self::REASON_TRIAL_PERIOD =>  __('hr::models/hr_end_service.reasonList.resignation_trial_period'),
          ];
      }

      // دالة للحصول على نص السبب
      public function getReasonTextAttribute()
      {
          return self::reasons()[$this->reason] ?? '';
      }





    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING  => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];
        return $badges[$this->status];
    }



}


















