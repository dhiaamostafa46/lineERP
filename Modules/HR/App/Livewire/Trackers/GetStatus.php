<?php

namespace Modules\HR\App\Livewire\Trackers;

use App\Events\NewNotification;
use App\Models\NotificationItem;
use Livewire\Component;
use Livewire\Attributes\On;
use Modules\HR\App\Models\HrEmployee;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\HR\App\Models\HrHolidayBalance;
use Modules\HR\App\Models\HrMonthlyPayment;
use Modules\HR\App\Models\HrTimeTrack;
use Modules\HR\App\Models\HrTimeTrackDetails;
use Modules\HR\App\Models\HrTrackingApproval;
use Modules\HR\App\Repositories\HrTrackingApprovalRepository;



class GetStatus extends Component
{
    use LivewireAlert;

    public $user_id;
    public $model;
    public $note;
    public $openModal;
    public $approvals;

    // Wizard
    public $steps;
    public $current_step;
    public $current_name;
    public $current_id;
    public $step_status;
    public $current_user_id;
    public $iam_approvals = false;

    protected HrTrackingApprovalRepository $hrApprovalRepo;

    public function boot(HrTrackingApprovalRepository $hrTrackingApprovalRepo)
    {
        $this->hrApprovalRepo = $hrTrackingApprovalRepo;
    }

    public function mount()
    {
        $this->openModal = false;
        $this->getData();
    }

    public function render()
    {
        $this->getData();
        return view('hr::livewire.trackers.get-status');
    }

    public function toggleOpenModal()
    {
        $this->openModal = !$this->openModal;
        if ($this->openModal) {
            $this->getData();
        }
    }

    public function createApproved($approval_id)
    {
        $approval = $this->hrApprovalRepo->find($approval_id);
        if ($approval && $approval->status == HrTrackingApproval::STATUS_PENDING) {
            $this->updateTrackerApproval($approval_id, HrTrackingApproval::STATUS_APPROVED);
            $this->approvalNextStep($approval->id, $approval->sort);
            $this->refreshStatus();
            $this->alertFire('success', __('hr::lang.approved_successfully'));
        } else {
            $this->alertFire('error', __('hr::lang.cannot_approved'));
        }
    }

    // عند الموافقة

    public function createRejected($approval_id)
    {
        $this->validate(['note' => 'required']);
        $approval = $this->hrApprovalRepo->find($approval_id);
        if (isset($approval->status) && $approval->status == HrTrackingApproval::STATUS_PENDING) {
            $this->updateTrackerApproval($approval_id, HrTrackingApproval::STATUS_REJECTED);
            $this->approvalNextStep($approval->id, $approval->sort);
            $this->refreshStatus();
            $this->alertFire('success', __('hr::lang.rejected_successfully'));
        } else {
            $this->alertFire('error', __('hr::lang.cannot_rejected'));
        }
    }

    public function backStep($approval_id)
    {
        $this->validate(['note' => 'required']);

        // جلب جميع الموافقات مرتبة حسب الترتيب
        $approvals = $this->hrApprovalRepo
            ->allQuery([])
            ->whereIn('id', $this->approvals->pluck('id')->toArray())
            ->orderBy('sort', 'asc')
            ->get();

        $currentApprovalIndex = null;

        // البحث عن موقع الموافقة الحالية
        foreach ($approvals as $index => $approval) {
            if ($approval->id == $approval_id) {
                $currentApprovalIndex = $index;
                break;
            }
        }

        // التحقق من إمكانية الرجوع
        if ($currentApprovalIndex === null) {
            return;
        }

        if ($currentApprovalIndex === 0) {
            return;
        }

        for ($i = $currentApprovalIndex; $i < $approvals->count(); $i++) {
            $updateData = [
                'status' => HrTrackingApproval::STATUS_PENDING,
                'is_current' => 0,
                'approved_at' => null,
            ];

            // إضافة الملاحظة فقط للخطوة التي يتم الرجوع منها
            if ($i === $currentApprovalIndex) {
                $updateData['note'] = $this->note;
            } else {
                $updateData['note'] = null;
            }

            $this->hrApprovalRepo->update($updateData, $approvals[$i]->id);
        }

        // تفعيل الخطوة السابقة
        $previousApproval = $approvals[$currentApprovalIndex - 1];
        $this->hrApprovalRepo->update(
            [
                'status' => HrTrackingApproval::STATUS_PENDING,
                'is_current' => 1,
            ],
            $previousApproval->id,
        );

        $this->refreshStatus();
        $this->note = '';
    }
    // public function backStep($approval_id)
    // {

