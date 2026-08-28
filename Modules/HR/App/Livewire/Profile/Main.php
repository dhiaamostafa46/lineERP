<?php

namespace Modules\HR\App\Livewire\Profile;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrPostRepository;

class Main extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public ?Employee $employee = null;

    public bool $openpage = false;

    public $tab;

    protected EmployeeRepository $employeeRepository;

    protected HrEmployeeRepository $hrEmployeeRepository;

    protected HrPostRepository $hrPostRepository;

    /**
     * ربط المستودعات (Repositories)
     */
    public function boot(
        EmployeeRepository $employeeRepository,
        HrEmployeeRepository $hrEmployeeRepository,
        HrPostRepository $hrPostRepository
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->hrEmployeeRepository = $hrEmployeeRepository;
        $this->hrPostRepository = $hrPostRepository;
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
        if (! $this->employee) {
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

        if ($this->employee?->exists) {
            $this->employee = $this->employeeRepository->find($this->employee->id);
        }

        $posts = $this->employee
            ? $this->hrPostRepository->publishedForEmployee($this->employee, 10)
            : collect();

        return view('hr::livewire.profile.main', compact('posts'));
    }
}
