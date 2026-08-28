<?php

namespace Modules\Store\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use Illuminate\Http\Request;
use Modules\Store\App\Models\StDirectTransfer;
use Modules\Store\App\Models\StDirectTransferItem;

class StReportController extends Controller
{
    private function getCategoryIds($categoryId)
    {
        $ids = [(int) $categoryId];
        $children = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getCategoryIds($childId));
        }

        return array_unique($ids);
    }

    private function getSearchableProducts($selectedId = null)
    {
        // If we have a selected ID, we should load just that one to show it in the dropdown
        $products = [];
        if ($selectedId) {
            $isSize = str_starts_with($selectedId, 'size_');
            $actualId = (int) str_replace(['prod_', 'size_'], '', $selectedId);

            if ($isSize) {
                $size = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations'])->find($actualId);
                if ($size) {
                    $products[$selectedId] = $size->product->name.' - '.$size->name;
                }
            } else {
                $product = Product::with('translations')->find($actualId);
                if ($product) {
                    $products[$selectedId] = $product->name;
                }
            }
        }

        return collect($products)->prepend(__('lang.all'), '');
    }

    /**
     * Calculate the true weighted average cost for a product up to a given date.
     *
     * Formula: ∑(quantity × unit_cost) / ∑(quantity)
     * Only "in" movements are included (purchases, adjustments in, etc.)
     *
     * @param  string  $toDate  Y-m-d
     */
    private function calculateWeightedAverageCost(int $productId, bool $isSize, string $toDate, ?int $storeId = null): float
    {
        $moves = StockMovement::where('product_id', $productId)
            ->where('is_size', $isSize)
            ->where('stock_type', 'in')
            ->where('movement_date', '<=', $toDate)
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->get(['quantity', 'unit_cost']);

        $totalQuantity = $moves->sum('quantity');
        $totalValue = $moves->sum(fn ($m) => $m->quantity * $m->unit_cost);

        if ($totalQuantity <= 0) {
            return 0.0;
        }

        return round($totalValue / $totalQuantity, 4);
    }

    private function exportReport($exportType, $headers, $data, $name)
    {
        if ($exportType === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Store\App\Exports\StoreExport($data, $headers), $name.'.xlsx');
        } elseif ($exportType === 'csv') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Store\App\Exports\StoreExport($data, $headers), $name.'.csv', \Maatwebsite\Excel\Excel::CSV);
        } elseif ($exportType === 'pdf') {
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
            $mpdf->WriteHTML(view('exports.pdf', ['headers' => $headers, 'data' => $data, 'name' => $name]));

            return $mpdf->Output($name.'.pdf', 'I');
        }
    }

    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        return view('store::reports.index');
    }

    /*
    |--------------------------------------------------------------------------
    | 1. تقرير حركة المخزون - Stock Movement Report
    |--------------------------------------------------------------------------
    */
    public function stockMovement(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');

        $movementTypes = StockMovement::documentTypes();

        $fromDate = $request->get('fromDate', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('toDate', now()->format('Y-m-d'));

        $movements = null;
        $openingBalance = 0;

        if ($request->has('fromDate') || $request->has('toDate')) {
            $query = StockMovement::with(['product', 'productSize.product', 'store', 'unit', 'user'])->whereBetween('movement_date', [$fromDate, $toDate]);

            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('branch_id')) {
                $query->whereHas('store', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            if ($request->filled('movement_type')) {
                $query->where('movement_type', $request->movement_type);
            }

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSizeSearch = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);

                if ($isSizeSearch) {
                    $query->where('product_id', $actualId)->where('is_size', 1);
                } else {
                    $product = Product::find($actualId);
                    if ($product && $product->have_sizes) {
                        $sizeIds = $product->sizes()->pluck('id')->toArray();
                        $query->where(function ($q) use ($actualId, $sizeIds) {
                            $q->where(function ($q1) use ($actualId) {
                                $q1->where('product_id', $actualId)->where('is_size', 0);
                            })->orWhere(function ($q1) use ($sizeIds) {
                                $q1->whereIn('product_id', $sizeIds)->where('is_size', 1);
                            });
                        });
                    } else {
                        $query->where('product_id', $actualId)->where('is_size', 0);
                    }
                }
            }

            if ($request->filled('category_id')) {
                $categoryIds = $this->getCategoryIds($request->category_id);
                $query->where(function ($q) use ($categoryIds) {
                    $q->where(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    })->orWhere(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    });
                });
            }

            // Calculate Opening Balance before $fromDate using SQL SUM for efficiency
            $openingBalanceQuery = StockMovement::query();
            if ($request->filled('store_id')) {
                $openingBalanceQuery->where('store_id', $request->store_id);
            }
            if ($request->filled('branch_id')) {
                $openingBalanceQuery->whereHas('store', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }
            if ($request->filled('movement_type')) {
                $openingBalanceQuery->where('movement_type', $request->movement_type);
            }
            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSizeSearch = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                if ($isSizeSearch) {
                    $openingBalanceQuery->where('product_id', $actualId)->where('is_size', 1);
                } else {
                    $product = Product::find($actualId);
                    if ($product && $product->have_sizes) {
                        $sizeIds = $product->sizes()->pluck('id')->toArray();
                        $openingBalanceQuery->where(function ($q) use ($actualId, $sizeIds) {
                            $q->where(function ($q1) use ($actualId) {
                                $q1->where('product_id', $actualId)->where('is_size', 0);
                            })->orWhere(function ($q1) use ($sizeIds) {
                                $q1->whereIn('product_id', $sizeIds)->where('is_size', 1);
                            });
                        });
                    } else {
                        $openingBalanceQuery->where('product_id', $actualId)->where('is_size', 0);
                    }
                }
            }
            if ($request->filled('category_id')) {
                $categoryIds = $this->getCategoryIds($request->category_id);
                $openingBalanceQuery->where(function ($q) use ($categoryIds) {
                    $q->where(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    })->orWhere(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    });
                });
            }

            $openingBalance = $openingBalanceQuery->where('movement_date', '<', $fromDate)->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as balance')->value('balance') ?? 0;

            if ($request->filled('export')) {
                $movements = $query->orderBy('movement_date', 'asc')->orderBy('created_at', 'asc')->get();
            } else {
                $movements = $query->orderBy('movement_date', 'asc')->orderBy('created_at', 'asc')->paginate(100);
            }

            // Calculate total balance of movements before the current page's first item
            // to maintain a correct running balance across pages.
            $previousPagesBalance = 0;
            if (! $request->filled('export') && $movements->currentPage() > 1) {
                $pId = $request->product_id;
                $isSizeSearch = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);

                // Get movements that would have appeared on previous pages
                $prevQuery = StockMovement::whereBetween('movement_date', [$fromDate, $toDate]);
                // ... apply same filters as $query ...
                // This is getting complex, maybe just calculate the balance of all items before the first ID on this page

                // Simpler approach: Sum all movements from $fromDate until the first movement of this page
                $firstMovement = $movements->first();
                if ($firstMovement) {
                    $prevQuery = clone $query;
                    // Remove pagination and just sum
                    $previousPagesBalance =
                        $prevQuery
                            ->where(function ($q) use ($firstMovement) {
                                $q->where('movement_date', '<', $firstMovement->movement_date)->orWhere(function ($q2) use ($firstMovement) {
                                    $q2->where('movement_date', $firstMovement->movement_date)->where('created_at', '<', $firstMovement->created_at);
                                });
                            })
                            ->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as balance')
                            ->value('balance') ?? 0;
                }
            }

            $currentBalance = $openingBalance + $previousPagesBalance;
            foreach ($movements as $mv) {
                if ($mv->stock_type === 'in') {
                    $currentBalance += $mv->quantity;
                } else {
                    $currentBalance -= $mv->quantity;
                }
                $mv->running_balance = $currentBalance;
            }

            if ($request->filled('export')) {
                $headers = [
                    __('store::models/st_reports.columns.id'),
                    __('store::models/st_reports.columns.movement_number'),
                    __('store::models/st_reports.columns.date'),
                    __('store::models/st_reports.columns.movement_type'),
                    __('store::models/st_reports.columns.category'),
                    __('store::models/st_reports.columns.product'),
                    __('store::models/st_reports.columns.store'),
                    __('store::models/st_reports.columns.quantity'),
                    __('store::models/st_reports.columns.balance'),
                    __('store::models/st_reports.columns.unit'),
                    __('store::models/st_reports.columns.unit_cost'),
                    __('store::models/st_reports.columns.total_cost'),
                    __('store::models/st_reports.columns.reference'),
                ];
                $dataExcel = [];
                $dataExcel[] = [
                    '-',
                    __('store::models/st_reports.columns.opening_balance'),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    number_format($openingBalance, 2),
                    '',
                    '',
                    '',
                    '',
                ];
                $index = 1;
                foreach ($movements as $mv) {
                    $dataExcel[] = [
                        $index++,
                        $mv->movement_number,
                        $mv->movement_date?->format('Y-m-d'),
                        $mv->movement_type_name,
                        $mv->is_size ? $mv->productSize?->product?->category?->name : $mv->product?->category?->name,
                        $mv->product_name,
                        $mv->store?->name,
                        ($mv->stock_type === 'in' ? '+' : '-').abs($mv->quantity),
                        number_format($mv->running_balance, 2),
                        $mv->unit?->name,
                        number_format($mv->unit_cost, 2),
                        number_format($mv->total_cost, 2),
                        $mv->reference_number,
                    ];
                }

                return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.stock_movement'));
            }
        }

        return view('store::reports.stockMovement.index', compact('stores', 'branches', 'products', 'categories', 'movements', 'fromDate', 'toDate', 'movementTypes', 'openingBalance'))->with('product_id', $request->product_id);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. تقرير رصيد المخزون - Stock Balance Report
    |--------------------------------------------------------------------------
    */
    public function stockBalance(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');

        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate', now()->format('Y-m-d'));
        $storeId = $request->get('store_id');

        $stocks = null;

        if ($request->has('search')) {
            $query = StockMovement::query()->where('movement_date', '<=', $toDate);

            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            if ($request->filled('branch_id')) {
                $query->whereHas('store', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSize = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                $query->where('product_id', $actualId)->where('is_size', $isSize ? 1 : 0);
            }

            if ($request->filled('category_id')) {
                $categoryIds = $this->getCategoryIds($request->category_id);
                $query->where(function ($q) use ($categoryIds) {
                    $q->where(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    })->orWhere(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    });
                });
            }

            $stockStatus = $request->get('stock_status', 'all');
            if ($stockStatus === 'without_zero') {
                $query->havingRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) != 0');
            } elseif ($stockStatus === 'zero_only') {
                $query->havingRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) = 0');
            }

            // Aggregation using CASE WHEN for Opening, In, and Out
            if ($request->filled('export')) {
                $stocks = $query
                    ->select('product_id', 'is_size')
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NOT NULL AND movement_date < ? THEN (CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END)
                        ELSE 0 END) as opening_quantity',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NULL OR movement_date >= ? THEN (CASE WHEN stock_type = "in" THEN quantity ELSE 0 END)
                        ELSE 0 END) as qty_in',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NULL OR movement_date >= ? THEN (CASE WHEN stock_type = "out" THEN quantity ELSE 0 END)
                        ELSE 0 END) as qty_out',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')
                    ->groupBy('product_id', 'is_size')
                    ->get();

                $stocks->transform(function ($row) use ($toDate, $storeId) {
                    // Fetch basic info
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                        $row->min_quantity = 0;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                        $row->min_quantity = $item?->min_quantity ?? 0;
                    }

                    $row->store = $storeId ? Store::find($storeId) : (object) ['name' => __('lang.all')];
                    $row->opening_qty = $row->opening_quantity;

                    // Get average cost at that date
                    $latestMove = StockMovement::where('product_id', $row->product_id)->where('is_size', $row->is_size)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->where('movement_date', '<=', $toDate)->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc')->first();

                    $row->average_cost = $latestMove ? $latestMove->unit_cost : 0;

                    return $row;
                });

                $headers = [
                    __('store::models/st_reports.columns.id'),
                    __('store::models/st_reports.columns.category'),
                    __('store::models/st_reports.columns.product'),
                    __('store::models/st_reports.columns.store'),
                    __('store::models/st_reports.columns.opening_quantity'),
                    __('store::models/st_reports.columns.qty_in'),
                    __('store::models/st_reports.columns.qty_out'),
                    __('store::models/st_reports.columns.current_quantity'),
                    __('store::models/st_reports.columns.unit'),
                    __('store::models/st_reports.columns.average_cost'),
                    __('store::models/st_reports.columns.total_value'),
                ];
                $dataExcel = [];
                $index = 1;
                foreach ($stocks as $stock) {
                    $dataExcel[] = [
                        $index++,
                        $stock->category_name,
                        $stock->product_name,
                        $stock->store?->name,
                        number_format($stock->opening_qty, 2),
                        number_format($stock->qty_in, 2),
                        number_format($stock->qty_out, 2),
                        number_format($stock->current_quantity, 2),
                        $stock->unit?->name,
                        number_format($stock->average_cost, 2),
                        number_format($stock->current_quantity * $stock->average_cost, 2),
                    ];
                }

                return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.stock_balance'));
            } else {
                $stocks = $query
                    ->select('product_id', 'is_size')
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NOT NULL AND movement_date < ? THEN (CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END)
                        ELSE 0 END) as opening_quantity',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NULL OR movement_date >= ? THEN (CASE WHEN stock_type = "in" THEN quantity ELSE 0 END)
                        ELSE 0 END) as qty_in',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw(
                        'SUM(CASE
                        WHEN ? IS NULL OR movement_date >= ? THEN (CASE WHEN stock_type = "out" THEN quantity ELSE 0 END)
                        ELSE 0 END) as qty_out',
                        [$fromDate, $fromDate],
                    )
                    ->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')
                    ->groupBy('product_id', 'is_size')
                    ->paginate(50);

                $stocks->getCollection()->transform(function ($row) use ($toDate, $storeId) {
                    // Fetch basic info
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                        $row->min_quantity = 0;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                        $row->min_quantity = $item?->min_quantity ?? 0;
                    }

                    $row->store = $storeId ? Store::find($storeId) : (object) ['name' => __('lang.all')];
                    $row->opening_qty = $row->opening_quantity;

                    // Calculate true weighted average cost: ∑(qty × cost) / ∑(qty)
                    $row->average_cost = $this->calculateWeightedAverageCost(
                        $row->product_id,
                        (bool) $row->is_size,
                        $toDate,
                        $storeId,
                    );

                    return $row;
                });
            }
        }

        return view('store::reports.stockBalance.index', compact('stores', 'branches', 'products', 'categories', 'stocks', 'fromDate', 'toDate'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. تقرير تقييم المخزون - Inventory Valuation Report
    |--------------------------------------------------------------------------
    */
    public function inventoryValuation(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $toDate = $request->get('toDate', now()->format('Y-m-d'));
        $storeId = $request->get('store_id');

        $data = null;

        if ($request->has('search')) {
            $query = StockMovement::query()->where('movement_date', '<=', $toDate);

            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            if ($request->filled('branch_id')) {
                $query->whereHas('store', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSize = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                $query->where('product_id', $actualId)->where('is_size', $isSize ? 1 : 0);
            }

            if ($request->filled('category_id')) {
                $categoryIds = $this->getCategoryIds($request->category_id);
                $query->where(function ($q) use ($categoryIds) {
                    $q->where(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    })->orWhere(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    });
                });
            }

            if ($request->filled('export')) {
                $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->having('current_quantity', '!=', 0)->get();

                $aggregatedData->transform(function ($row) use ($toDate, $storeId) {
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                    }

                    // Calculate true weighted average cost: ∑(qty × cost) / ∑(qty)
                    $row->average_cost = $this->calculateWeightedAverageCost(
                        $row->product_id,
                        (bool) $row->is_size,
                        $toDate,
                        $storeId,
                    );
                    $row->total_value = $row->current_quantity * $row->average_cost;
                    $row->store = $storeId ? Store::find($storeId) : (object) ['name' => __('lang.all')];

                    return $row;
                });

                $headers = [
                    __('store::models/st_reports.columns.id'),
                    __('store::models/st_reports.columns.category'),
                    __('store::models/st_reports.columns.product'),
                    __('store::models/st_reports.columns.store'),
                    __('store::models/st_reports.columns.current_quantity'),
                    __('store::models/st_reports.columns.unit'),
                    __('store::models/st_reports.columns.average_cost'),
                    __('store::models/st_reports.columns.total_value'),
                ];
                $dataExcel = [];
                $index = 1;
                foreach ($aggregatedData as $stock) {
                    $dataExcel[] = [
                        $index++,
                        $stock->category_name,
                        $stock->product_name,
                        $stock->store?->name,
                        number_format($stock->current_quantity, 2),
                        $stock->unit?->name,
                        number_format($stock->average_cost, 2),
                        number_format($stock->total_value, 2),
                    ];
                }

                return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.inventory_valuation'));
            } else {
                $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->having('current_quantity', '!=', 0)->paginate(50);

                $aggregatedData->getCollection()->transform(function ($row) use ($toDate, $storeId) {
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                    }

                    // Calculate true weighted average cost: ∑(qty × cost) / ∑(qty)
                    $row->average_cost = $this->calculateWeightedAverageCost(
                        $row->product_id,
                        (bool) $row->is_size,
                        $toDate,
                        $storeId,
                    );
                    $row->total_value = $row->current_quantity * $row->average_cost;
                    $row->store = $storeId ? Store::find($storeId) : (object) ['name' => __('lang.all')];

                    return $row;
                });
            }

            $totalValue = $aggregatedData->sum('total_value');
            $totalQty = $aggregatedData->sum('current_quantity');

            $data = [
                'stocks' => $aggregatedData,
                'total_value' => number_format($totalValue, 2),
                'total_qty' => number_format($totalQty, 4),
                'store_name' => $storeId ? Store::find($storeId)?->name : __('lang.all'),
                'toDate' => $toDate,
            ];
        }

        return view('store::reports.inventoryValuation.index', compact('stores', 'branches', 'products', 'categories', 'data', 'toDate'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. تقرير المخزون المنخفض - Low Stock Report
    |--------------------------------------------------------------------------
    */
    public function lowStock(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $toDate = $request->get('toDate', now()->format('Y-m-d'));
        $storeId = $request->get('store_id');

        $query = StockMovement::query()->where('movement_date', '<=', $toDate);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('product_id')) {
            $pId = $request->product_id;
            $isSize = str_starts_with($pId, 'size_');
            $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
            $query->where('product_id', $actualId)->where('is_size', $isSize ? 1 : 0);
        }

        if ($request->filled('category_id')) {
            $categoryIds = $this->getCategoryIds($request->category_id);
            $query->where(function ($q) use ($categoryIds) {
                $q->where(function ($q1) use ($categoryIds) {
                    $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                })->orWhere(function ($q1) use ($categoryIds) {
                    $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                });
            });
        }

        // Aggregate current quantity
        if ($request->filled('export')) {
            $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->get();

            $aggregatedData->transform(function ($row) use ($storeId) {
                if ($row->is_size) {
                    $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                    $row->product_name = $item?->product?->name.' - '.$item?->name;
                    $row->category_name = $item?->product?->category?->name;
                    $row->unit = $item?->product?->units->first()?->unit;
                    $row->min_quantity = 0;
                } else {
                    $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                    $row->product_name = $item?->name;
                    $row->category_name = $item?->category?->name;
                    $row->unit = $item?->units->first()?->unit;
                    $row->min_quantity = $item?->min_quantity ?? 0;
                }

                $stockRecord = Stock::where('product_id', $row->product_id)->where('is_size', $row->is_size)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->first();

                $row->actual_min_qty = $stockRecord?->min_quantity ?? $row->min_quantity;
                $row->reorder_point = $stockRecord?->reorder_point ?? 0;
                $row->store = $storeId ? Store::find($storeId) : null;

                return $row;
            });

            // Filter out items that are NOT low stock in PHP after enrichment
            $filtered = $aggregatedData->filter(function ($row) {
                return $row->current_quantity < $row->actual_min_qty;
            });

            $headers = [
                __('store::models/st_reports.columns.id'),
                __('store::models/st_reports.columns.category'),
                __('store::models/st_reports.columns.product'),
                __('store::models/st_reports.columns.store'),
                __('store::models/st_reports.columns.current_quantity'),
                __('store::models/st_reports.columns.min_quantity'),
                __('store::models/st_reports.columns.reorder_point'),
                __('store::models/st_reports.columns.unit'),
            ];
            $dataExcel = [];
            $index = 1;
            foreach ($filtered as $stock) {
                $dataExcel[] = [
                    $index++,
                    $stock->category_name,
                    $stock->product_name,
                    $stock->store?->name ?? __('lang.all'),
                    number_format($stock->current_quantity, 2),
                    number_format($stock->actual_min_qty, 2),
                    number_format($stock->reorder_point, 2),
                    $stock->unit?->name,
                ];
            }

            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.low_stock'));
        } else {
            $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->paginate(50);

            $aggregatedData->getCollection()->transform(function ($row) use ($storeId) {
                if ($row->is_size) {
                    $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                    $row->product_name = $item?->product?->name.' - '.$item?->name;
                    $row->category_name = $item?->product?->category?->name;
                    $row->unit = $item?->product?->units->first()?->unit;
                    $row->min_quantity = 0;
                } else {
                    $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                    $row->product_name = $item?->name;
                    $row->category_name = $item?->category?->name;
                    $row->unit = $item?->units->first()?->unit;
                    $row->min_quantity = $item?->min_quantity ?? 0;
                }

                $stockRecord = Stock::where('product_id', $row->product_id)->where('is_size', $row->is_size)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->first();

                $row->actual_min_qty = $stockRecord?->min_quantity ?? $row->min_quantity;
                $row->reorder_point = $stockRecord?->reorder_point ?? 0;
                $row->store = $storeId ? Store::find($storeId) : null;

                return $row;
            });
        }

        // Filter out items that are NOT low stock in PHP after enrichment (since min_qty can vary)
        // Note: For better performance, min_qty should ideally be in stock_movements or easily joinable.
        // But with pagination, we'll just show the enriched results for the current page.
        $stocks = $aggregatedData;

        return view('store::reports.lowStock.index', compact('stores', 'branches', 'products', 'categories', 'stocks', 'toDate'));
    }

    public function inventoryCount(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $toDate = $request->get('toDate', now()->format('Y-m-d'));
        $storeId = $request->get('store_id');

        $stocks = null;

        if ($request->has('search')) {
            $query = StockMovement::query()->where('movement_date', '<=', $toDate);

            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            if ($request->filled('branch_id')) {
                $query->whereHas('store', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSize = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                $query->where('product_id', $actualId)->where('is_size', $isSize ? 1 : 0);
            }

            if ($request->filled('category_id')) {
                $categoryIds = $this->getCategoryIds($request->category_id);
                $query->where(function ($q) use ($categoryIds) {
                    $q->where(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 0)->whereHas('product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    })->orWhere(function ($q1) use ($categoryIds) {
                        $q1->where('is_size', 1)->whereHas('productSize.product', fn ($q2) => $q2->whereIn('category_id', $categoryIds));
                    });
                });
            }

            if ($request->filled('export')) {
                $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->get();

                $aggregatedData->transform(function ($row) use ($toDate, $storeId) {
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                    }

                    $row->average_cost = $this->calculateWeightedAverageCost(
                        $row->product_id,
                        (bool) $row->is_size,
                        $toDate,
                        $storeId,
                    );
                    $latestMoveDate = StockMovement::where('product_id', $row->product_id)->where('is_size', $row->is_size)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->where('movement_date', '<=', $toDate)->max('movement_date');
                    $row->last_movement_at = $latestMoveDate ? \Illuminate\Support\Carbon::parse($latestMoveDate) : null;
                    $row->store = $storeId ? Store::find($storeId) : null;
                    $row->reserved_quantity = 0;

                    return $row;
                });

                $headers = [
                    __('store::models/st_reports.columns.id'),
                    __('store::models/st_reports.columns.category'),
                    __('store::models/st_reports.columns.product'),
                    __('store::models/st_reports.columns.store'),
                    __('store::models/st_reports.columns.book_quantity'),
                    __('store::models/st_reports.columns.actual_quantity'),
                    __('store::models/st_reports.columns.difference'),
                    __('store::models/st_reports.columns.unit'),
                    __('store::models/st_reports.columns.last_movement'),
                ];
                $dataExcel = [];
                $index = 1;
                foreach ($aggregatedData as $stock) {
                    $dataExcel[] = [
                        $index++,
                        $stock->category_name,
                        $stock->product_name,
                        $stock->store?->name ?? __('lang.all'),
                        number_format($stock->current_quantity, 2),
                        '________',
                        '________',
                        $stock->unit?->name,
                        $stock->last_movement_at ? $stock->last_movement_at->format('Y-m-d') : '-',
                    ];
                }

                return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.inventory_count'));
            } else {
                $aggregatedData = $query->select('product_id', 'is_size')->selectRaw('SUM(CASE WHEN stock_type = "in" THEN quantity ELSE -quantity END) as current_quantity')->groupBy('product_id', 'is_size')->paginate(50);

                $aggregatedData->getCollection()->transform(function ($row) use ($toDate, $storeId) {
                    if ($row->is_size) {
                        $item = \App\Models\BasicDataApp\ProductSize::with(['translations', 'product.translations', 'product.category.translations', 'product.units.unit'])->find($row->product_id);
                        $row->product_name = $item?->product?->name.' - '.$item?->name;
                        $row->category_name = $item?->product?->category?->name;
                        $row->unit = $item?->product?->units->first()?->unit;
                    } else {
                        $item = Product::with(['translations', 'category.translations', 'units.unit'])->find($row->product_id);
                        $row->product_name = $item?->name;
                        $row->category_name = $item?->category?->name;
                        $row->unit = $item?->units->first()?->unit;
                    }

                    $row->average_cost = $this->calculateWeightedAverageCost(
                        $row->product_id,
                        (bool) $row->is_size,
                        $toDate,
                        $storeId,
                    );
                    $latestMoveDate = StockMovement::where('product_id', $row->product_id)->where('is_size', $row->is_size)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->where('movement_date', '<=', $toDate)->max('movement_date');
                    $row->last_movement_at = $latestMoveDate ? \Illuminate\Support\Carbon::parse($latestMoveDate) : null;
                    $row->store = $storeId ? Store::find($storeId) : null;
                    $row->reserved_quantity = 0;

                    return $row;
                });
            }

            $stocks = $aggregatedData;
        }

        return view('store::reports.inventoryCount.index', compact('stores', 'branches', 'products', 'categories', 'stocks', 'toDate'));
    }

    // -----------------------------------------------------------------------
    // Resource methods (required by Route::resource)
    // -----------------------------------------------------------------------

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }

    public function pendingStock(Request $request)
    {
        $stores = Store::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $branches = \App\Models\Branch::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');
        $products = $this->getSearchableProducts($request->product_id);
        $categories = Category::with('translations')->activeOnly()->get()->pluck('name', 'id')->prepend(__('lang.all'), '');

        $toDate = $request->get('toDate', now()->format('Y-m-d'));
        $fromStoreId = $request->get('from_store_id');
        $toStoreId = $request->get('to_store_id');

        $stocks = null;
        $reservations = null;

        if ($request->has('search')) {
            // 1. Direct Transfers Pending
            $query = StDirectTransferItem::whereHas('directTransfer', function ($q) use ($toDate, $fromStoreId, $toStoreId) {
                $q->whereIn('status', [StDirectTransfer::STATUS_SOURCE_APPROVED, StDirectTransfer::STATUS_DESTINATION_DRAFT, StDirectTransfer::STATUS_PARTIAL_APPROVED])->where('document_date', '<=', $toDate);

                if ($fromStoreId) {
                    $q->where('from_store_id', $fromStoreId);
                }
                if (request()->filled('branch_id')) {
                    $q->whereHas('fromStore', function ($q2) {
                        $q2->where('branch_id', request()->branch_id);
                    });
                }
                if ($toStoreId) {
                    $q->where('to_store_id', $toStoreId);
                }
            });

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSize = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                $query->where('product_id', $actualId)->where('have_sizes', $isSize ? 1 : 0);
            }

            $stocksData = $query->with([
                'directTransfer.fromStore',
                'directTransfer.toStore',
                'product.translations',
                'productSize.product.translations',
                'productSize.translations',
                'product.category.translations',
                'ProductUnit.unit.translations',
            ])->get();

            $stocksData->transform(function ($row) {
                if ($row->have_sizes) {
                    $row->product_name = $row->productSize?->product?->name.' - '.$row->productSize?->name;
                    $row->category_name = $row->productSize?->product?->category?->name;
                } else {
                    $row->product_name = $row->product?->name;
                    $row->category_name = $row->product?->category?->name;
                }
                $row->unit_name = $row->unit_name;
                $row->pending_qty = $row->quantity - ($row->received_quantity ?? 0);

                $row->report_type = __('store::models/st_direct_transfers.singular');
                $row->source_store = $row->directTransfer?->fromStore?->name;
                $row->destination_store = $row->directTransfer?->toStore?->name;
                $row->doc_number = $row->directTransfer?->document_number;
                $row->doc_date = $row->directTransfer?->document_date;
                $row->status_label = $row->directTransfer?->status_text;

                return $row;
            });

            // 2. Reservations Pending (Reserved but not returned)
            $resQuery = \Modules\Store\App\Models\StReservationItem::whereHas('reservation', function ($q) use ($toDate, $fromStoreId) {
                $q->where('status', \Modules\Store\App\Models\StReservation::STATUS_APPROVED)->where('document_date', '<=', $toDate);
                if ($fromStoreId) {
                    $q->where('store_id', $fromStoreId);
                }
                if (request()->filled('branch_id')) {
                    $q->whereHas('store', function ($q2) {
                        $q2->where('branch_id', request()->branch_id);
                    });
                }
            });

            if ($request->filled('product_id')) {
                $pId = $request->product_id;
                $isSize = str_starts_with($pId, 'size_');
                $actualId = (int) str_replace(['prod_', 'size_'], '', $pId);
                $resQuery->where('product_id', $actualId)->where('have_sizes', $isSize ? 1 : 0);
            }

            $reservationsData = $resQuery->with([
                'reservation.store',
                'product.translations',
                'productSize.product.translations',
                'productSize.translations',
                'product.category.translations',
                'ProductUnit.unit.translations',
            ])->get();

            $reservationsData->transform(function ($row) {
                if ($row->have_sizes) {
                    $row->product_name = $row->productSize?->product?->name.' - '.$row->productSize?->name;
                    $row->category_name = $row->productSize?->product?->category?->name;
                } else {
                    $row->product_name = $row->product?->name;
                    $row->category_name = $row->product?->category?->name;
                }
                $row->unit_name = $row->unit_name;
                $row->pending_qty = $row->quantity;

                $row->report_type = __('store::models/st_reservations.singular');
                $row->source_store = $row->reservation?->store?->name;
                $row->destination_store = '-';
                $row->doc_number = $row->reservation?->document_number;
                $row->doc_date = $row->reservation?->document_date;
                $row->status_label = $row->reservation?->status_text;

                return $row;
            });

            $combinedData = $stocksData->concat($reservationsData)->sortByDesc('doc_date');

            if ($request->filled('export')) {
                $headers = [
                    __('store::models/st_reports.columns.id'),
                    __('lang.type'),
                    __('store::models/st_reports.columns.movement_number'),
                    __('store::models/st_reports.columns.date'),
                    __('store::models/st_reports.columns.product'),
                    __('store::models/st_reports.columns.from_store'),
                    __('store::models/st_reports.columns.to_store'),
                    __('store::models/st_reports.columns.quantity'),
                    __('store::models/st_reports.columns.pending_quantity'),
                    __('store::models/st_reports.columns.unit'),
                    __('store::models/st_reports.columns.status'),
                ];
                $dataExcel = [];
                $index = 1;
                foreach ($combinedData as $item) {
                    $dataExcel[] = [
                        $index++,
                        $item->report_type,
                        $item->doc_number,
                        $item->doc_date?->format('Y-m-d'),
                        $item->product_name.' ('.$item->category_name.')',
                        $item->source_store,
                        $item->destination_store,
                        number_format($item->quantity, 2),
                        number_format($item->pending_qty, 2),
                        $item->unit_name,
                        $item->status_label,
                    ];
                }

                return $this->exportReport($request->input('export'), $headers, $dataExcel, __('store::models/st_reports.types.pending_stock'));
            } else {
                // Manual pagination for combined results
                $perPage = 50;
                $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
                $currentItems = $combinedData->slice(($currentPage - 1) * $perPage, $perPage)->all();
                $stocks = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $combinedData->count(), $perPage, $currentPage, [
                    'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                    'query' => $request->query(),
                ]);
            }
        }

        return view('store::reports.pendingStock.index', compact('stores', 'branches', 'products', 'categories', 'stocks', 'toDate'));
    }
}
