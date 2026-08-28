<?php

namespace Modules\Store\App\Http\Controllers;

use App\Helpers\StockManagementTrait;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Store\App\Repositories\StDirectTransferRepository;
use Modules\Store\App\Repositories\StDirectTransferItemRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Models\StDirectTransfer;

class StDirectTransferController extends AppBaseController
{
    use StockManagementTrait;
    private $stDirectTransferRepository;
    private $stDirectTransferItemRepository;

    public function __construct(StDirectTransferRepository $stDirectTransferRepository, StDirectTransferItemRepository $stDirectTransferItemRepository)
    {
        $this->stDirectTransferRepository = $stDirectTransferRepository;
        $this->stDirectTransferItemRepository = $stDirectTransferItemRepository;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['transfers'] = $this->stDirectTransferRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stDirectTransferRepository->statuses();
        $data['stores'] = $this->stDirectTransferRepository->stores();
        return view('store::direct_transfers.index', $data);
    }

    public function show($id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer)) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['items.product', 'items.ProductUnit', 'fromStore', 'toStore']);
        return view('store::direct_transfers.show', compact('transfer'));
    }

    public function create()
    {
        $settings = \Modules\Store\App\Models\InventorySettings::first();
        $defaultType = $settings->default_transfer_type ?? \Modules\Store\App\Models\StDirectTransfer::TYPE_DIRECT;

        $data['statuses'] = $this->stDirectTransferRepository->statuses($defaultType);
        $data['all_statuses'] = [
            \Modules\Store\App\Models\StDirectTransfer::TYPE_DIRECT => $this->stDirectTransferRepository->statuses(\Modules\Store\App\Models\StDirectTransfer::TYPE_DIRECT),
            \Modules\Store\App\Models\StDirectTransfer::TYPE_INDIRECT => $this->stDirectTransferRepository->statuses(\Modules\Store\App\Models\StDirectTransfer::TYPE_INDIRECT),
        ];
        $data['stores'] = $this->stDirectTransferRepository->stores();
        $data['default_transfer_type'] = $defaultType;
        $data['document_number'] = \Modules\Store\App\Models\StDirectTransfer::generateDocumentNumber();
        return view('store::direct_transfers.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $this->stDirectTransferRepository->createTransfer($request->all());
            flash()->success(__('messages.saved', ['model' => __('store::models/st_direct_transfers.singular')]));
            return redirect()->route('store.direct_transfer.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('store::models/st_direct_transfers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer) || !$transfer->is_editable) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found') . ' ' . __('store::messages.or_cannot_edit'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['items.product', 'items.ProductUnit']);
        $data['transfer'] = $transfer;
        $data['statuses'] = $this->stDirectTransferRepository->statuses($transfer->transfer_type, $transfer->status);
        $data['all_statuses'] = [
            \Modules\Store\App\Models\StDirectTransfer::TYPE_DIRECT => $this->stDirectTransferRepository->statuses(\Modules\Store\App\Models\StDirectTransfer::TYPE_DIRECT, $transfer->status),
            \Modules\Store\App\Models\StDirectTransfer::TYPE_INDIRECT => $this->stDirectTransferRepository->statuses(\Modules\Store\App\Models\StDirectTransfer::TYPE_INDIRECT, $transfer->status),
        ];
        $data['stores'] = $this->stDirectTransferRepository->stores();
        return view('store::direct_transfers.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $this->stDirectTransferRepository->updateTransfer($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('store::models/st_direct_transfers.singular')]));
            return redirect()->route('store.direct_transfer.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('store::models/st_direct_transfers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function checkDestinationManager(StDirectTransfer $transfer)
    {
        if (!$transfer->is_direct) {
            $destinationStore = $transfer->toStore;
            $currentUser = auth()->user();
            if ($destinationStore) {
                $managerId = $destinationStore->manager_user_id;
                if ($managerId) {
                    if ($currentUser->id !== $managerId && !$currentUser->hasRole('admin')) {
                        return __('store::messages.only_receiving_manager_can_validate');
                    }
                } else {
                    if (!$currentUser->hasRole('admin')) {
                        return __('store::messages.no_manager_assigned_to_store');
                    }
                }
            }
        }
        return null;
    }

    public function validateTransfer($id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer) || !$transfer->can_be_validated) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found') . ' ' . __('store::messages.or_cannot_edit'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['items.product', 'items.ProductUnit', 'fromStore', 'toStore']);

        $error = $this->checkDestinationManager($transfer);
        if ($error) {
            flash()->error($error);
            return redirect(route('store.direct_transfer.index'));
        }

        return view('store::direct_transfers.validate', compact('transfer'));
    }

    public function storeValidation(Request $request, $id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer) || !$transfer->can_be_validated) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found') . ' ' . __('store::messages.or_cannot_edit'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['fromStore', 'toStore']);

        $error = $this->checkDestinationManager($transfer);
        if ($error) {
            flash()->error($error);
            return redirect(route('store.direct_transfer.index'));
        }

        try {
            $this->stDirectTransferRepository->approveTransfer($id, $request->all());
            flash()->success(__('messages.updated', ['model' => __('store::models/st_direct_transfers.singular')]));
            return redirect()->route('store.direct_transfer.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function returnTransfer($id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer) || !$transfer->can_be_validated) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['items.product', 'items.ProductUnit', 'fromStore', 'toStore']);

        $error = $this->checkDestinationManager($transfer);
        if ($error) {
            flash()->error($error);
            return redirect(route('store.direct_transfer.index'));
        }

        return view('store::direct_transfers.return', compact('transfer'));
    }

    public function storeReturn(Request $request, $id)
    {
        $transfer = $this->stDirectTransferRepository->find($id);
        if (empty($transfer) || !$transfer->can_be_validated) {
            flash()->error(__('store::models/st_direct_transfers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.direct_transfer.index'));
        }
        $transfer->load(['fromStore', 'toStore']);

        $error = $this->checkDestinationManager($transfer);
        if ($error) {
            flash()->error($error);
            return redirect(route('store.direct_transfer.index'));
        }

        try {
            $input = $request->all();
            $this->stDirectTransferRepository->returnTransfer($id, $input);
            flash()->success('تمت عملية الإرجاع بنجاح');
            return redirect()->route('store.direct_transfer.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->stDirectTransferRepository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('store::models/st_direct_transfers.singular')]));
            return redirect()->route('store.direct_transfer.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('store::models/st_direct_transfers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stDirectTransferRepository->header();
        $dataExcel = $this->stDirectTransferRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'direct_transfers.xlsx');
    }

    public function csv()
    {
        $headers = $this->stDirectTransferRepository->header();
        $dataExcel = $this->stDirectTransferRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'direct_transfers.csv');
    }

    public function pdf()
    {
        $headers = $this->stDirectTransferRepository->header();
        $dataExcel = $this->stDirectTransferRepository->dataExel();
        $name = $this->stDirectTransferRepository->name();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;

        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;

        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(view('exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
