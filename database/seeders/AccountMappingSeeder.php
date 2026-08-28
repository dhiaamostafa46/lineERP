<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Support\Facades\DB;
class AccountMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * يفترض هذا الـ Seeder أنه تم تشغيل AccountingSeeder أولاً.
     */
    public function run(): void
    {
        // 1. تعريف التوجيهات المحاسبية المطلوبة [key => [code, name]]
        $mappings = [
            // --- المبيعات والعملاء (Sales & Customers) ---
            'customer'         => ['code' => '11201001', 'name_ar' => 'عملاء محليون',                    'name_en' => 'Local Customers'],
            'sales'            => ['code' => '511',      'name_ar' => 'المبيعات',                         'name_en' => 'Sales'],
            'sales_return'     => ['code' => '412',      'name_ar' => 'مردودات المبيعات',                 'name_en' => 'Sales Returns'],
            'sales_discount'   => ['code' => '413',      'name_ar' => 'الخصم المسموح به',                 'name_en' => 'Allowed Discount'],
            'sales_tax'        => ['code' => '21301',    'name_ar' => 'ضريبة القيمة المضافة - مخرجات',    'name_en' => 'VAT Output'],
            'shipping_revenue' => ['code' => '525',      'name_ar' => 'إيرادات شحن',                     'name_en' => 'Shipping Revenues'],
            // المخزون في البيع (نظام الجرد المستمر - يُنقص من هذا الحساب عند البيع)
            'sales_inventory'  => ['code' => '11301',    'name_ar' => 'مخزون إنتاج تام',                 'name_en' => 'Finished Goods Inventory'],
            // تكلفة البضاعة المباعة (نظام الجرد المستمر)
            'cogs'             => ['code' => '411',      'name_ar' => 'تكلفة البضاعة المباعة',            'name_en' => 'Cost of Goods Sold'],

            // --- المشتريات والموردين (Purchases & Suppliers) ---
            'supplier'           => ['code' => '21101', 'name_ar' => 'الموردون المحليون',                 'name_en' => 'Local Suppliers'],
            // المخزون في الشراء (نظام الجرد المستمر - يُزاد في هذا الحساب عند الشراء)
            'purchase_inventory' => ['code' => '11302', 'name_ar' => 'مخزون مواد أولية',                 'name_en' => 'Raw Materials Inventory'],
            'purchase'           => ['code' => '11302', 'name_ar' => 'مخزون مواد أولية',                 'name_en' => 'Raw Materials Inventory'],
            // مرتجع المشتريات في الجرد المستمر = نقص في المخزون مباشرة (لا يوجد حساب مرتجعات مستقل)
            'purchase_return'    => ['code' => '11302', 'name_ar' => 'مخزون مواد أولية (مرتجع)',          'name_en' => 'Raw Materials Inventory (Return)'],
            'purchase_discount'  => ['code' => '521',   'name_ar' => 'الخصم المكتسب',                    'name_en' => 'Earned Discount'],
            'purchase_tax'       => ['code' => '11402', 'name_ar' => 'ضريبة القيمة المضافة - مدخلات',    'name_en' => 'VAT Input'],

            // --- المخزون العام (Inventory) ---
            'inventory'            => ['code' => '113', 'name_ar' => 'المخزون',                 'name_en' => 'Inventory'],
            'inventory_in_transit' => ['code' => '11303', 'name_ar' => 'بضاعة بالطريق',         'name_en' => 'Inventory In Transit'],
            
            'inventory_settlement' => ['code' => '414',   'name_ar' => 'تسوية المخزون',                  'name_en' => 'Inventory Settlement'],
            'inventory_damage'     => ['code' => '415',   'name_ar' => 'تلف وفاقد المخزون',              'name_en' => 'Inventory Damage'],
            'inventory_adjustment_loss'   => ['code' => '416', 'name_ar' => 'خسائر تسويات الجرد',           'name_en' => 'Inventory Adjustment Loss'],
            'inventory_adjustment_profit' => ['code' => '524', 'name_ar' => 'أرباح تسويات الجرد',           'name_en' => 'Inventory Adjustment Profit'],

            // --- النقدية والبنوك (Cash & Banks) ---
            // الكود الصحيح هو الحساب الفرعي (is_leaf=true) وليس الأب
            'cash' => ['code' => '111011', 'name_ar' => 'الصندوق الرئيسي',    'name_en' => 'Main Cash Box'],
            'bank' => ['code' => '111021', 'name_ar' => 'حساب بنكي جاري',     'name_en' => 'Current Bank Account'],
            'tax'=> ['code' => '213', 'name_ar' => 'حساب الضريبة',     'name_en' => 'Tax Account'],


            // --- حقوق الملكية (Equity) ---
            'capital' => ['code' => '3101', 'name_ar' => 'رأس المال', 'name_en' => 'Capital'],
            'retained_earnings' => ['code' => '3201', 'name_ar' => 'أرباح وخسائر مرحلة', 'name_en' => 'Retained Earnings'],
            'income_summary' => ['code' => '3202', 'name_ar' => 'ملخص الدخل للسنة الحالية', 'name_en' => 'Income Summary'],

            // --- الموارد البشرية (HR & Payroll) ---
            'salaries_expense' => ['code' => '42101', 'name_ar' => 'الرواتب الأساسية', 'name_en' => 'Basic Salaries'],
            'accrued_salaries' => ['code' => '21201', 'name_ar' => 'رواتب وأجور مستحقة', 'name_en' => 'Accrued Salaries'],
            'employee_advance' => ['code' => '11403', 'name_ar' => 'سلف الموظفين', 'name_en' => 'Employee Advances'],
            'employee_custody' => ['code' => '11404', 'name_ar' => 'عهدة الموظفين', 'name_en' => 'Custodies'],

            // --- الأصول الثابتة (Fixed Assets) ---
            'accumulated_depreciation' => ['code' => '21501', 'name_ar' => 'مجمعات الإهلاك', 'name_en' => 'Accumulated Depreciation'],
            'Expenseasste_depreciation' => ['code' => '42304', 'name_ar' => 'مصروف إهلاك الأصول الثابتة', 'name_en' => 'Expenseasste depreciation'],

            // ['code' => '42304', 'name_ar' => 'إهلاك الأصول الثابتة', 'name_en' => 'Depreciation Expense', 'type' => 'debtor', 'is_leaf' => true],
            
            // --- السائقين (Drivers) ---
            'driver_advances_account' => ['code' => '11405', 'name_ar' => 'سلف وعهد السائقين', 'name_en' => 'Driver Advances'],
            'driver_payables_account' => ['code' => '21203', 'name_ar' => 'ذمم/جاري السائقين', 'name_en' => 'Driver Payables'],
            'driver_commissions_expense' => ['code' => '42104', 'name_ar' => 'أجور وعمولات السائقين', 'name_en' => 'Driver Commissions'],
            'driver_bonuses_expense' => ['code' => '42105', 'name_ar' => 'مكافآت السائقين', 'name_en' => 'Driver Bonuses'],
            'driver_deductions_income' => ['code' => '526', 'name_ar' => 'إيراد غرامات وجزاءات السائقين', 'name_en' => 'Driver Deductions Income'],
            'driver_traffic_violations_recovery_income' => ['code' => '527', 'name_ar' => 'إيراد استرداد مخالفات السائقين', 'name_en' => 'Driver Traffic Violations Recovery Income'],
            
            // --- المركبات (Vehicles) ---
            'vehicle_maintenance_expense' => ['code' => '42204', 'name_ar' => 'مصروف صيانة المركبات', 'name_en' => 'Vehicles Maintenance Expense'],
            'vehicle_insurance_expense' => ['code' => '42205', 'name_ar' => 'مصروف تأمين المركبات', 'name_en' => 'Vehicles Insurance Expense'],
            'vehicle_traffic_violations_expense' => ['code' => '42305', 'name_ar' => 'مصروف المخالفات المرورية', 'name_en' => 'Traffic Violations Expense'],
            'vehicle_fuel_expense' => ['code' => '42206', 'name_ar' => 'مصروف وقود المركبات', 'name_en' => 'Vehicles Fuel Expense'],
            'vehicle_cash_account' => ['code' => '111013', 'name_ar' => 'صندوق المركبات', 'name_en' => 'Vehicles Cash Box'],
            'vehicle_bank_account' => ['code' => '111022', 'name_ar' => 'بنك المركبات', 'name_en' => 'Vehicles Bank Account'],
            'vehicle_insurance_receivable_account' => ['code' => '11406', 'name_ar' => 'مطالبات شركات التأمين (مركبات)', 'name_en' => 'Vehicles Insurance Receivables'],
            'vehicle_supplier_payable_account' => ['code' => '21104', 'name_ar' => 'الموردون والدائنون (مركبات)', 'name_en' => 'Vehicles Suppliers Payable'],
        ];

        // 2. إنشاء أو تحديث التوجيهات (updateOrCreate لمنع التكرار عند إعادة التشغيل)
        foreach ($mappings as $key => $data) {
            $account = TreeAccounts::where('code', $data['code'])->first();

            if ($account) {
                AccountMapping::updateOrCreate(
                    ['mapping_key' => $key],
                    [
                        'ar'         => ['name' => $data['name_ar']],
                        'en'         => ['name' => $data['name_en']],
                        'account_id' => $account->id,
                        'status'     => AccountMapping::STATUS_ACTIVE,
                    ]
                );
            } else {
                // طباعة تحذير في الكونسول إذا لم يتم العثور على الحساب
                $this->command->warn("Account with code '{$data['code']}' for mapping key '{$key}' not found. Skipping.");
            }
        }
    }
}
