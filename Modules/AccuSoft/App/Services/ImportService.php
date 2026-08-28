<?php

namespace Modules\AccuSoft\App\Services;

use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportService
{
    /**
     * Parse raw content from various sources and formats.
     */
    public function parse($source, $format, $options = [])
    {
        $rawRows = [];

        if ($format === 'excel' || $format === 'csv') {
            if ($source instanceof \Illuminate\Http\UploadedFile) {
                $filePath = $source->getRealPath();
            } else {
                $filePath = $source;
            }

            // Using Maatwebsite Excel to load file content as array
            try {
                $importClass = new class implements \Maatwebsite\Excel\Concerns\ToArray
                {
                    public function array(array $array)
                    {
                        return $array;
                    }
                };
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray($importClass, $filePath);

                // Get first sheet data
                $sheetData = $sheets[0] ?? [];
                if (! empty($sheetData)) {
                    $headers = array_shift($sheetData);
                    foreach ($sheetData as $row) {
                        $mappedRow = [];
                        foreach ($headers as $index => $header) {
                            if ($header !== null && $header !== '') {
                                $mappedRow[$header] = $row[$index] ?? null;
                            }
                        }
                        $rawRows[] = $mappedRow;
                    }
                }
            } catch (Exception $e) {
                Log::error('Excel Parsing Error: '.$e->getMessage());
                throw new Exception('فشل في قراءة ملف الإكسيل/CSV: '.$e->getMessage());
            }
        } elseif ($format === 'json') {
            $content = $source;
            if (filter_var($source, FILTER_VALIDATE_URL)) {
                $content = $this->fetchFromUrl($source);
            }
            $decoded = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('ملف JSON غير صالح.');
            }
            $rawRows = $this->flattenJson($decoded);
        } elseif ($format === 'xml') {
            $content = $source;
            if (filter_var($source, FILTER_VALIDATE_URL)) {
                $content = $this->fetchFromUrl($source);
            }
            $rawRows = $this->parseXml($content);
        } elseif ($format === 'manual_csv') {
            $rawRows = $this->parseCsvString($source);
        } elseif ($format === 'manual_json') {
            $decoded = json_decode($source, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('النص المدخل لا يحتوي على JSON صالح.');
            }
            $rawRows = $this->flattenJson($decoded);
        }

        return $this->detectAndMapColumns($rawRows);
    }

    public function normalize(array $rows)
    {
        $normalized = [];
        $tempIdCounter = 1;

        // Dynamic Account Type and Nature Mapping based on translation files
        $originalLocale = app()->getLocale();
        $accountTypeMap = [];
        $typeMap = [];

        // Prepopulate basic en/ar keys
        $staticAccountTypeMap = [
            'asset' => TreeAccounts::ACCOUNT_TYPE_ASSET,
            'liability' => TreeAccounts::ACCOUNT_TYPE_LIABILITY,
            'equity' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
            'revenue' => TreeAccounts::ACCOUNT_TYPE_REVENUE,
            'expense' => TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            'cost_of_sales' => TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES,
            'suppliers' => TreeAccounts::ACCOUNT_TYPE_SUPPLIERS,
            'treasury' => TreeAccounts::ACCOUNT_TYPE_TREASURY,
            'bank' => TreeAccounts::ACCOUNT_TYPE_BANK,
            'inventory' => TreeAccounts::ACCOUNT_TYPE_INVENTORY,
            'customers' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
            'sales' => TreeAccounts::ACCOUNT_TYPE_SALES,
            'purchases' => TreeAccounts::ACCOUNT_TYPE_PURCHASES,
        ];
        foreach ($staticAccountTypeMap as $key => $val) {
            $accountTypeMap[$key] = $val;
        }

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);

            foreach (TreeAccounts::accountTypes() as $id => $label) {
                if (empty($label)) {
                    continue;
                }
                $labelLower = mb_strtolower(trim($label), 'UTF-8');
                $labelNormalized = $this->normalizeArabicString($label);

                $accountTypeMap[$labelLower] = $id;
                $accountTypeMap[$labelNormalized] = $id;
            }

            foreach (TreeAccounts::types() as $id => $label) {
                if (empty($label)) {
                    continue;
                }
                $labelLower = mb_strtolower(trim($label), 'UTF-8');
                $labelNormalized = $this->normalizeArabicString($label);

                $typeMap[$labelLower] = $id;
                $typeMap[$labelNormalized] = $id;
            }
        }
        app()->setLocale($originalLocale);

        // 1. Initial pass to create normalized items with temp IDs
        foreach ($rows as $row) {
            $nameAr = trim($row['name_ar'] ?? '');
            $nameEn = trim($row['name_en'] ?? '');

            if (empty($nameAr)) {
                continue; // Skip completely empty rows
            }

            // Detect account type
            $rawAccountType = mb_strtolower(trim($row['account_type'] ?? ''), 'UTF-8');
            $accountType = null;
            if (is_numeric($rawAccountType)) {
                $accountType = (int) $rawAccountType;
            } else {
                $normAccountType = $this->normalizeArabicString($rawAccountType);
                $accountType = $accountTypeMap[$rawAccountType] ?? $accountTypeMap[$normAccountType] ?? null;
            }

            // Detect Nature (Debit/Credit)
            $rawType = mb_strtolower(trim($row['type'] ?? ''), 'UTF-8');
            $type = null;
            if (is_numeric($rawType)) {
                $type = (int) $rawType;
            } else {
                $normType = $this->normalizeArabicString($rawType);
                if ($rawType === 'debit' || $normType === 'مدين') {
                    $type = TreeAccounts::TYPE_DEBIT;
                } elseif ($rawType === 'credit' || $normType === 'دائن') {
                    $type = TreeAccounts::TYPE_CREDIT;
                } else {
                    $type = $typeMap[$rawType] ?? $typeMap[$normType] ?? null;
                }
            }

            $normalized[] = [
                'temp_id' => 'temp_'.$tempIdCounter++,
                'excel_code' => $row['excel_code'] ?? '',
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'account_type' => $accountType,
                'type' => $type,
                'parent_code' => trim($row['parent_code'] ?? ''),
                'parent_name' => trim($row['parent_name'] ?? ''),
                'parent_temp_id' => null,
                'parent_id' => null,
                'level' => 1,
                'code' => null,
                'errors' => [],
            ];
        }

        // 2. Resolve hierarchy and detect existing accounts
        $existingAccounts = TreeAccounts::with('translations')->get();

        foreach ($normalized as &$item) {
            $item['already_exists'] = false;
            
            // First, check if this item ALREADY EXISTS in the DB exactly!
            $normalizedNameAr = $this->normalizeArabicString($item['name_ar']);
            $matchedDbAccount = null;
            if ($item['account_type']) {
                foreach ($existingAccounts as $dbAccount) {
                    $dbAr = $dbAccount->translate('ar')?->name ?? '';
                    if ($this->normalizeArabicString($dbAr) === $normalizedNameAr && $dbAccount->account_type == $item['account_type']) {
                        $matchedDbAccount = $dbAccount;
                        break;
                    }
                }
            }

            if ($matchedDbAccount) {
                // It already exists! Keep its DB parent and DB code.
                $item['already_exists'] = true;
                $item['parent_id'] = $matchedDbAccount->parent_id;
                $item['code'] = $matchedDbAccount->code;
                $item['db_id'] = $matchedDbAccount->id;
                continue; // Skip the rest of parent resolution
            }

            $parentCode = $item['parent_code'];
            $parentName = $item['parent_name'];

            if (!empty($parentCode) || !empty($parentName)) {
                // Find parent in the current import list
                $parentInImport = null;
                foreach ($normalized as $other) {
                    if ($other['temp_id'] !== $item['temp_id']) {
                        if (!empty($parentCode) && $other['excel_code'] === $parentCode) {
                            $parentInImport = $other;
                            break;
                        }
                        if (!empty($parentName) && 
                            ($this->normalizeArabicString($other['name_ar']) === $this->normalizeArabicString($parentName) ||
                             mb_strtolower($other['name_en']) === mb_strtolower($parentName))) {
                            $parentInImport = $other;
                            break;
                        }
                    }
                }

                if ($parentInImport) {
                    $item['parent_temp_id'] = $parentInImport['temp_id'];
                } else {
                    // Look up parent in the database
                    $parentInDb = null;
                    if (!empty($parentCode)) {
                        $parentInDb = $existingAccounts->firstWhere('code', $parentCode);
                    }
                    if (!$parentInDb && !empty($parentName)) {
                        $normalizedParentName = $this->normalizeArabicString($parentName);
                        foreach ($existingAccounts as $dbAccount) {
                            $dbAr = $dbAccount->translate('ar')?->name ?? '';
                            $dbEn = $dbAccount->translate('en')?->name ?? '';

                            if ($this->normalizeArabicString($dbAr) === $normalizedParentName ||
                                $this->normalizeArabicString($dbEn) === $normalizedParentName) {
                                $parentInDb = $dbAccount;
                                break;
                            }
                        }
                    }

                    if ($parentInDb) {
                        $item['parent_id'] = $parentInDb->id;
                    } else {
                        $item['errors'][] = "لم يتم العثور على الحساب الأب (" . ($parentCode ?: $parentName) . ")";
                    }
                }
            } else {
                // AUTO INFERENCE: If no parent is specified, link it to the system root matching its account_type
                if ($item['account_type']) {
                    $rootAccount = $existingAccounts->whereNull('parent_id')
                        ->where('account_type', $item['account_type'])
                        ->first();
                        
                    if ($rootAccount) {
                        $item['parent_id'] = $rootAccount->id;
                    }
                }
            }
        }
        unset($item);

        // 3. Compute levels and validate against circular reference
        foreach ($normalized as &$item) {
            $this->calculateLevelAndValidate($item, $normalized);
        }
        unset($item);

        // 4. Infer missing account types and natures based on parents or default types
        foreach ($normalized as &$item) {
            if ($item['account_type'] === null) {
                $item['account_type'] = $this->inferAccountTypeFromHierarchy($item, $normalized);
            }
            if ($item['type'] === null) {
                $item['type'] = $this->inferNatureFromHierarchy($item, $normalized);
            }
        }
        unset($item);

        return $normalized;
    }

    /**
     * Generate system codes dynamically for the normalized accounts.
     */
    public function generateCodes(array $normalized)
    {
        // Sort by level so we process parents before children
        usort($normalized, function ($a, $b) {
            return $a['level'] <=> $b['level'];
        });

        $generatedMap = []; // temp_id => generated_code
        $childCounters = []; // parent_key => last_used_code

        foreach ($normalized as &$item) {
            if ($item['already_exists']) {
                $generatedMap[$item['temp_id']] = $item['code'];
                continue; // Keep the existing code from the database
            }

            $parentCode = '';
            $parentKey = null;

            if ($item['parent_temp_id']) {
                $parentKey = $item['parent_temp_id'];
                $parentCode = $generatedMap[$parentKey] ?? '';
            } elseif ($item['parent_id']) {
                $parentKey = 'db_'.$item['parent_id'];
                $parent = TreeAccounts::find($item['parent_id']);
                $parentCode = $parent ? $parent->code : '';
            }

            if ($parentKey) {
                // Child Node Code Generation
                if (! isset($childCounters[$parentKey])) {
                    if ($item['parent_id']) {
                        // Find max code among existing database children
                        $maxDbCode = TreeAccounts::where('parent_id', $item['parent_id'])
                            ->selectRaw('MAX(CAST(code AS UNSIGNED)) as max_code')
                            ->value('max_code');

                        $childCounters[$parentKey] = $maxDbCode ? $this->incrementCode((string) $maxDbCode, $parentCode) : $parentCode.'01';
                    } else {
                        $childCounters[$parentKey] = $parentCode.'01';
                    }
                } else {
                    $childCounters[$parentKey] = $this->incrementCode($childCounters[$parentKey], $parentCode);
                }

                $item['code'] = $childCounters[$parentKey];
            } else {
                // Root Node Code Generation
                $accType = $item['account_type'] ?? TreeAccounts::ACCOUNT_TYPE_ASSET;
                $rootKey = 'root_'.$accType;

                if (! isset($childCounters[$rootKey])) {
                    $maxDbCode = TreeAccounts::whereNull('parent_id')
                        ->where('account_type', $accType)
                        ->selectRaw('MAX(CAST(code AS UNSIGNED)) as max_code')
                        ->value('max_code');

                    $childCounters[$rootKey] = $maxDbCode ? $this->incrementCode((string) $maxDbCode) : (string) $accType;
                } else {
                    $childCounters[$rootKey] = $this->incrementCode($childCounters[$rootKey]);
                }

                $item['code'] = $childCounters[$rootKey];
            }

            // Cache generated code
            $generatedMap[$item['temp_id']] = $item['code'];
        }
        unset($item);

        return $normalized;
    }

    /**
     * Save normalized and reviewed accounts to the database and link to account_mappings.
     */
    public function saveToDatabase(array $normalized)
    {
        // Sort by level so we create parent accounts first
        usort($normalized, function ($a, $b) {
            return $a['level'] <=> $b['level'];
        });

        $dbIdMap = []; // temp_id => db_id
        $existingAccounts = TreeAccounts::with('translations')->get();

        DB::beginTransaction();
        try {
            foreach ($normalized as $item) {
                // Resolve parent_id (either from DB directly or from previously saved node in this transaction)
                $parentId = null;
                if ($item['parent_temp_id']) {
                    $parentId = $dbIdMap[$item['parent_temp_id']] ?? null;
                } elseif ($item['parent_id']) {
                    $parentId = $item['parent_id'];
                }

                // Check if account already exists by name translation AND account_type to avoid duplicates
                $account = null;
                $normalizedNameAr = $this->normalizeArabicString($item['name_ar']);
                foreach ($existingAccounts as $dbAccount) {
                    $dbAr = $dbAccount->translate('ar')?->name ?? '';
                    if ($this->normalizeArabicString($dbAr) === $normalizedNameAr && $dbAccount->account_type == $item['account_type']) {
                        $account = $dbAccount;
                        break;
                    }
                }

                if (! $account) {
                    $account = new TreeAccounts;
                    $account->code = $item['code'];
                    $account->account_type = $item['account_type'];
                    $account->parent_id = $parentId;
                    $account->level = $item['level'];
                    $account->is_leaf = true; // boot method will automatically update parent leaf flag
                    $account->type = $item['type'];
                    $account->status = TreeAccounts::STATUS_ACTIVE;
                    $account->is_system = false;

                    // Add translations
                    $account->translateOrNew('ar')->name = $item['name_ar'];
                    if (! empty($item['name_en'])) {
                        $account->translateOrNew('en')->name = $item['name_en'];
                    }
                    $account->save();
                    
                    // Automatic linking to account_mappings using fuzzy name matching (Only for newly inserted)
                    $this->linkToAccountMappings($account);
                } else {
                    // Account already exists. Skip inserting or updating, just map it so children can link.
                }

                $dbIdMap[$item['temp_id']] = $account->id;

            }

            DB::commit();

            return count($normalized);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import Save Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if a saved account matches predefined mapping keys and link them.
     */
    private function linkToAccountMappings(TreeAccounts $account)
    {
        $mappingKeysMap = [
            'customer' => ['عملاء محليون', 'Local Customers', 'العملاء', 'Customers', 'عميل', 'Customer'],
            'sales' => ['المبيعات', 'Sales', 'مبيعات', 'Sales Account'],
            'sales_return' => ['مردودات المبيعات', 'Sales Returns', 'مرتجع مبيعات', 'Sales Return'],
            'sales_discount' => ['الخصم المسموح به', 'Allowed Discount', 'خصم مسموح به', 'Sales Discount'],
            'sales_tax' => ['ضريبة القيمة المضافة - مخرجات', 'VAT Output', 'ضريبة المبيعات', 'Sales Tax', 'ضريبة القيمة المضافة مخرجات'],
            'shipping_revenue' => ['إيرادات شحن', 'Shipping Revenues', 'ايراد شحن', 'Shipping Revenue'],
            'sales_inventory' => ['مخزون إنتاج تام', 'Finished Goods Inventory', 'مخزون انتاج تام', 'Finished Goods'],
            'cogs' => ['تكلفة البضاعة المباعة', 'Cost of Goods Sold', 'تكلفة المبيعات', 'COGS'],
            'supplier' => ['الموردون المحليون', 'Local Suppliers', 'الموردين', 'Suppliers', 'مورد', 'Supplier'],
            'purchase_inventory' => ['مخزون مواد أولية', 'Raw Materials Inventory', 'مخزون مواد اوليه', 'Raw Materials'],
            'purchase' => ['المشتريات', 'Purchases', 'مشتريات', 'Purchase Account'],
            'purchase_return' => ['مردودات المشتريات', 'Purchase Returns', 'مرتجع مشتريات', 'Purchase Return'],
            'purchase_discount' => ['الخصم المكتسب', 'Earned Discount', 'خصم مكتسب', 'Purchase Discount'],
            'purchase_tax' => ['ضريبة القيمة المضافة - مدخلات', 'VAT Input', 'ضريبة المشتريات', 'Purchase Tax', 'ضريبة القيمة المضافة مدخلات'],
            'inventory' => ['المخزون', 'Inventory', 'مخازن', 'مخزن', 'Stock'],
            'inventory_in_transit' => ['بضاعة بالطريق', 'Inventory In Transit', 'بضاعه بالطريق', 'In Transit'],
            'inventory_settlement' => ['تسوية المخزون', 'Inventory Settlement', 'تسويات المخزون', 'Inventory Adjustment'],
            'inventory_damage' => ['تلف وفاقد المخزون', 'Inventory Damage', 'تالف المخزون', 'Damaged Goods'],
            'inventory_adjustment_loss' => ['خسائر تسويات الجرد', 'Inventory Adjustment Loss', 'خسائر جرد', 'Inventory Loss'],
            'inventory_adjustment_profit' => ['أرباح تسويات الجرد', 'Inventory Adjustment Profit', 'ارباح جرد', 'Inventory Gain'],
            'cash' => ['الصندوق الرئيسي', 'Main Cash Box', 'الصندوق', 'الخزينة', 'Cash', 'Main Cash'],
            'bank' => ['حساب بنكي جاري', 'Current Bank Account', 'البنك', 'Bank', 'Current Bank'],
            'tax' => ['حساب الضريبة', 'Tax Account', 'الضريبة', 'Tax'],
            'capital' => ['رأس المال', 'Capital', 'راس المال'],
            'retained_earnings' => ['أرباح وخسائر مرحلة', 'Retained Earnings', 'ارباح وخسائر مرحله', 'ارباح مرحلة', 'الارباح المرحلة'],
            'income_summary' => ['ملخص الدخل للسنة الحالية', 'Income Summary', 'ملخص الدخل', 'حساب ملخص الدخل'],
            'salaries_expense' => ['الرواتب الأساسية', 'Basic Salaries', 'الرواتب والاجور', 'رواتب واجور', 'Salaries Expense'],
            'accrued_salaries' => ['رواتب وأجور مستحقة', 'Accrued Salaries', 'رواتب مستحقة', 'Accrued Wages'],
            'employee_advance' => ['سلف الموظفين', 'Employee Advances', 'سلف موظفين', 'Employee Advance'],
            'employee_custody' => ['عهدة الموظفين', 'Custodies', 'عهدة موظف', 'Employee Custody'],
            'accumulated_depreciation' => ['مجمعات الإهلاك', 'Accumulated Depreciation', 'مجمع اهلاك', 'Accumulated Depr'],
        ];

        $arName = $account->translate('ar')?->name ?? '';
        $enName = $account->translate('en')?->name ?? '';

        $normalizedAr = $this->normalizeArabicString($arName);
        $normalizedEn = mb_strtolower(trim($enName), 'UTF-8');

        foreach ($mappingKeysMap as $key => $names) {
            foreach ($names as $nameVal) {
                $normVal = $this->normalizeArabicString($nameVal);
                if ($normalizedAr === $normVal || $normalizedEn === mb_strtolower(trim($nameVal), 'UTF-8')) {
                    // Save the mapping
                    AccountMapping::updateOrCreate(
                        ['mapping_key' => $key],
                        [
                            'ar' => ['name' => $arName],
                            'en' => ['name' => $enName ?: $arName],
                            'account_id' => $account->id,
                            'status' => AccountMapping::STATUS_ACTIVE,
                        ]
                    );

                    return; // Link found and mapped
                }
            }
        }
    }

    /**
     * Recursively calculate level and check for circular reference.
     */
    private function calculateLevelAndValidate(&$item, array $normalized, $visited = [])
    {
        if (in_array($item['temp_id'], $visited)) {
            $item['errors'][] = 'حلقة تكرار دائرية في حساب الأب.';

            return;
        }

        if ($item['parent_temp_id']) {
            $parent = null;
            foreach ($normalized as $other) {
                if ($other['temp_id'] === $item['parent_temp_id']) {
                    $parent = $other;
                    break;
                }
            }

            if ($parent) {
                $visited[] = $item['temp_id'];
                $parentLevel = $parent['level'];

                // If parent level is not calculated yet, do it recursively
                if ($parentLevel === 1 && $parent['parent_temp_id'] !== null) {
                    $this->calculateLevelAndValidate($parent, $normalized, $visited);
                    $parentLevel = $parent['level'];
                }

                $item['level'] = $parentLevel + 1;
            }
        } elseif ($item['parent_id']) {
            $parent = TreeAccounts::find($item['parent_id']);
            if ($parent) {
                $item['level'] = $parent->level + 1;
            }
        }
    }

    /**
     * Infer account type based on hierarchy tree parent.
     */
    private function inferAccountTypeFromHierarchy($item, array $normalized)
    {
        if ($item['parent_temp_id']) {
            foreach ($normalized as $parent) {
                if ($parent['temp_id'] === $item['parent_temp_id']) {
                    return $parent['account_type'] ?? $this->inferAccountTypeFromHierarchy($parent, $normalized);
                }
            }
        } elseif ($item['parent_id']) {
            $parent = TreeAccounts::find($item['parent_id']);
            if ($parent) {
                return $parent->account_type;
            }
        }

        // Default root fallbacks based on name analysis
        $normalizedName = $this->normalizeArabicString($item['name_ar']);
        if (strpos($normalizedName, 'اصل') !== false || strpos($normalizedName, 'أصل') !== false) {
            return TreeAccounts::ACCOUNT_TYPE_ASSET;
        }
        if (strpos($normalizedName, 'التزام') !== false || strpos($normalizedName, 'خصوم') !== false) {
            return TreeAccounts::ACCOUNT_TYPE_LIABILITY;
        }
        if (strpos($normalizedName, 'ملكيه') !== false || strpos($normalizedName, 'راس المال') !== false) {
            return TreeAccounts::ACCOUNT_TYPE_EQUITY;
        }
        if (strpos($normalizedName, 'ايراد') !== false || strpos($normalizedName, 'مبيعات') !== false) {
            return TreeAccounts::ACCOUNT_TYPE_REVENUE;
        }
        if (strpos($normalizedName, 'مصروف') !== false || strpos($normalizedName, 'تكلفه') !== false) {
            return TreeAccounts::ACCOUNT_TYPE_EXPENSE;
        }

        return TreeAccounts::ACCOUNT_TYPE_ASSET; // Default
    }

    /**
     * Infer Nature (Debit/Credit) based on hierarchy tree parent or type.
     */
    private function inferNatureFromHierarchy($item, array $normalized)
    {
        if ($item['parent_temp_id']) {
            foreach ($normalized as $parent) {
                if ($parent['temp_id'] === $item['parent_temp_id']) {
                    return $parent['type'] ?? $this->inferNatureFromHierarchy($parent, $normalized);
                }
            }
        } elseif ($item['parent_id']) {
            $parent = TreeAccounts::find($item['parent_id']);
            if ($parent) {
                return $parent->type;
            }
        }

        // Standard Accounting nature based on account type
        $accType = $item['account_type'];
        if (in_array($accType, [
            TreeAccounts::ACCOUNT_TYPE_ASSET,
            TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES,
            TreeAccounts::ACCOUNT_TYPE_TREASURY,
            TreeAccounts::ACCOUNT_TYPE_BANK,
            TreeAccounts::ACCOUNT_TYPE_INVENTORY,
            TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
            TreeAccounts::ACCOUNT_TYPE_PURCHASES,
        ])) {
            return TreeAccounts::TYPE_DEBIT;
        }

        return TreeAccounts::TYPE_CREDIT;
    }

    /**
     * Fetch API content from URL.
     */
    private function fetchFromUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('فشل الاتصال بالرابط الخارجي: '.curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Flatten multi-level JSON containing nested objects/children to a flat list.
     */
    private function flattenJson($data, $parentName = '', &$result = [])
    {
        if (! is_array($data)) {
            return $result;
        }

        // If it's a single item
        if (isset($data['name']) || isset($data['name_ar'])) {
            $item = [
                'name_ar' => $data['name_ar'] ?? $data['name'] ?? '',
                'name_en' => $data['name_en'] ?? '',
                'type' => $data['type'] ?? '',
                'account_type' => $data['account_type'] ?? '',
                'parent_name' => $parentName ?: ($data['parent_name'] ?? ''),
            ];
            $result[] = $item;

            // Process nested children if any
            if (isset($data['children']) && is_array($data['children'])) {
                foreach ($data['children'] as $child) {
                    $this->flattenJson($child, $item['name_ar'], $result);
                }
            }
        } else {
            // It is an array of items
            foreach ($data as $node) {
                $this->flattenJson($node, $parentName, $result);
            }
        }

        return $result;
    }

    /**
     * Parse XML content into simple records.
     */
    private function parseXml($xmlString)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);
        if ($xml === false) {
            throw new Exception('ملف XML غير صالح.');
        }
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $result = [];
        $this->flattenXmlNode($array, '', $result);

        return $result;
    }

    private function flattenXmlNode($node, $parentName = '', &$result = [])
    {
        if (empty($node)) {
            return;
        }

        // If node has account elements or is list of accounts
        $accounts = isset($node['account']) ? (isset($node['account'][0]) ? $node['account'] : [$node['account']]) : [];
        if (empty($accounts) && isset($node['accounts'])) {
            $accounts = isset($node['accounts']['account']) ? (isset($node['accounts']['account'][0]) ? $node['accounts']['account'] : [$node['accounts']['account']]) : [];
        }

        if (! empty($accounts)) {
            foreach ($accounts as $acc) {
                $name = $acc['name_ar'] ?? $acc['name'] ?? '';
                $item = [
                    'name_ar' => $name,
                    'name_en' => $acc['name_en'] ?? '',
                    'type' => $acc['type'] ?? '',
                    'account_type' => $acc['account_type'] ?? '',
                    'parent_name' => $parentName ?: ($acc['parent_name'] ?? ''),
                ];
                $result[] = $item;

                if (isset($acc['children'])) {
                    $this->flattenXmlNode($acc['children'], $name, $result);
                }
            }
        } else {
            // General structure recursive traversal
            foreach ($node as $key => $val) {
                if (is_array($val)) {
                    if (isset($val['name']) || isset($val['name_ar'])) {
                        $name = $val['name_ar'] ?? $val['name'] ?? '';
                        $item = [
                            'name_ar' => $name,
                            'name_en' => $val['name_en'] ?? '',
                            'type' => $val['type'] ?? '',
                            'account_type' => $val['account_type'] ?? '',
                            'parent_name' => $parentName ?: ($val['parent_name'] ?? ''),
                        ];
                        $result[] = $item;
                        $this->flattenXmlNode($val, $name, $result);
                    } else {
                        $this->flattenXmlNode($val, $parentName, $result);
                    }
                }
            }
        }
    }

    /**
     * Parse raw CSV string.
     */
    private function parseCsvString($csvString)
    {
        $lines = explode("\n", str_replace("\r", '', $csvString));
        $headers = [];
        $data = [];
        foreach ($lines as $i => $line) {
            if (empty(trim($line))) {
                continue;
            }
            $row = str_getcsv($line);
            if ($i === 0) {
                $headers = array_map('trim', $row);
            } else {
                $item = [];
                foreach ($headers as $index => $header) {
                    $item[$header] = $row[$index] ?? '';
                }
                $data[] = $item;
            }
        }

        return $data;
    }

    private function detectAndMapColumns(array $rawRows)
    {
        if (empty($rawRows)) {
            return [];
        }

        $firstRow = $rawRows[0];
        $mapping = [
            'code' => null,
            'name_ar' => null,
            'name_en' => null,
            'account_type' => null,
            'type' => null,
            'parent_code' => null,
            'parent_name' => null,
        ];

        foreach ($firstRow as $colName => $value) {
            $cleanCol = mb_strtolower(trim($colName), 'UTF-8');

            if ($cleanCol === 'code' || $cleanCol === 'رمز' || $cleanCol === 'كود') {
                $mapping['code'] = $colName;
            } elseif ($cleanCol === 'name' || $cleanCol === 'اسم' || $cleanCol === 'الاسم') {
                $mapping['name_ar'] = $colName;
            } elseif ($cleanCol === 'name_en' || $cleanCol === 'english' || str_contains($cleanCol, 'انجليزي')) {
                $mapping['name_en'] = $colName;
            } elseif ($cleanCol === 'account_type' || str_contains($cleanCol, 'تصنيف') || $cleanCol === 'نوع الحساب') {
                $mapping['account_type'] = $colName;
            } elseif ($cleanCol === 'type' || str_contains($cleanCol, 'طبيع') || $cleanCol === 'نوع') {
                $mapping['type'] = $colName;
            } elseif ($cleanCol === 'parent_code' || $cleanCol === 'كود الاب' || $cleanCol === 'رمز الاب') {
                $mapping['parent_code'] = $colName;
            } elseif ($cleanCol === 'parent_name' || $cleanCol === 'اسم الاب' || str_contains($cleanCol, 'اب') || str_contains($cleanCol, 'أب')) {
                // Prevent mapping "parent_code" to "parent_name" if fuzzy matched
                if (!str_contains($cleanCol, 'code') && !str_contains($cleanCol, 'كود') && !str_contains($cleanCol, 'رمز')) {
                    $mapping['parent_name'] = $colName;
                }
            }
        }

        // Fallback for some common mixed headers
        if (!$mapping['name_ar']) {
            foreach ($firstRow as $colName => $value) {
                if (strpos(strtolower($colName), 'name') !== false || strpos($colName, 'اسم') !== false) {
                    $mapping['name_ar'] = $colName;
                    break;
                }
            }
        }

        $normalizedRows = [];
        foreach ($rawRows as $row) {
            $normalizedRows[] = [
                'excel_code' => $mapping['code'] ? trim($row[$mapping['code']] ?? '') : '',
                'name_ar' => $mapping['name_ar'] ? trim($row[$mapping['name_ar']] ?? '') : '',
                'name_en' => $mapping['name_en'] ? trim($row[$mapping['name_en']] ?? '') : '',
                'account_type' => $mapping['account_type'] ? trim($row[$mapping['account_type']] ?? '') : '',
                'type' => $mapping['type'] ? trim($row[$mapping['type']] ?? '') : '',
                'parent_code' => $mapping['parent_code'] ? trim($row[$mapping['parent_code']] ?? '') : '',
                'parent_name' => $mapping['parent_name'] ? trim($row[$mapping['parent_name']] ?? '') : '',
            ];
        }

        return $normalizedRows;
    }

    /**
     * Safely increment numerical/padded code.
     */
    private function incrementCode($code, $parentCode = '')
    {
        if (empty($parentCode)) {
            return (string) ((int) $code + 1);
        }
        $parentLen = strlen($parentCode);
        $suffix = substr($code, $parentLen);
        $nextSuffix = (int) $suffix + 1;
        $paddedSuffix = str_pad($nextSuffix, strlen($suffix), '0', STR_PAD_LEFT);

        return $parentCode.$paddedSuffix;
    }

    /**
     * Normalize Arabic string to remove diacritics and unify letters for clean comparisons.
     */
    private function normalizeArabicString($str)
    {
        $str = trim($str);
        // Remove Arabic diacritics (harakat)
        $str = preg_replace('/[\x{064B}-\x{0652}]/u', '', $str);
        // Normalize Alef
        $str = preg_replace('/[أإآ]/u', 'ا', $str);
        // Normalize Yaa
        $str = preg_replace('/ى/u', 'ي', $str);
        // Normalize Ta Marbouta
        $str = preg_replace('/ة/u', 'ه', $str);
        // Remove extra whitespaces
        $str = preg_replace('/\s+/', ' ', $str);

        $str = mb_strtolower($str, 'UTF-8');

        // Optional: strip leading 'ال' to improve fuzzy matching (e.g. الأصول == أصول)
        if (mb_strpos($str, 'ال') === 0) {
            $str = mb_substr($str, 2);
        }

        return trim($str);
    }
}
