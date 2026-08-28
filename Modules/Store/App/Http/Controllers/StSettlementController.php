<?php

namespace Modules\Store\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Http\Requests\CreateStSettlementRequest;
use Modules\Store\App\Http\Requests\UpdateStSettlementRequest;
use Modules\Store\App\Repositories\StSettlementRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Models\StSettlement;

class StSettlementController extends AppBaseController
{
    private $stSettlementRepository;

    public function __construct(StSettlementRepository $stSettlementRepository)
    {
        $this->stSettlementRepository = $stSettlementRepository;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['settlements'] = $this->stSettlementRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stSettlementRepository->statuses();
        $data['stores'] = $this->stSettlementRepository->stores();
        return view('store::settlements.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->stSettlementRepository->statuses();
        $data['stores'] = $this->stSettlementRepository->stores();
        $data['settlement'] = null;

        if (session()->has('smart_import_settlement_data')) {
            $importData = session('smart_import_settlement_data');
            $storeId = $importData['store_id'] ?? null;

            $settlement = new StSettlement();
            $settlement->store_id = $storeId;
            $settlement->document_number = StSettlement::generateDocumentNumber();
            $settlement->document_date = now();

            $items = collect($importData['items'])->map(function ($item) use ($storeId) {
                $stock = null;
                if ($storeId) {
                    $stock = \App\Models\StoreApp\Stock::where('store_id', $storeId)
                        ->where('product_id', $item['product_id'])
                        ->where('is_size', false)
                        ->first();
                }

                $bookQty = $stock ? $stock->current_quantity : 0;
                $unitCost = ($stock && $stock->average_cost > 0) ? $stock->average_cost : $item['price'];

                $stItem = new \Modules\Store\App\Models\StSettlementItem([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'actual_quantity' => $item['qty'],
                    'unit_cost' => $unitCost,
                    'have_sizes' => false,
                ]);
                $stItem->system_quantity = $bookQty;
                return $stItem;
            });

            $settlement->items = $items;
            $data['settlement'] = $settlement;
            session()->forget('smart_import_settlement_data');
        }

        return view('store::settlements.create', $data);
    }

    public function import()
    {
        $stores = $this->stSettlementRepository->stores();
        return view('store::settlements.import', compact('stores'));
    }

    public function processSmartImport(Request $request)
    {
        try {
            $items = $request->get('items');
            $storeId = $request->get('store_id');
            $processedItems = [];
            $orgId = auth()->user()->org_id ?? null;
            $userId = auth()->id();

            foreach ($items as $item) {
                $barcode = ($item['barcode'] == '---' || empty($item['barcode'])) ? null : $item['barcode'];
                $name = trim($item['name']);

                // 1. البحث عن المنتج بالباركود أو عبر الاسم
                $product = \App\Models\BasicDataApp\Product::where('org_id', $orgId)
                    ->where(function ($q) use ($barcode, $name) {
                        if ($barcode) {
                            $q->where('barcode', $barcode);
                        }
                        $q->orWhereHas('translations', function ($query) use ($name) {
                            $query->where('name', $name);
                        });
                    })->first();

                // 2. إذا لم يوجد المنتج، قم بإنشائه فوراً
                if (!$product) {
                    $category = \App\Models\BasicDataApp\Category::where('org_id', $orgId)->first();
                    $unit = \App\Models\BasicDataApp\Unit::where('org_id', $orgId)->first();

                    $product = \App\Models\BasicDataApp\Product::create([
                        'org_id' => $orgId,
                        'user_id' => $userId,
                        'barcode' => $barcode ?: \App\Models\BasicDataApp\Product::generateUniqueBarcode(),
                        'category_id' => $category ? $category->id : 1,
                        'base_unit_id' => $unit ? $unit->id : 1,
                        'status' => 1,
                        'type' => 1,
                        'have_sizes' => false,
                        'cost_price' => $item['price'],
                        'prod_price' => $item['price'] * 1.2,
                        'ar' => ['name' => $name],
                        'en' => ['name' => $name],
                    ]);

                    \App\Models\BasicDataApp\ProductUnit::updateOrCreate(
                        ['product_id' => $product->id, 'unit_id' => $product->base_unit_id],
                        ['conversion_factor' => 1, 'is_base' => true]
                    );
                }

                $pUnit = \App\Models\BasicDataApp\ProductUnit::where('product_id', $product->id)->first();

                $processedItems[] = [
                    'product_id' => $product->id,
                    'unit_id' => $pUnit ? $pUnit->id : null,
                    'name' => $product->name,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ];
            }

            session(['smart_import_settlement_data' => [
                'store_id' => $storeId,
                'items' => $processedItems
            ]]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('store.settlement.create')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()
            ], 500);
        }
    }