    //     $this->validate(['note' => 'required']);

    //     $approvals = $this->hrApprovalRepo
    //         ->allQuery([])
    //         ->whereIn('id', $this->approvals->pluck('id')->toArray())
    //         ->orderByDesc('sort')
    //         ->get();

    //          dd($approvals);
    //     foreach ($approvals as $approval) {

    //         if ($approval->id == $approval_id) {
    //             $this->hrApprovalRepo->update(
    //                 [
    //                     'status' => HrTrackingApproval::STATUS_PENDING,
    //                     'note' => $this->note,
    //                     'is_current' => 0,
    //                 ],
    //                 $approval_id,
    //             );
    //         } else {
    //             $this->hrApprovalRepo->update(
    //                 [
    //                     'status' => HrTrackingApproval::STATUS_PENDING,
    //                     'is_current' => 1,
    //                 ],
    //                 $approval->id,
    //             );
    //             break;
    //         }
    //     }
    //     $this->refreshStatus();
    // }

    public function restart($approval_id)
    {
        $this->validate(['note' => 'required']);

        $approvals = $this->hrApprovalRepo
            ->allQuery([])
            ->whereIn('id', $this->approvals->pluck('id')->toArray())
            ->orderByDesc('sort')
            ->get();

        foreach ($approvals as $approval) {
            if ($approval->id == $approval_id) {
                $this->hrApprovalRepo->update(
                    [
                        'status' => HrTrackingApproval::STATUS_PENDING,
                        'note' => $this->note,
                        'is_current' => 0,
                    ],
                    $approval_id,
                );
            } elseif ($approval->sort == 1) {
                $this->hrApprovalRepo->update(
                    [
                        'status' => HrTrackingApproval::STATUS_PENDING,
                        'is_current' => 1,
                    ],
                    $approval->id,
                );
            } else {
                $this->hrApprovalRepo->update(
                    [
                        'status' => HrTrackingApproval::STATUS_PENDING,
                        'is_current' => 0,
                    ],
                    $approval->id,
                );
            }
        }

        $this->refreshStatus();
    }

    public function approveModal()
    {
        $model = $this->model;

        $model->update([
            'status' => HrTrackingApproval::STATUS_APPROVED,
            'approver_id' => auth()->id(),
        ]);

        if (get_class($model) == \Modules\HR\App\Models\HrHoliday::class) {
            $title = ' إشعار موافقة';
            $body = 'تمت الموافقة على طلب الاجازة الخاص بك';
            $this->senFcmNotification($model, $title, $body);
            // Balance is now dynamically calculated from approved holidays, no manual update needed.
        } elseif (get_class($model) == \Modules\HR\App\Models\HrAdvance::class) {
                $title = ' إشعار موافقة';
                $body = 'تمت الموافقة على طلب السلفة الخاص بك';
            $this->senFcmNotification($model, $title, $body);
            $this->approverPayment($model);

            // Accounting Integration for Advance Approval
            $journalEntry = \Modules\HR\App\Services\HrJournalEntryService::createEntry(
                (float) $model->amount,
                'hr_advance_receivable_account', // Debit
                'hr_default_cash_bank_account',  // Credit
                'صرف سلفة للموظف: ' . ($model->employee->username ?? 'Unknown'),
                get_class($model),
                $model->id,
                $model->employee_id
            );

            if ($journalEntry) {
                $model->journal_entry_id = $journalEntry->id;
                $model->save();
            }
        } elseif (get_class($model) == \Modules\HR\App\Models\HrJustification::class) {
                $title = ' إشعار موافقة';
                $body = 'تمت الموافقة على طلب التسوية الخاص بك';
            $this->senFcmNotification($model, $title, $body);
            $this->updateTimeTrackJustification($model);
        }

        $this->Notifications($model);

        $this->alertFire('success', __('hr::lang.approved_successfully'));
    }

    public function updateTimeTrackJustification($model)
    {
        $timetrack = HrTimeTrack::where('employee_id', $model->employee_id)->where('date', $model->request_date)->with('timeTrackDetails')->first();

        if (!$timetrack) {
            return;
        }

        // تحديث تفاصيل الشفت المطلوب
        HrTimeTrackDetails::where('hr_time_track_id', $timetrack->id)
            ->where('shift_from', $model->HrShift->from)
            ->where('shift_to', $model->HrShift->to)
            ->update([
                'type' => HrTimeTrackDetails::TYPE_JUSTIFICATION,
            ]);



        // إعادة تحميل العلاقة بعد التحديث
        $timetrack->load('timeTrackDetails');

        if ($timetrack->timeTrackDetails->where('type', '!=', HrTimeTrackDetails::TYPE_JUSTIFICATION)->count() === 0) {
            $timetrack->update([
                'type' => HrTimeTrack::TYPE_EXEMPT,
            ]);
        }
    }

