<?php

namespace Modules\Invoices\App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoices\App\Repositories\InvCustomerRepository;
use Livewire\Attributes\On;

class Table extends Component
{
    use WithPagination;

    public $search;
    public $status;
    public $pagination = 10;

    protected $repository;

    public function boot(InvCustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    #[On('refresh-customer-table')]
    public function refresh()
    {
        $this->render();
    }

    public function delete($id)
    {
        try {
            $this->repository->delete($id);
            $this->dispatch('alert', ['type' => 'success', 'message' => __('messages.deleted', ['model' => __('invoices::models/inv_customers.singular')])]);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $customers = $this->repository->searchAndFilter($this->search, $this->status, $this->pagination);
        
        return view('invoices::livewire.customers.table', [
            'customers' => $customers
        ]);
    }
}
