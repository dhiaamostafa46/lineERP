<?php

namespace Modules\HR\App\Livewire\Profile;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Model;

/**
 * Base class for profile tab components.
 *
 * Handles common logic for fetching and displaying paginated data
 * related to an employee's profile.
 */
abstract class ProfileTab extends Component
{
    use LivewireAlert, AuthorizesRequests, WithPagination, ProfileComponentLogic;

    protected $paginationTheme = 'bootstrap';

    /** @var bool */
    public $openModal = false;

    /** @var Model|null */
    public $model;

    /**
     * The Eloquent model class for the component.
     * e.g., HrAdvance::class
     *
     * @var string
     */
    protected string $modelClass;

    /**
     * The name of the view to render.
     * e.g., 'hr::livewire.profile.advances'
     *
     * @var string
     */
    protected string $viewName;

    public function mount()
    {
        $this->initializeProfile();
    }

    public function render()
    {

       
        return view($this->viewName, [
            'dataInf' => $this->getData(),
            'HrEmployee' => $this->HrEmployee,
        ]);
    }

    public function toggleOpenModal($id)
    {

        if ($id == 0) {
            $this->openModal = false;
            $this->model = null;
            return;
        }

        $this->openModal = true;

      
        $this->model = $this->getSingleRecord($id);

        
    }

    private function getSingleRecord($id)
    {
        

        if (!$this->HrEmployee) {
            return null;
        }

         // dd($id  , $this->openModal ,$this->model , $this->HrEmployee ,$this->modelClass );


        return $this->modelClass::where('employee_id', $this->HrEmployee->id)->findOrFail($id);
    }

    private function getData()
    {
        if (!$this->HrEmployee) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url()]);
        }

        return $this->modelClass::where('employee_id', $this->HrEmployee->id)
            ->orderByDesc('created_at')
            ->paginate(10);
    }
}