    public function Notifications($model)
    {
        $notification = NotificationItem::where('notifiable_type', get_class($model))->where('notifiable_id', $model->id)->where('status', NotificationItem::STATUS_PENDING)->first(); // جلب الموديل فقط

        if ($notification) {
            $notification->update([
                'status' => NotificationItem::STATUS_CANCELLED,
                'confirmed_at' => now(),
                'read_at' => now(),
            ]);

            // تمرير الموديل نفسه للـ Event
            event(new NewNotification($notification));
        }
    }

    public function approverPayment($HrAdvance)
    {
        HrMonthlyPayment::where('employee_id', $HrAdvance->employee_id)
            ->where('hr_advance_id', $HrAdvance->id)
            ->update([
                'status' => HrTrackingApproval::STATUS_APPROVED,
                'approver_id' => auth()->id(),
            ]);
        
        // إعادة تحميل الصفحة بالكامل بعد التنفيذ
        return redirect(request()->header('Referer'));
    }



    public function rejectModal()
    {
        $model = $this->model;
        $model->update([
            'status' => HrTrackingApproval::STATUS_REJECTED,
        ]);
        if (get_class($model) == \Modules\HR\App\Models\HrHoliday::class) {
            $title = ' إشعار رفض';
            $body = 'تم رفض طلب الاجازة الخاص بك';
            $this->senFcmNotification($model, $title, $body);
        
        } elseif (get_class($model) == \Modules\HR\App\Models\HrAdvance::class) {
                $title = ' إشعار رفض';
                $body = 'تم رفض طلب السلفة الخاص بك';
            $this->senFcmNotification($model, $title, $body);
            
        } elseif (get_class($model) == \Modules\HR\App\Models\HrJustification::class) {
                 $title = ' إشعار رفض';
                $body = 'تم رفض طلب التسوية الخاص بك';
            $this->senFcmNotification($model, $title, $body);
        }
        $this->alertFire('success', __('hr::lang.rejected_successfully'));
    }

    private function checkApprovalsDone()
    {
        $approvals = $this->hrApprovalRepo
            ->allQuery([])
            ->whereIn('id', $this->approvals->pluck('id')->toArray())
            ->get();

        $statuses = $approvals->pluck('status')->toArray();
        if ($approvals->count() > 0 && !in_array(HrTrackingApproval::STATUS_PENDING, $statuses)) {
            if (in_array(HrTrackingApproval::STATUS_REJECTED, $statuses)) {
                $this->rejectModal();
            } elseif (in_array(HrTrackingApproval::STATUS_APPROVED, $statuses)) {
                $this->approveModal();
            }
            $this->getData();
        }
    }

    private function getData()
    {
        // get approvals
        $approvals = HrTrackingApproval::where('trackable_id', $this->model->id)
            ->where('trackable_type', get_class($this->model))
            ->get();

        // if exists
        if ($approvals) {
            $user_login = auth()->user()->id ?? 0;
            if ($user_login) {
                $this->user_id = $user_login;
            } else {
                $this->user_id = 0; // Assign 0 if no valid user login
            }

            $current = HrTrackingApproval::whereIn('id', $approvals->pluck('id')->toArray())
                ->where('is_current', 1)
                ->first();
            if ($current) {
                $this->current_id = $current->id;
                $this->current_user_id = $current->user_id;
                $this->current_step = $current->sort;
                $this->current_name = $current->user->username;
            } else {
                $this->current_step = $approvals->count();
            }

            if (in_array($this->user_id, $approvals->pluck('user_id')->toArray())) {
                $this->iam_approvals = true;
            }
            $this->steps = $approvals->count();
            $this->approvals = $approvals;
        }
        $this->CheckStatus();
    }

    private function alertFire($status, $message)
    {
        $this->alert($status, $message);
    }

    private function CheckStatus()
    {
        $approvals = $this->approvals;

        if ($approvals->count() > 0) {
            if (in_array(HrTrackingApproval::STATUS_PENDING, $approvals->pluck('status')->toArray())) {
                $this->step_status = 'has_track_pending';
            } else {
                $this->step_status = 'has_track_finished';
            }
        } else {
            if ($this->model->status == 1) {
                $this->step_status = 'do_not_have_track_pending';
            } else {
                $this->step_status = 'do_not_have_track_finished';
            }
        }
    }

