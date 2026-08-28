<?php
namespace Modules\HR\App\Livewire\Profile;

use App\Models\Employee;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Repositories\EmployeeRepository;
use Livewire\WithPagination;
use Modules\HR\App\Models\HrDocument;
use Modules\HR\App\Repositories\HrEmployeeRepository;

class Assets extends Component
{


      use LivewireAlert, AuthorizesRequests, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $employee;
    public $HrEmployee;
    public $user;

    public $openModal = false;
    public $model;

    public function mount()
    {
        $this->user = auth()->id();
        $this->employee = auth()->user()->employee;
        $this->HrEmployee = $this->employee?->hrEmployee;
    }

    public function render()
    {
        $Justifications = $this->getJustifications();

        return view('hr::livewire.profile.assets', [
            'dataInf' => $Justifications,
            'HrEmployee' => $this->HrEmployee
        ]);
    }

    public function toggleOpenModal($id)
    {
        // إذا تم تمرير 0 نغلق المودال
        if ($id == 0) {
            $this->openModal = false;
            $this->model = null;
            return;
        }

        $this->openModal = true;
        $this->model = $this->getDataEmployee($id);
    }

    private function getDataEmployee($id)
    {
        if (!$this->HrEmployee) {
            return null;
        }
        return HrDocument::findOrFail($id);
    }

    private function getJustifications()
    {
        if (!$this->HrEmployee) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                10,
                1,
                ['path' => request()->url()]
            );
        }

        return HrDocument::where('employee_id', $this->HrEmployee->id)
            ->orderByDesc('created_at')
            ->paginate(10);
    }


}
