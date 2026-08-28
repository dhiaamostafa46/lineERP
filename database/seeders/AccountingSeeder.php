<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\TreeAccounts;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $this->createFiscalYear();
        $this->createChartOfAccounts();
        $this->createCostCenters();
    }

    private function createFiscalYear()
    {
        FiscalYear::firstOrCreate(
            ['start_date' => Carbon::parse(date('Y') . '-01-01'), 'end_date' => Carbon::parse(date('Y') . '-12-31')],
            ['is_current' => true, 'is_closed' => false]
        );
    }

    private function createChartOfAccounts()
    {
        $accounts = [
            [
                'code' => '1',
                'name_ar' => 'الأصول',
                'name_en' => 'Assets',
                'account_type' => 'asset',
                'type' => 'debtor',
                'children' => [
                    [
                        'code' => '11',
                        'name_ar' => 'الأصول المتداولة',
                        'name_en' => 'Current Assets',
                        'type' => 'debtor',
                        'children' => [
                            [
                                'code' => '111',
                                'name_ar' => 'النقدية وما في حكمها',
                                'name_en' => 'Cash and Cash Equivalents',
                                'type' => 'debtor',
                                'children' => [
                                    [
                                        'code' => '11101',
                                        'name_ar' => 'الصناديق',
                                        'name_en' => 'Cash in Hand',
                                        'account_type' => 'treasury',
                                        'type' => 'debtor',
                                        'children' => [
                                            ['code' => '111011', 'name_ar' => 'الصندوق الرئيسي', 'name_en' => 'Main Cash Box', 'type' => 'debtor', 'is_leaf' => true], 
                                            ['code' => '111012', 'name_ar' => 'صندوق المصاريف والعهد', 'name_en' => 'Petty Cash Box', 'type' => 'debtor', 'is_leaf' => true],
                                            ['code' => '111013', 'name_ar' => 'صندوق المركبات', 'name_en' => 'Vehicles Cash Box', 'type' => 'debtor', 'is_leaf' => true]
                                        ],
                                    ],
                                    [
                                        'code' => '11102',
                                        'name_ar' => 'البنوك',
                                        'name_en' => 'Cash in Banks',
                                        'account_type' => 'bank',
                                        'type' => 'debtor',
                                        'children' => [
                                            ['code' => '111021', 'name_ar' => 'حساب بنكي جاري', 'name_en' => 'Current Bank Account', 'type' => 'debtor', 'is_leaf' => true],
                                            ['code' => '111022', 'name_ar' => 'بنك المركبات', 'name_en' => 'Vehicles Bank Account', 'type' => 'debtor', 'is_leaf' => true]
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'code' => '112',
                                'name_ar' => 'المدينون والذمم المدينة',
                                'name_en' => 'Receivables',
                                'type' => 'debtor',
                                'children' => [
                                    [
                                        'code' => '11201',
                                        'name_ar' => 'العملاء',
                                        'name_en' => 'Customers',
                                        'account_type' => 'customers',
                                        'type' => 'debtor',
                                        'children' => [
                                            ['code' => '11201001', 'name_ar' => 'عملاء محليون', 'name_en' => 'Local Customers', 'type' => 'debtor', 'is_leaf' => false],
                                            ['code' => '11201002', 'name_ar' => 'عملاء خارجيون', 'name_en' => 'External Customers', 'type' => 'debtor', 'is_leaf' => false],
                                       
                                        ],
                                    ],
                                    ['code' => '11202', 'name_ar' => 'أوراق القبض', 'name_en' => 'Notes Receivable', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11203', 'name_ar' => 'شيكات تحت التحصيل', 'name_en' => 'Checks Under Collection', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '113',
                                'name_ar' => 'المخزون',
                                'name_en' => 'Inventory',
                                'account_type' => 'inventory',
                                'type' => 'debtor',
                                'children' => [
                                    ['code' => '11301', 'name_ar' => 'مخزون إنتاج تام', 'name_en' => 'Finished Goods Inventory', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11302', 'name_ar' => 'مخزون مواد أولية', 'name_en' => 'Raw Materials Inventory', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11303', 'name_ar' => 'بضاعة بالطريق', 'name_en' => 'Inventory In Transit', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '114',
                                'name_ar' => 'أرصدة مدينة أخرى',
                                'name_en' => 'Other Debit Balances',
                                'type' => 'debtor',
                                'children' => [
                                    ['code' => '11401', 'name_ar' => 'مصاريف مدفوعة مقدماً', 'name_en' => 'Prepaid Expenses', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11402', 'name_ar' => 'ضريبة القيمة المضافه - مدخلات', 'name_en' => 'VAT Input', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11403', 'name_ar' => 'سلف الموظفين', 'name_en' => 'Staff Advances', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11404', 'name_ar' => 'عهد الموظفين', 'name_en' => 'Staff Custodies', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11405', 'name_ar' => 'سلف وعهد السائقين', 'name_en' => 'Driver Advances and Custodies', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '11406', 'name_ar' => 'مطالبات شركات التأمين (مركبات)', 'name_en' => 'Vehicles Insurance Receivables', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => '12',
                        'name_ar' => 'الأصول غير المتداولة',
                        'name_en' => 'Non-Current Assets',
                        'type' => 'debtor',
                        
                        'children' => [
                            [
                                'code' => '121',
                                'name_ar' => 'الأصول الثابتة',
                                'name_en' => 'Fixed Assets',
                                'type' => 'debtor',
                                'account_type' => 'Fixedasset',
                                'children' => [
                                    ['code' => '12101', 'name_ar' => 'الأراضي', 'name_en' => 'Land', 'account_type' => 'Fixedasset','type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '12102', 'name_ar' => 'المباني والمنشآت', 'name_en' => 'Buildings',  'account_type' => 'Fixedasset', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '12103', 'name_ar' => 'الآلات والمعدات', 'name_en' => 'Machinery and Equipment', 'account_type' => 'Fixedasset', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '12104', 'name_ar' => 'وسائل نقل وانتقالات', 'name_en' => 'Vehicles',  'account_type' => 'Fixedasset', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '12105', 'name_ar' => 'أثاث وتجهيزات مكتبية', 'name_en' => 'Furniture and Office Equipment', 'account_type' => 'Fixedasset', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => '2',
                'name_ar' => 'الالتزامات',
                'name_en' => 'Liabilities',
                'account_type' => 'liability',
                'type' => 'creditor',
                'children' => [
                    [
                        'code' => '21',
                        'name_ar' => 'الالتزامات المتداولة',
                        'name_en' => 'Current Liabilities',
                        'type' => 'creditor',
                        'children' => [
                            [
                                'code' => '211',
                                'name_ar' => 'الموردون وأوراق الدفع',
                                'name_en' => 'Trade Payables',
                                'type' => 'creditor',
                                'children' => [
                                    ['code' => '21101', 'name_ar' => 'الموردون المحليون', 'name_en' => 'Local Suppliers', 'account_type' => 'suppliers', 'type' => 'creditor', 'is_leaf' =>false],
                                    ['code' => '21102', 'name_ar' => 'الموردون الخارجيون', 'name_en' => 'External Suppliers', 'type' => 'creditor', 'is_leaf' => false],
                                    ['code' => '21103', 'name_ar' => 'أوراق الدفع', 'name_en' => 'Notes Payable', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21104', 'name_ar' => 'الموردون والدائنون (مركبات)', 'name_en' => 'Vehicles Suppliers Payable', 'type' => 'creditor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '212',
                                'name_ar' => 'المصاريف المستحقة',
                                'name_en' => 'Accrued Expenses',
                                'type' => 'creditor',
                                'children' => [
                                    ['code' => '21201', 'name_ar' => 'رواتب وأجور مستحقة', 'name_en' => 'Accrued Salaries', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21202', 'name_ar' => 'عمولات مستحقة', 'name_en' => 'Accrued Commissions', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21203', 'name_ar' => 'ذمم/جاري السائقين', 'name_en' => 'Driver Payables', 'type' => 'creditor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '213',
                                'name_ar' => 'الضرائب المستحقة',
                                'name_en' => 'Taxes Payable',
                                'type' => 'creditor',
                                'children' => [
                                    ['code' => '21301', 'name_ar' => 'ضريبة القيمة المضافة - مخرجات', 'name_en' => 'VAT Output', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21302', 'name_ar' => 'ضريبة الدخل والزكاة', 'name_en' => 'Income Tax and Zakat', 'type' => 'creditor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '214',
                                'name_ar' => 'أرصدة دائنة أخرى',
                                'name_en' => 'Other Credit Balances',
                                'type' => 'creditor',
                                'children' => [
                                    ['code' => '21401', 'name_ar' => 'قروض قصيرة الأجل', 'name_en' => 'Short Term Loans', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21402', 'name_ar' => 'تأمينات محتجزة للغير', 'name_en' => 'Retention for Others', 'type' => 'creditor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '215',
                                'name_ar' => 'مجمعات الإهلاك',
                                'name_en' => 'Accumulated Depreciation',
                                'type' => 'creditor',
                                'children' => [
                                    ['code' => '21501', 'name_ar' => 'مجمع إهلاك مباني', 'name_en' => 'Acc. Depr. Buildings', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21502', 'name_ar' => 'مجمع إهلاك آلات ومعدات', 'name_en' => 'Acc. Depr. Machinery', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21503', 'name_ar' => 'مجمع إهلاك وسائل نقل', 'name_en' => 'Acc. Depr. Vehicles', 'type' => 'creditor', 'is_leaf' => true],
                                    ['code' => '21504', 'name_ar' => 'مجمع إهلاك أثاث ومكتبية', 'name_en' => 'Acc. Depr. Furniture', 'type' => 'creditor', 'is_leaf' => true],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => '22',
                        'name_ar' => 'الالتزامات غير المتداولة',
                        'name_en' => 'Non-Current Liabilities',
                        'type' => 'creditor',
                        'children' => [
                            ['code' => '221', 'name_ar' => 'قروض طويلة الأجل', 'name_en' => 'Long Term Loans', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '222', 'name_ar' => 'مخصصات طويلة الأجل', 'name_en' => 'Long Term Provisions', 'type' => 'creditor', 'is_leaf' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '3',
                'name_ar' => 'حقوق الملكية',
                'name_en' => 'Owner\'s Equity',
                'account_type' => 'equity',
                'type' => 'creditor',
                'children' => [
                    [
                        'code' => '31',
                        'name_ar' => 'رأس المال والاحتياطيات',
                        'name_en' => 'Capital and Reserves',
                        'type' => 'creditor',
                        'children' => [
                            ['code' => '3101', 'name_ar' => 'رأس المال', 'name_en' => 'Capital', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '3102', 'name_ar' => 'جاري الشركاء', 'name_en' => 'Partner Current Account', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '3103', 'name_ar' => 'احتياطي قانوني', 'name_en' => 'Statutory Reserve', 'type' => 'creditor', 'is_leaf' => true],
                        ],
                    ],
                    [
                        'code' => '32',
                        'name_ar' => 'الأرباح المحتجزة',
                        'name_en' => 'Retained Earnings',
                        'type' => 'creditor',
                        'children' => [
                            ['code' => '3201', 'name_ar' => 'أرباح وخسائر مرحلة', 'name_en' => 'Retained Earnings', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '3202', 'name_ar' => 'ملخص الدخل للسنة الحالية', 'name_en' => 'Income Summary', 'type' => 'creditor', 'is_leaf' => true, 'is_system' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '4',
                'name_ar' => 'المصاريف',
                'name_en' => 'Expenses',
                'account_type' => 'expense',
                'type' => 'debtor',
                'children' => [
                    [
                        'code' => '41',
                        'name_ar' => 'تكلفة النشاط',
                        'name_en' => 'Cost of Sales',
                        'account_type' => 'cost_of_sales',
                        'type' => 'debtor',
                        'children' => [
                            ['code' => '411', 'name_ar' => 'تكلفة البضاعة المباعة', 'name_en' => 'Cost of Goods Sold', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '412', 'name_ar' => 'مردودات المبيعات', 'name_en' => 'Sales Returns', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '413', 'name_ar' => 'الخصم المسموح به', 'name_en' => 'Allowed Discount', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '414', 'name_ar' => 'تسوية المخزون', 'name_en' => 'Inventory Settlement', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '415', 'name_ar' => 'مصروف تلف وفاقد المخزون', 'name_en' => 'Inventory Damage / Loss', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '416', 'name_ar' => 'خسائر تسويات الجرد', 'name_en' => 'Inventory Adjustment Loss', 'type' => 'debtor', 'is_leaf' => true],
                        ],
                    ],
                    [
                        'code' => '42',
                        'name_ar' => 'المصاريف الإدارية والعمومية',
                        'name_en' => 'General and Administrative Expenses',
                        'type' => 'debtor',
                        'children' => [
                            [
                                'code' => '421',
                                'name_ar' => 'الرواتب والأجور وملحقاتها',
                                'name_en' => 'Salaries and Wages',
                                'type' => 'debtor',
                                'children' => [
                                    ['code' => '42101', 'name_ar' => 'الرواتب الأساسية', 'name_en' => 'Basic Salaries', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42102', 'name_ar' => 'البدلات والمزايا', 'name_en' => 'Allowances and Benefits', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42103', 'name_ar' => 'التأمينات الاجتماعية - حصة الشركة', 'name_en' => 'Social Insurance - Company Share', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42104', 'name_ar' => 'أجور وعمولات السائقين', 'name_en' => 'Driver Commissions', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42105', 'name_ar' => 'مكافآت السائقين', 'name_en' => 'Driver Bonuses', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '422',
                                'name_ar' => 'مصاريف التشغيل والمرافق',
                                'name_en' => 'Operating and Utilities Expenses',
                                'type' => 'debtor',
                                'children' => [
                                    ['code' => '42201', 'name_ar' => 'الإيجارات', 'name_en' => 'Rents', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42202', 'name_ar' => 'الكهرباء والمياه', 'name_en' => 'Electricity and Water', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42203', 'name_ar' => 'الاتصالات والإنترنت', 'name_en' => 'Telecom and Internet', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42204', 'name_ar' => 'مصروف صيانة المركبات', 'name_en' => 'Vehicles Maintenance Expense', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42205', 'name_ar' => 'مصروف تأمين المركبات', 'name_en' => 'Vehicles Insurance Expense', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42206', 'name_ar' => 'مصروف وقود المركبات', 'name_en' => 'Vehicles Fuel Expense', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                            [
                                'code' => '423',
                                'name_ar' => 'مصاريف عامة أخرى',
                                'name_en' => 'Other General Expenses',
                                'type' => 'debtor',
                                'children' => [
                                    ['code' => '42301', 'name_ar' => 'الأدوات المكتبية والقرطاسية', 'name_en' => 'Stationery', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42302', 'name_ar' => 'مصاريف البنك والعمولات', 'name_en' => 'Bank Charges and Commissions', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42303', 'name_ar' => 'مصاريف صيانة عمومية', 'name_en' => 'General Maintenance', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42304', 'name_ar' => 'إهلاك الأصول الثابتة', 'name_en' => 'Depreciation Expense', 'type' => 'debtor', 'is_leaf' => true],
                                    ['code' => '42305', 'name_ar' => 'مصروف المخالفات المرورية', 'name_en' => 'Traffic Violations Expense', 'type' => 'debtor', 'is_leaf' => true],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => '43',
                        'name_ar' => 'المصاريف البيعية والتسويقية',
                        'name_en' => 'Selling and Marketing Expenses',
                        'type' => 'debtor',
                        'children' => [
                            ['code' => '431', 'name_ar' => 'الدعاية والإعلان', 'name_en' => 'Advertising', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '432', 'name_ar' => 'عمولات البيع', 'name_en' => 'Sales Commissions', 'type' => 'debtor', 'is_leaf' => true],
                            ['code' => '433', 'name_ar' => 'مصاريف نقل المبيعات', 'name_en' => 'Delivery Expenses', 'type' => 'debtor', 'is_leaf' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '5',
                'name_ar' => 'الإيرادات',
                'name_en' => 'Revenues',
                'account_type' => 'revenue',
                'type' => 'creditor',
                'children' => [
                    [
                        'code' => '51',
                        'name_ar' => 'إيرادات النشاط الرئيسي',
                        'name_en' => 'Operating Revenues',
                        'type' => 'creditor',
                        'children' => [
                            ['code' => '511', 'name_ar' => 'المبيعات', 'name_en' => 'Sales', 'account_type' => 'sales', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '512', 'name_ar' => 'إيرادات الخدمات', 'name_en' => 'Service Revenues', 'type' => 'creditor', 'is_leaf' => true],
                        ],
                    ],
                    [
                        'code' => '52',
                        'name_ar' => 'إيرادات أخرى',
                        'name_en' => 'Other Income',
                        'type' => 'creditor',
                        'children' => [
                            ['code' => '521', 'name_ar' => 'الخصم المكتسب', 'name_en' => 'Earned Discount', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '522', 'name_ar' => 'إيرادات متنوعة', 'name_en' => 'Miscellaneous Income', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '523', 'name_ar' => 'فروقات تحويل عملات', 'name_en' => 'Currency Exchange Gains', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '524', 'name_ar' => 'أرباح تسويات الجرد', 'name_en' => 'Inventory Adjustment Profit', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '525', 'name_ar' => 'إيرادات شحن', 'name_en' => 'Shipping Revenues', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '526', 'name_ar' => 'إيراد غرامات وجزاءات السائقين', 'name_en' => 'Driver Deductions Income', 'type' => 'creditor', 'is_leaf' => true],
                            ['code' => '527', 'name_ar' => 'إيراد استرداد مخالفات السائقين', 'name_en' => 'Driver Traffic Violations Recovery Income', 'type' => 'creditor', 'is_leaf' => true],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($accounts as $account) {
            $this->createAccountRecursive($account);
        }
    }

    private function createAccountRecursive($data, $parent = null)
    {
        $hasChildren = !empty($data['children']);

        $accountTypes = [
            'asset' => TreeAccounts::ACCOUNT_TYPE_ASSET,
            'liability' => TreeAccounts::ACCOUNT_TYPE_LIABILITY,
            'equity' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
            'revenue' => TreeAccounts::ACCOUNT_TYPE_REVENUE,
            'expense' => TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            'cost_of_sales' => TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES,
            'treasury' => TreeAccounts::ACCOUNT_TYPE_TREASURY,
            'bank' => TreeAccounts::ACCOUNT_TYPE_BANK,
            'inventory' => TreeAccounts::ACCOUNT_TYPE_INVENTORY,
            'customers' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
            'suppliers' => TreeAccounts::ACCOUNT_TYPE_SUPPLIERS,
            'sales' => TreeAccounts::ACCOUNT_TYPE_SALES,
            'purchases' => TreeAccounts::ACCOUNT_TYPE_PURCHASES,
            'Fixedasset' => TreeAccounts::ACCOUNT_TYPE_FIXED_ASSET,
        ];

        $accountType = isset($data['account_type']) ? $accountTypes[$data['account_type']] ?? null : ($parent ? $parent->account_type : null);

        // Determine type based on data provided, with fallback to calculation
        if (isset($data['type'])) {
            $type = $data['type'] === 'creditor' ? TreeAccounts::TYPE_CREDIT : TreeAccounts::TYPE_DEBIT;
        } else {
            // Fallback logic if 'type' is not specified in the array
            $type = TreeAccounts::TYPE_DEBIT;
            if (in_array($accountType, [TreeAccounts::ACCOUNT_TYPE_LIABILITY, TreeAccounts::ACCOUNT_TYPE_EQUITY, TreeAccounts::ACCOUNT_TYPE_REVENUE])) {
                $type = TreeAccounts::TYPE_CREDIT;
            }
        }

        $account = TreeAccounts::where('code', $data['code'])->first();

        if (!$account) {
            $account = TreeAccounts::create([
                'code' => $data['code'],
                'ar' => ['name' => $data['name_ar']],
                'en' => ['name' => $data['name_en']],
                'account_type' => $accountType,
                'parent_id' => $parent ? $parent->id : null,
                'is_leaf' => $data['is_leaf'] ?? 0,
                'level' => $parent ? $parent->level + 1 : 1,
                'type' => $type,
                'status' => TreeAccounts::STATUS_ACTIVE,
                'is_system' => $data['is_system'] ?? false,
            ]);
        } else {
            // Update translations if needed, but we don't overwrite names if user changed them.
            // Just ensure it exists.
        }

        if ($hasChildren) {
            foreach ($data['children'] as $child) {
                $this->createAccountRecursive($child, $account);
            }
        }
    }

    private function createCostCenters()
    {
        if (CostCenters::exists()) {
            return;
        }

        $centers = [
            [
                'code' => 'CC-01',
                'name_ar' => 'الإدارة العامة',
                'name_en' => 'General Management',
                'children' => [['code' => 'CC-01-01', 'name_ar' => 'الموارد البشرية', 'name_en' => 'Human Resources'], ['code' => 'CC-01-02', 'name_ar' => 'المالية', 'name_en' => 'Finance'], ['code' => 'CC-01-03', 'name_ar' => 'تقنية المعلومات', 'name_en' => 'IT']],
            ],
            [
                'code' => 'CC-02',
                'name_ar' => 'المبيعات والتسويق',
                'name_en' => 'Sales and Marketing',
                'children' => [['code' => 'CC-02-01', 'name_ar' => 'المبيعات - الرياض', 'name_en' => 'Sales - Riyadh'], ['code' => 'CC-02-02', 'name_ar' => 'المبيعات - جدة', 'name_en' => 'Sales - Jeddah']],
            ],
            [
                'code' => 'CC-03',
                'name_ar' => 'العمليات والتشغيل',
                'name_en' => 'Operations',
            ],
        ];

        foreach ($centers as $center) {
            $this->createCostCenterRecursive($center);
        }
    }

    private function createCostCenterRecursive($data, $parent = null)
    {
        $hasChildren = !empty($data['children']);

        $center = CostCenters::updateOrCreate(
            ['code' => $data['code']],
            [
                'ar' => ['name' => $data['name_ar']],
                'en' => ['name' => $data['name_en']],
                'parent_id' => $parent ? $parent->id : null,
                'is_leaf' => !$hasChildren,
                'level' => $parent ? $parent->level + 1 : 1,
                'status' => CostCenters::STATUS_ACTIVE,
            ]
        );

        if ($hasChildren) {
            foreach ($data['children'] as $child) {
                $this->createCostCenterRecursive($child, $center);
            }
        }
    }
}
