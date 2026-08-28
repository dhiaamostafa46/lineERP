<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrPayrollTransactionRequest;
use Modules\HR\App\Http\Requests\UpdateHrPayrollTransactionRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrPayrollTransactionRepository;
use Illuminate\Http\Request;


class HrPayrollTransactionController extends AppBaseController
{
    /** @var HrPayrollTransactionRepository $hrPayrollTransactionRepository*/
    private $hrPayrollTransactionRepository;

    public function __construct(HrPayrollTransactionRepository $hrPayrollTransactionRepo)
    {
        $this->hrPayrollTransactionRepository = $hrPayrollTransactionRepo;
    }

    /**
     * Display a listing of the HrPayrollTransaction.
     */
    public function index(Request $request)
    {
        $hrPayrollTransactions = $this->hrPayrollTransactionRepository->paginate(10);

        return view('hr::payroll_transactions.index')
            ->with('hrPayrollTransactions', $hrPayrollTransactions);
    }

    /**
     * Show the form for creating a new HrPayrollTransaction.
     */
    public function create()
    {
        return view('hr::payroll_transactions.create');
    }

    /**
     * Store a newly created HrPayrollTransaction in storage.
     */
    public function store(CreateHrPayrollTransactionRequest $request)
    {
        $input = $request->all();

        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrPayrollTransactions.singular')]));

        return redirect(route('hrPayrollTransactions.index'));
    }

    /**
     * Display the specified HrPayrollTransaction.
     */
    public function show($id)
    {
        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->find($id);

        if (empty($hrPayrollTransaction)) {
            flash()->error(__('models/hrPayrollTransactions.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollTransactions.index'));
        }

        return view('hr::payroll_transactions.show')->with('hrPayrollTransaction', $hrPayrollTransaction);
    }

    /**
     * Show the form for editing the specified HrPayrollTransaction.
     */
    public function edit($id)
    {
        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->find($id);

        if (empty($hrPayrollTransaction)) {
            flash()->error(__('models/hrPayrollTransactions.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollTransactions.index'));
        }

        return view('hr::payroll_transactions.edit')->with('hrPayrollTransaction', $hrPayrollTransaction);
    }

    /**
     * Update the specified HrPayrollTransaction in storage.
     */
    public function update($id, UpdateHrPayrollTransactionRequest $request)
    {
        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->find($id);

        if (empty($hrPayrollTransaction)) {
            flash()->error(__('models/hrPayrollTransactions.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollTransactions.index'));
        }

        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrPayrollTransactions.singular')]));

        return redirect(route('hrPayrollTransactions.index'));
    }

    /**
     * Remove the specified HrPayrollTransaction from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrPayrollTransaction = $this->hrPayrollTransactionRepository->find($id);

        if (empty($hrPayrollTransaction)) {
            flash()->error(__('models/hrPayrollTransactions.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollTransactions.index'));
        }

        $this->hrPayrollTransactionRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrPayrollTransactions.singular')]));

        return redirect(route('hrPayrollTransactions.index'));
    }
}
