<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class QuickAccess extends Component
{
    public $shortcuts = [];

    public function mount()
    {
        $this->shortcuts = [
            [
                'title' => 'فاتورة مبيعات جديدة',
                'subtitle' => 'إصدار فاتورة ضريبية ZATCA',
                'icon' => 'ki-document',
                'bg_color' => 'rgba(27, 54, 93, 0.08)',
                'icon_color' => '#1b365d',
                'border_color' => '#1b365d',
                'url' => Route::has('invoices.sales_invoices.create') ? route('invoices.sales_invoices.create') : url('/invoices/sales_invoices/create'),
            ],
            [
                'title' => 'فاتورة مشتريات جديدة',
                'subtitle' => 'تسجيل توريد من مورد',
                'icon' => 'ki-shopping-cart',
                'bg_color' => 'rgba(104, 94, 153, 0.08)',
                'icon_color' => '#685e99',
                'border_color' => '#685e99',
                'url' => Route::has('invoices.purchases_invoices.create') ? route('invoices.purchases_invoices.create') : url('/invoices/purchases_invoices/create'),
            ],
            [
                'title' => 'عرض سعر جديد',
                'subtitle' => 'تقديم عرض سعر لعميل',
                'icon' => 'ki-file',
                'bg_color' => 'rgba(134, 184, 107, 0.12)',
                'icon_color' => '#2e694e',
                'border_color' => '#86b86b',
                'url' => Route::has('invoices.quotations.create') ? route('invoices.quotations.create') : url('/invoices/quotations/create'),
            ],
            [
                'title' => 'كوانتر نقاط البيع (POS)',
                'subtitle' => 'فتح شاشة بيع الكوانتر',
                'icon' => 'ki-screen',
                'bg_color' => 'rgba(217, 119, 6, 0.08)',
                'icon_color' => '#d97706',
                'border_color' => '#d97706',
                'url' => Route::has('pos.select_device') ? route('pos.select_device') : (Route::has('pos.devices.index') ? route('pos.devices.index') : url('/pos/select-device')),
            ],
            [
                'title' => 'إضافة صنف / منتج',
                'subtitle' => 'تسجيل كارت صنف مخزني',
                'icon' => 'ki-package',
                'bg_color' => 'rgba(27, 54, 93, 0.08)',
                'icon_color' => '#1b365d',
                'border_color' => '#1b365d',
                'url' => Route::has('store.products.create') ? route('store.products.create') : url('/store/products/create'),
            ],
            [
                'title' => 'إضافة عميل جديد',
                'subtitle' => 'تسجيل بيانات عميل ذمم',
                'icon' => 'ki-user',
                'bg_color' => 'rgba(134, 184, 107, 0.12)',
                'icon_color' => '#2e694e',
                'border_color' => '#86b86b',
                'url' => Route::has('inv_customers.create') ? route('inv_customers.create') : url('/inv_customers/create'),
            ],
            [
                'title' => 'إضافة مورد جديد',
                'subtitle' => 'فتح حساب مورد جديد',
                'icon' => 'ki-bank',
                'bg_color' => 'rgba(104, 94, 153, 0.08)',
                'icon_color' => '#685e99',
                'border_color' => '#685e99',
                'url' => Route::has('inv_suppliers.create') ? route('inv_suppliers.create') : url('/inv_suppliers/create'),
            ],
            [
                'title' => 'سجلات الحضور والبصمة',
                'subtitle' => 'متابعة دوام الموظفين',
                'icon' => 'ki-fingerprint',
                'bg_color' => 'rgba(217, 56, 86, 0.08)',
                'icon_color' => '#d93856',
                'border_color' => '#d93856',
                'url' => Route::has('hr.attendances.index') ? route('hr.attendances.index') : url('/hr/attendances'),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.quick-access');
    }
}
