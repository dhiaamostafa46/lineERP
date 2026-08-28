<?php

namespace Modules\Invoices\App\Livewire\Customers;

use Livewire\Component;
use Modules\Invoices\App\Repositories\InvCustomerRepository;
use App\Models\Branch;
use App\Models\AccuSoft\TreeAccounts;
use Livewire\Attributes\On;

class Form extends Component
{
    public $customer_id;
    public $is_edit = false;

    // Form fields
    public $name = []; // For translations
    public $phone;
    public $email;
    public $vat_number;
    public $cr_number;
    public $tree_account_id;
    public $branch_id;
    public $credit_limit;
    public $status = 1;
    public $address;

    protected $repository;

    public function boot(InvCustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mount($customer_id = null)
    {
        if ($customer_id) {
            $this->loadCustomer($customer_id);
        } else {
            $this->resetFields();
        }
    }

    public function resetFields()
    {
        $this->customer_id = null;
        $this->is_edit = false;
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
        $this->phone = '';
        $this->email = '';
        $this->vat_number = '';
        $this->cr_number = '';
        $this->tree_account_id = '';
        $this->branch_id = '';
        $this->credit_limit = '';
        $this->status = 1;
        $this->address = '';
        $this->resetErrorBag();
    }

    #[On('openCreateModal')]
    public function openCreate()
    {
        $this->resetFields();
        $this->dispatch('show-customer-modal');
    }

    #[On('openEditModal')]
    public function loadCustomer($id)
    {
        $this->resetFields();
        $customer = $this->repository->find($id);
        if ($customer) {
            $this->customer_id = $id;
            $this->is_edit = true;
            
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $customer->translate($locale)->name ?? '';
            }
            
            $this->phone = $customer->phone;
            $this->email = $customer->email;
            $this->vat_number = $customer->vat_number;
            $this->cr_number = $customer->cr_number;
            $this->tree_account_id = $customer->tree_account_id;
            $this->branch_id = $customer->branch_id;
            $this->credit_limit = $customer->credit_limit;
            $this->status = $customer->status;
            $this->address = $customer->address;

            $this->dispatch('show-customer-modal');
            // Dispatch event to update select2 if needed
            $this->dispatch('customer-data-loaded', [
                'tree_account_id' => $this->tree_account_id,
                'branch_id' => $this->branch_id,
                'status' => $this->status
            ]);
        }
    }

    public function save()
    {
        $rules = [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_number' => 'nullable|string|max:50',
            'cr_number' => 'nullable|string|max:50',
            'tree_account_id' => 'required',
            'branch_id' => 'required',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|boolean',
            'address' => 'nullable|string',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
        }

        $validatedData = $this->validate($rules);

        // Format data for repository (translations)
        $data = $validatedData;
        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }
        unset($data['name']);

        try {
            if ($this->is_edit) {
                $this->repository->update($data, $this->customer_id);
                $message = __('messages.updated', ['model' => __('invoices::models/inv_customers.singular')]);
            } else {
                $this->repository->create($data);
                $message = __('messages.saved', ['model' => __('invoices::models/inv_customers.singular')]);
            }

            $this->dispatch('hide-customer-modal');
            $this->dispatch('refresh-customer-table');
            $this->dispatch('alert', ['type' => 'success', 'message' => $message]);
            
            if (!$this->is_edit) {
                $this->resetFields();
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('invoices::livewire.customers.form', [
            'accounts' => TreeAccounts::active()->get()->pluck('name', 'id')->toArray(),
            'branches' => Branch::all()->pluck('name', 'id')->toArray(),
        ]);
    }
}