    public function store(CreateStSettlementRequest $request)
    {
        try {
            $this->stSettlementRepository->createSettlement($request->all());

            flash()->success(__('messages.saved', ['model' => __('store::models/st_settlements.singular')]));
            return redirect()->route('store.settlement.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('store::models/st_settlements.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $settlement = $this->stSettlementRepository->find($id);
        if (empty($settlement)) {
            flash()->error(__('store::models/st_settlements.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.settlement.index'));
        }
        $settlement->load(['items.product', 'items.unitRelation']);
        return view('store::settlements.show', compact('settlement'));
    }

    public function edit($id)
    {
        $settlement = $this->stSettlementRepository->find($id);

        if (empty($settlement)) {
            flash()->error(__('store::models/st_settlements.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.settlement.index'));
        }

        $settlement->load(['items.product', 'items.unitRelation']);

        // جلب الكمية الدفترية الحالية لكل صنف لضمان ظهور الرصيد المحدث عند التعديل
        foreach ($settlement->items as $item) {
            $stock = \App\Models\StoreApp\Stock::where('store_id', $settlement->store_id)
                ->where('product_id', $item->product_id)
                ->where('is_size', $item->have_sizes)
                ->first();
            $item->system_quantity = $stock ? $stock->current_quantity : 0;
        }

        $data['settlement'] = $settlement;
        $data['statuses'] = $this->stSettlementRepository->statuses();
        $data['stores'] = $this->stSettlementRepository->stores();

        return view('store::settlements.edit', $data);
    }

    public function update(UpdateStSettlementRequest $request, $id)
    {
        try {
            $settlement = $this->stSettlementRepository->find($id);

            if (empty($settlement)) {
                flash()->error(__('store::models/st_settlements.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.settlement.index'));
            }

            $this->stSettlementRepository->updateSettlement($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('store::models/st_settlements.singular')]));
            return redirect()->route('store.settlement.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('store::models/st_settlements.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $settlement = $this->stSettlementRepository->find($id);

            if (empty($settlement)) {
                flash()->error(__('store::models/st_settlements.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.settlement.index'));
            }

            $this->stSettlementRepository->deleteSettlement($id);

            flash()->success(__('messages.deleted', ['model' => __('store::models/st_settlements.singular')]));
            return redirect()->route('store.settlement.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('store::models/st_settlements.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function authorizeSettlement($id)
    {
        try {
            $this->stSettlementRepository->authorizeSettlement($id);
            flash()->success('تم تعميد التسوية بنجاح');
            return redirect()->route('store.settlement.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stSettlementRepository->header();
        $dataExcel = $this->stSettlementRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'settlements.xlsx');
    }

    public function csv()
    {
        $headers = $this->stSettlementRepository->header();
        $dataExcel = $this->stSettlementRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'settlements.csv');
    }

    public function pdf()
    {
        $headers = $this->stSettlementRepository->header();
        $dataExcel = $this->stSettlementRepository->dataExel();
        $name = $this->stSettlementRepository->name();

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
