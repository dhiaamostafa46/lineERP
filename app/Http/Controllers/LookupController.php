<?php

namespace App\Http\Controllers;

use App\Models\AccuSoft\TreeAccounts;
use App\Services\ProductService;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Ø¬Ù„Ø¨ Ù‚Ø§Ø¦Ù…Ø© Ø§Ù„Ù…Ù†ØªØ¬Ø§Øª Ø£Ùˆ Ø§Ù„Ø®Ø¯Ù…Ø§Øª Ø¨Ù†Ø§Ø¡Ù‹ Ø¹Ù„Ù‰ Ø§Ù„Ù†ÙˆØ¹
     */
    public function getproducts(Request $request)
    {
        $searchType = $request->input('search_type', 'all');

        switch ($searchType) {
            case 'services': // 1 - Ø®Ø¯Ù…Ø§Øª ÙÙ‚Ø·
                $data = $this->productService->searchServices($request);
                break;
            case 'products': // 3 - Ù…Ù†ØªØ¬Ø§Øª ÙÙ‚Ø· (ÙÙŠ Ø§Ù„Ù…Ø®Ø²Ù†)
                $data = $this->productService->searchProductsOnly($request);
                break;
            case 'location': // 4 - Ù…Ù†ØªØ¬Ø§Øª Ù…ØªÙˆÙØ±Ø© ÙÙŠ Ø§Ù„ÙØ±Ø¹ Ø£Ùˆ Ø§Ù„Ù…Ø³ØªÙˆØ¯Ø¹
                $data = $this->productService->searchByBranchOrStore($request);
                break;
            case 'all': // 2 - Ù…Ù†ØªØ¬Ø§Øª ÙˆØ®Ø¯Ù…Ø§Øª Ù…Ø¹Ø§
            default:
                $data = $this->productService->searchProductsAndServices($request);
                break;
        }

        return response()->json([
            'results' => $data['results'],
        ]);
    }

    /**
     * Ø¬Ù„Ø¨ Ø´Ø¬Ø±Ø© Ø§Ù„Ø­Ø³Ø§Ø¨Ø§Øª Ù…Ø¹ Ø´Ø±Ø· Ø£Ù† ÙŠÙƒÙˆÙ† Ø§Ù„Ø­Ø³Ø§Ø¨ Ù†Ù‡Ø§Ø¦ÙŠ (is_leaf)
     */
    public function getTreeAccounts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $lang = $request->get('lang', app()->getLocale());
            $search = $request->get('search', '');
            $page = max(1, (int) $request->get('page', 1));
            $id = $request->get('id', null);
            $ids = $request->get('ids', null);
            $accountType = $request->get('account_type', null);
            $perPage = 20;

            $fetchAll = $request->has('all_accounts') && $request->get('all_accounts') == '1';

            // Store base query as reusable closure
            $baseQuery = static function () use ($lang, $fetchAll, $accountType) {
                $query = TreeAccounts::select('id', 'code', 'use_cost_center')
                    ->with([
                        'translations' => static function ($query) use ($lang) {
                            $query->select('tree_accounts_id', 'locale', 'name')->where('locale', $lang);
                        },
                    ])
                    ->where('status', 1);
                
                if (!$fetchAll) {
                    $query->where('is_leaf', true);
                }

                if ($accountType !== null) {
                    $query->where('account_type', $accountType);
                }
                
                return $query;
            };

            // Ø¬Ù„Ø¨ Ù…Ø¬Ù…ÙˆØ¹Ø© Ù…Ù† Ø§Ù„Ø­Ø³Ø§Ø¨Ø§Øª Ø¨ÙˆØ§Ø³Ø·Ø© IDs
            if ($ids && is_array($ids)) {
                $accounts = $baseQuery()
                    ->orderBy('code')
                    ->whereIn('id', $ids)->get()
                    ->map(fn ($account) => [
                        'id' => $account->id,
                        'text' => $this->formatAccountText($account),
                    ]);

                return response()->json(['results' => $accounts]);
            }

            // Ø¬Ù„Ø¨ Ø­Ø³Ø§Ø¨ ÙˆØ§Ø­Ø¯ Ù…Ø¹ÙŠÙ†
            if ($id) {
                $account = $baseQuery()->where('id', $id)->first();

                if ($account) {
                    return response()->json([
                        'results' => [
                            [
                                'id' => $account->id,
                                'text' => $this->formatAccountText($account),
                                'cost_center' => $account->use_cost_center,
                            ],
                        ],
                    ]);
                }

                return response()->json(['results' => []]);
            }

            // تطبيق فلتر البحث
            $query = $baseQuery()->orderBy('code');

            if (! empty($search)) {
                $query->where(static function ($q) use ($search, $lang) {
                    $q->where('code', 'LIKE', "{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', $search);
                    } else {
                        $q->orWhereHas('translations', static function ($query) use ($search, $lang) {
                            $query->where('locale', $lang)->where('name', 'LIKE', "%{$search}%");
                        });
                    }
                });
            }

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $accounts = $paginated->items();
            $results = collect($accounts)->map(fn ($account) => [
                'id' => $account->id,
                'text' => $this->formatAccountText($account),
                'cost_center' => $account->use_cost_center,
            ]);

            return response()->json([
                'results' => $results->values(),
                'pagination' => [
                    'more' => $paginated->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'results' => [],
                    'error' => 'Ø®Ø·Ø£ ÙÙŠ Ø¬Ù„Ø¨ Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø­Ø³Ø§Ø¨Ø§Øª: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * ØªÙ†Ø³ÙŠÙ‚ Ù†Øµ Ø¹Ø±Ø¶ Ø§Ù„Ø­Ø³Ø§Ø¨ (Ø§Ù„ÙƒÙˆØ¯ - Ø§Ù„Ø§Ø³Ù…)
     */
    private function formatAccountText($account)
    {
        $code = trim($account->code ?? '');
        $name = trim($account->translations->first()?->name ?? '');

        if (! empty($code) && ! empty($name)) {
            if (strpos($name, $code) === 0) {
                return $name;
            }

            return $code.' - '.$name;
        }

        return $code ?: ($name ?: 'Ø­Ø³Ø§Ø¨ #'.$account->id);
    }
    public function getCustomers(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $page = max(1, (int) $request->get('page', 1));
            $id = $request->get('id', null);
            $ids = $request->get('ids', null);
            $perPage = 20;

            $baseQuery = static function () {
                return \App\Models\invApp\InvCustomer::select('id', 'phone', 'status')
                    ->with('translations');
            };

            if ($ids && is_array($ids)) {
                $customers = $baseQuery()
                    ->whereIn('id', $ids)->get()
                    ->map(fn ($customer) => [
                        'id' => $customer->id,
                        'text' => $customer->name . ($customer->phone ? ' - ' . $customer->phone : ''),
                    ]);

                return response()->json(['results' => $customers]);
            }

            if ($id) {
                $customer = $baseQuery()->where('id', $id)->first();

                if ($customer) {
                    return response()->json([
                        'results' => [
                            [
                                'id' => $customer->id,
                                'text' => $customer->name . ($customer->phone ? ' - ' . $customer->phone : ''),
                            ],
                        ],
                    ]);
                }

                return response()->json(['results' => []]);
            }

            $query = $baseQuery();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('translations', function ($t) use ($search) {
                        $t->where('name', 'LIKE', "%{$search}%");
                    })->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $results = collect($paginated->items())->map(fn ($customer) => [
                'id' => $customer->id,
                'text' => $customer->name . ($customer->phone ? ' - ' . $customer->phone : ''),
            ]);

            return response()->json([
                'results' => $results->values(),
                'pagination' => [
                    'more' => $paginated->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'results' => [],
                    'error' => 'خطأ في جلب بيانات العملاء: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getSuppliers(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $page = max(1, (int) $request->get('page', 1));
            $id = $request->get('id', null);
            $ids = $request->get('ids', null);
            $perPage = 20;

            $baseQuery = static function () {
                return \App\Models\invApp\InvSupplier::select('id', 'phone', 'status')
                    ->with('translations');
            };

            if ($ids && is_array($ids)) {
                $suppliers = $baseQuery()
                    ->whereIn('id', $ids)->get()
                    ->map(fn ($supplier) => [
                        'id' => $supplier->id,
                        'text' => $supplier->name . ($supplier->phone ? ' - ' . $supplier->phone : ''),
                    ]);

                return response()->json(['results' => $suppliers]);
            }

            if ($id) {
                $supplier = $baseQuery()->where('id', $id)->first();

                if ($supplier) {
                    return response()->json([
                        'results' => [
                            [
                                'id' => $supplier->id,
                                'text' => $supplier->name . ($supplier->phone ? ' - ' . $supplier->phone : ''),
                            ],
                        ],
                    ]);
                }

                return response()->json(['results' => []]);
            }

            $query = $baseQuery();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('translations', function ($t) use ($search) {
                        $t->where('name', 'LIKE', "%{$search}%");
                    })->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $results = collect($paginated->items())->map(fn ($supplier) => [
                'id' => $supplier->id,
                'text' => $supplier->name . ($supplier->phone ? ' - ' . $supplier->phone : ''),
            ]);

            return response()->json([
                'results' => $results->values(),
                'pagination' => [
                    'more' => $paginated->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'results' => [],
                    'error' => 'خطأ في جلب بيانات الموردين: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getStores(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $page = max(1, (int) $request->get('page', 1));
            $id = $request->get('id', null);
            $ids = $request->get('ids', null);
            $perPage = 20;

            $baseQuery = static function () {
                return \App\Models\StoreApp\Store::with('translations');
            };

            if ($ids && is_array($ids)) {
                $stores = $baseQuery()
                    ->whereIn('id', $ids)->get()
                    ->map(fn ($store) => [
                        'id' => $store->id,
                        'text' => $store->name,
                    ]);

                return response()->json(['results' => $stores]);
            }

            if ($id) {
                $store = $baseQuery()->where('id', $id)->first();

                if ($store) {
                    return response()->json([
                        'results' => [
                            [
                                'id' => $store->id,
                                'text' => $store->name,
                            ],
                        ],
                    ]);
                }

                return response()->json(['results' => []]);
            }

            $query = $baseQuery();

            if (!empty($search)) {
                $query->whereHas('translations', function ($t) use ($search) {
                    $t->where('name', 'LIKE', "%{$search}%");
                });
            }

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $results = collect($paginated->items())->map(fn ($store) => [
                'id' => $store->id,
                'text' => $store->name,
            ]);

            return response()->json([
                'results' => $results->values(),
                'pagination' => [
                    'more' => $paginated->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'results' => [],
                    'error' => 'خطأ في جلب بيانات المستودعات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $page = max(1, (int) $request->get('page', 1));
            $id = $request->get('id', null);
            $ids = $request->get('ids', null);
            $perPage = 20;

            $baseQuery = static function () {
                return \App\Models\User::select('id', 'name', 'email', 'phone');
            };

            if ($ids && is_array($ids)) {
                $users = $baseQuery()
                    ->whereIn('id', $ids)->get()
                    ->map(fn ($user) => [
                        'id' => $user->id,
                        'text' => $user->name . ($user->phone ? ' - ' . $user->phone : ''),
                    ]);

                return response()->json(['results' => $users]);
            }

            if ($id) {
                $user = $baseQuery()->where('id', $id)->first();

                if ($user) {
                    return response()->json([
                        'results' => [
                            [
                                'id' => $user->id,
                                'text' => $user->name . ($user->phone ? ' - ' . $user->phone : ''),
                            ],
                        ],
                    ]);
                }

                return response()->json(['results' => []]);
            }

            $query = $baseQuery();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $results = collect($paginated->items())->map(fn ($user) => [
                'id' => $user->id,
                'text' => $user->name . ($user->phone ? ' - ' . $user->phone : ''),
            ]);

            return response()->json([
                'results' => $results->values(),
                'pagination' => [
                    'more' => $paginated->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'results' => [],
                    'error' => 'خطأ في جلب بيانات المستخدمين: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