    private function refreshStatus()
    {
        $this->note = null;
        $this->getData();
        $this->checkApprovalsDone();
    }

    private function updateTrackerApproval($approval_id, $status, $is_current = 0)
    {
        $this->hrApprovalRepo->update(
            [
                'status' => $status,
                'is_current' => $is_current,
                'note' => $this->note,
            ],
            $approval_id,
        );
        $this->note = null;
    }

    private function approvalNextStep($current_approval_id, $current_approval_sort)
    {
        $approval = HrTrackingApproval::whereIn('id', $this->approvals->pluck('id')->toArray())
            ->where('sort', $current_approval_sort + 1)
            ->where('id', '!=', $current_approval_id)
            ->first();

        if ($approval) {
            $approval->update([
                'is_current' => 1,
            ]);
        }
    }

    private function approvalBackStep($current_approval_id, $current_approval_sort)
    {
        $approval = HrTrackingApproval::whereIn('id', $this->approvals->pluck('id')->toArray())
            ->where('sort', $current_approval_sort - 1)
            ->where('id', '!=', $current_approval_id)
            ->first();

        if ($approval) {
            $approval->update([
                'is_current' => 1,
                'status' => HrTrackingApproval::STATUS_PENDING,
            ]);
        }
    }

    private function senFcmNotification($model,$title,$body)
    {
        $t = $title;
        $b = $body;
             $employee = HrEmployee::find($model->employee_id);
             $model_type = get_class($model);
     if (get_class($model) == \Modules\HR\App\Models\HrHoliday::class) {
            // إرسال إشعار
                 
    
             $result = $employee->main_employee->user->deviceSessions()->where('device_type','!=','Desktop')->where('is_active', 1)->get()->each(function ($deviceSession) use ($model,$title,$body) {
                 
                app(\App\Services\pushNotificationService::class)->sendToToken(
                     token: $deviceSession->device_token ??"",
                     title:$title,
                     body: $body,
                     data: [
                         'type' => 'request_status',
                         'id' => $model->id,
                     ]
                 );
             });
             //dd($result);
        } elseif (get_class($model) == \Modules\HR\App\Models\HrAdvance::class) {
           // إرسال إشعار
     
        
            $result = $employee->main_employee->user->deviceSessions()->where('device_type','!=','Desktop')->where('is_active', 1)->each(function ($deviceSession) use ($model,$title,$body) {
                app(\App\Services\pushNotificationService::class)->sendToToken(
                     token: $deviceSession->device_token ??"",
                     title:$title,
                     body: $body,
                     data: [
                         'type' => 'request_status',
                         'id' => $model->id,
                     ]
                 );
             });
             
            
        } elseif (get_class($model) == \Modules\HR\App\Models\HrJustification::class) {
             // إرسال إشعار

                 $result = $employee->main_employee->user->deviceSessions()->where('device_type','!=','Desktop')->where('is_active', 1)->each(function ($deviceSession) use ($model,$title,$body) {
                app(\App\Services\pushNotificationService::class)->sendToToken(
                     token: $deviceSession->device_token ??"",
                     title:$title,
                     body: $body,
                     data: [
                         'type' => 'request_status',
                         'id' => $model->id,
                     ]
                 );
             });
        }
        

        /////////////store notification to database////////
            $type = NotificationItem::TYPE_REQUEST_STATUS;
         $fingerprint = hash('sha256', implode('|', [$model->employee_id, $type, $model->id, $model->created_at]));

        // التحقق من وجود تنبيه مشابه نشط
            $existing = NotificationItem::where('fingerprint', $fingerprint)
            ->whereIn('status', [1, 4])
            ->first();
        if ($existing) {
           // $this->line("Notification already exists for employee {$employee->id} - {$type}");
            return;
        }
        //dd($title,$body);
        NotificationItem::create([
            'org_id' => $employee->main_employee->org_id ?? 1,
            'user_id' => $employee->main_employee->user->id ?? 0,
            'notification_type' => $type,
            'notifiable_id' =>$model->id,
            'title' => $t,
            'body' => $b,
            'notifiable_type' => $model_type,
            'channel' => NotificationItem::CHANNEL_MOBILE_PUSH,
            'status' => 1, // نشط
            'fingerprint' => $fingerprint,
            'read_at' => null
        ]);

    }
}
