<?php

namespace Modules\Store\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Modules\Store\App\Http\Requests\CreateStReservationRequest;
use Modules\Store\App\Http\Requests\UpdateStReservationRequest;
use Modules\Store\App\Repositories\StReservationRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;

class StReservationController extends AppBaseController
{
    private $stReservationRepository;

    public function __construct(StReservationRepository $stReservationRepo)
    {
        $this->stReservationRepository = $stReservationRepo;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $reservations = $this->stReservationRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $stores = $this->stReservationRepository->stores();
        $statuses = \Modules\Store\App\Models\StReservation::statuses();
        return view('store::reservations.index', compact('reservations', 'stores', 'statuses'));
    }

    public function create()
    {
        $stores = $this->stReservationRepository->stores();
        $statuses = \Modules\Store\App\Models\StReservation::statuses(true);
        return view('store::reservations.create', compact('stores', 'statuses'));
    }

    public function store(CreateStReservationRequest $request)
    {
        $input = $request->all();
        try {
            $this->stReservationRepository->createReservation($input);
            flash()->success(__('messages.saved', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating') . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $reservation = $this->stReservationRepository->find($id);
        if (empty($reservation)) {
            flash()->error(__('messages.not_found', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        }
        return view('store::reservations.show')->with('reservation', $reservation);
    }

    public function edit($id)
    {
        $reservation = $this->stReservationRepository->find($id);
        if (empty($reservation)) {
            flash()->error(__('messages.not_found', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        }
        if (!$reservation->is_editable) {
            flash()->error(__('store::messages.cannot_edit_approved_record'));
            return redirect(route('store.reservation.index'));
        }
        $stores = $this->stReservationRepository->stores();
        $statuses = \Modules\Store\App\Models\StReservation::statuses(true);
        return view('store::reservations.edit', compact('reservation', 'stores', 'statuses'));
    }

    public function update($id, UpdateStReservationRequest $request)
    {
        $reservation = $this->stReservationRepository->find($id);
        if (empty($reservation)) {
            flash()->error(__('messages.not_found', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        }
        try {
            $this->stReservationRepository->updateReservation($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating') . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        $reservation = $this->stReservationRepository->find($id);
        if (empty($reservation)) {
            flash()->error(__('messages.not_found', ['model' => __('store::models/st_reservations.singular')]));
            return redirect(route('store.reservation.index'));
        }
        try {
            $this->stReservationRepository->deleteReservation($id);
            flash()->success(__('messages.deleted', ['model' => __('store::models/st_reservations.singular')]));
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
        }
        return redirect(route('store.reservation.index'));
    }

    public function authorizeReservation($id)
    {
        try {
            $this->stReservationRepository->authorizeReservation($id);
            flash()->success(__('store::models/st_reservations.messages.authorized_success') ?? 'تم الحجز بنجاح');
            return redirect()->route('store.reservation.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    public function returnToWarehouse($id)
    {
        try {
            $this->stReservationRepository->returnToWarehouse($id);
            flash()->success(__('store::models/st_reservations.messages.returned_success') ?? 'تم الإرجاع للمستودع بنجاح');
            return redirect()->route('store.reservation.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stReservationRepository->header();
        $dataExcel = $this->stReservationRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'reservations.xlsx');
    }

    public function csv()
    {
        $headers = $this->stReservationRepository->header();
        $dataExcel = $this->stReservationRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'reservations.csv');
    }

    public function pdf()
    {
        $headers = $this->stReservationRepository->header();
        $dataExcel = $this->stReservationRepository->dataExel();
        $name = $this->stReservationRepository->name();

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
