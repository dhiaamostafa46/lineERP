<?php

namespace Modules\HR\App\Livewire\Profile;

use App\Models\Employee;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;

class Show extends Component
{
    use LivewireAlert, AuthorizesRequests;

    public ?Employee $employee = null;
    public bool $openpage = false;
    public $tab;

    protected EmployeeRepository $employeeRepository;

    /**
     * ربط المستودعات (Repositories)
     */
    public function boot(EmployeeRepository $employeeRepository, HrEmployeeRepository $hrEmployeeRepository)
    {
          $this->employeeRepository = $employeeRepository;
          $this->employee = auth()->user()->employee;
    }



    /**
     * التهيئة الأولية للمكون
     */
    public function mount()
    {
        // جلب الموظف المرتبط بالمستخدم الحالي
        $this->employee = auth()->user()->employee;

        // إذا لم يكن لدى المستخدم بيانات موظف
        if (!$this->employee) {
            $this->openpage = false;
            return; // لا داعي للتحقق من الصلاحيات أو تحميل التبويبات
        }

        // في حال وجود بيانات موظف
        $this->openpage = true;

        // التحقق من صلاحية الوصول للملف الشخصي
       // $this->authorizeAccess();

        // إعداد التبويب الافتراضي
        $this->tab = $this->employee?->tab ?? Employee::TABS['main'];
    }

    /**
     * عرض صفحة الملف الشخصي
     */
    public function render()
    {
        return view('hr::livewire.profile.show');
    }

    /**
     * تغيير التبويب الحالي
     */
    public function changeTab($tab)
    {
       // $this->authorizeAccess();

        // if (!in_array($tab, Employee::availableTabs())) {
        //     $this->alert('error', __('lang.invalid_tab'));
        //     return;
        // }

        $this->tab = $tab;
        // تحديث التبويب في قاعدة البيانات
        $this->employeeRepository->update(['tab' => $tab], $this->employee->id, false);
    }

    /**
     * التحقق من صلاحية المستخدم للوصول لهذا الملف الشخصي
     */
    // protected function authorizeAccess()
    // {
    //     $authEmployee = auth()->user()->employee;

    //     if (!$authEmployee) {
    //         abort(403, __('lang.unauthorized_access'));
    //     }

    //     if ($authEmployee->id !== $this->employee?->id && !$this->canViewOtherProfiles()) {
    //         abort(403, __('lang.cannot_view_other_profiles'));
    //     }
    // }

    /**
     * التحقق من صلاحية عرض ملفات الموظفين الآخرين
     */
    // protected function canViewOtherProfiles(): bool
    // {
    //     return auth()->user()->hasRole(['super-admin', 'admin']);
    // }
}
