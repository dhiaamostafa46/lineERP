<?php

namespace App\Console\Commands;

use App\Models\BasicDataApp\Product;
use App\Models\Employee;
use App\Models\invApp\SalesInvoice;
use App\Models\NotificationItem;
use App\Models\Vehicles\Vehicle;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MohamedSabil83\LaravelHijrian\Facades\Hijrian;
use Modules\HR\App\Models\HrEmployee;

class NotificationsCommand extends Command
{
    protected $signature = 'notifications:process';
    protected $description = 'Process automated system-wide health checks and create notifications for HR, Vehicles, Inventory, and Invoices.';

    private const ALERT_DAYS = [30, 15, 7, 1];

    public function handle()
    {
        $this->info('🚀 Starting system-wide notifications processing...');

        $createdCount = 0;
        $createdCount += $this->processHrNotifications();
        $createdCount += $this->processVehicleNotifications();
        $createdCount += $this->processLowStockNotifications();
        $createdCount += $this->processInvoiceNotifications();

        $this->info("✅ Notifications processing completed. Total Created/Updated: {$createdCount} notifications.");
        return Command::SUCCESS;
    }

    /**
     * Process HR Expiries (Iqama, Insurance, Passport, License)
     */
    protected function processHrNotifications(): int
    {
        $this->info('👥 Processing HR document expiries...');
        $today = Carbon::today();
        $employees = Employee::with(['identity', 'hrEmployee'])->get();
        $count = 0;

        foreach ($employees as $employee) {
            $identity = $employee->identity;
            $hrEmployee = $employee->hrEmployee;

            // Identity / Iqama Expiry
            if ($identity && $identity->identity_expired_at) {
                if ($this->shouldCreateNotification($identity->identity_expired_at, $today)) {
                    // إرسال لمسؤولي HR (من لديهم صلاحية hr.documents.index)
                    NotificationService::sendToPermission(
                        'hr.documents.index',
                        NotificationItem::TYPE_IQAMA_EXPIRY,
                        "⚠️ انتهاء هوية/إقامة الموظف ({$employee->name})",
                        "تنتهي صلاحية الهوية للموظف {$employee->name} بتاريخ {$identity->identity_expired_at}. يرجى التجديد.",
                        $hrEmployee ?? $employee,
                        NotificationItem::PRIORITY_HIGH,
                        ['expiry_date' => $identity->identity_expired_at]
                    );
                    $count++;
                } else {
                    NotificationService::cancel(NotificationItem::TYPE_IQAMA_EXPIRY, $hrEmployee ?? $employee);
                }
            }

            // Insurance Expiry
            if ($identity && $identity->insurance_expired_at) {
                if ($this->shouldCreateNotification($identity->insurance_expired_at, $today)) {
                    NotificationService::sendToPermission(
                        'hr.documents.index',
                        NotificationItem::TYPE_INSURANCE_EXPIRY,
                        "⚠️ انتهاء التأمين الطبي للموظف ({$employee->name})",
                        "ينتهي التأمين الطبي للموظف {$employee->name} بتاريخ {$identity->insurance_expired_at}. يرجى التجديد.",
                        $hrEmployee ?? $employee,
                        NotificationItem::PRIORITY_HIGH,
                        ['expiry_date' => $identity->insurance_expired_at]
                    );
                    $count++;
                } else {
                    NotificationService::cancel(NotificationItem::TYPE_INSURANCE_EXPIRY, $hrEmployee ?? $employee);
                }
            }

            // Driver License Expiry on HR Employee
            if ($hrEmployee && isset($hrEmployee->license_expired_at) && $hrEmployee->license_expired_at) {
                if ($this->shouldCreateNotification($hrEmployee->license_expired_at, $today)) {
                    NotificationService::sendToPermission(
                        'hr.documents.index',
                        NotificationItem::TYPE_DRIVER_LICENSE_EXPIRY,
                        "🪪 انتهاء رخصة قيادة الموظف ({$employee->name})",
                        "تنتهي رخصة القيادة للموظف {$employee->name} بتاريخ {$hrEmployee->license_expired_at}. يرجى التجديد.",
                        $hrEmployee,
                        NotificationItem::PRIORITY_HIGH,
                        ['expiry_date' => $hrEmployee->license_expired_at]
                    );
                    $count++;
                } else {
                    NotificationService::cancel(NotificationItem::TYPE_DRIVER_LICENSE_EXPIRY, $hrEmployee);
                }
            }
        }

        return $count;
    }

    /**
     * Process Fleet & Vehicles Expiries
     */
    protected function processVehicleNotifications(): int
    {
        $this->info('🚚 Processing Vehicles license expiries...');
        $today = Carbon::today();
        $count = 0;

        if (!Schema::hasTable('vehicles')) {
            return 0;
        }

        $vehicles = Vehicle::whereNotNull('license_expiry_date')->get();
        foreach ($vehicles as $vehicle) {
            if ($this->shouldCreateNotification($vehicle->license_expiry_date, $today)) {
                $plate = $vehicle->plate ?? "{$vehicle->plate_letters} {$vehicle->plate_numbers}";
                NotificationService::sendToPermission(
                    'vc.vehicles.index',
                    NotificationItem::TYPE_VEHICLE_LICENSE_EXPIRY,
                    "🚗 انتهاء رخصة المركبة ({$plate})",
                    "تنتهي رخصة استمارة المركبة ذات اللوحة {$plate} بتاريخ {$vehicle->license_expiry_date}. يرجى التجديد.",
                    $vehicle,
                    NotificationItem::PRIORITY_HIGH,
                    ['license_expiry_date' => $vehicle->license_expiry_date, 'plate' => $plate]
                );
                $count++;
            } else {
                NotificationService::cancel(NotificationItem::TYPE_VEHICLE_LICENSE_EXPIRY, $vehicle);
            }
        }

        return $count;
    }

    /**
     * Process Low Stock threshold notifications
     */
    protected function processLowStockNotifications(): int
    {
        $this->info('📦 Processing Low Stock alerts...');
        $count = 0;

        if (!Schema::hasTable('products')) {
            return 0;
        }

        $products = Product::whereNotNull('min_quantity')->where('min_quantity', '>', 0)->get();
        foreach ($products as $product) {
            $totalStock = DB::table('stocks')->where('product_id', $product->id)->sum('current_quantity') ?? 0;
            if ($totalStock <= $product->min_quantity) {
                NotificationService::sendToPermission(
                    'store.stores.index',
                    NotificationItem::TYPE_LOW_STOCK,
                    "⚠️ تنبيه انخفاض المخزون للمنتج ({$product->name})",
                    "وصل المخزون الحرج للمنتج '{$product->name}' (الكمية المتوفرة حالياً: {$totalStock}، حد الأمان: {$product->min_quantity}).",
                    $product,
                    NotificationItem::PRIORITY_HIGH,
                    ['current_stock' => $totalStock, 'min_quantity' => $product->min_quantity]
                );
                $count++;
            } else {
                NotificationService::cancel(NotificationItem::TYPE_LOW_STOCK, $product);
            }
        }

        return $count;
    }

    /**
     * Process Invoices status notifications
     */
    protected function processInvoiceNotifications(): int
    {
        $this->info('🧾 Processing Invoices notifications...');
        $count = 0;

        if (!Schema::hasTable('sales_invoices')) {
            return 0;
        }

        // Draft or unsubmitted invoices older than 7 days
        $pendingInvoices = SalesInvoice::where('status', SalesInvoice::STATUS_DRAFT)
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->get();

        foreach ($pendingInvoices as $invoice) {
            $invNo = $invoice->invoice_number ?? $invoice->id;
            NotificationService::send(
                NotificationItem::TYPE_INVOICE_DUE,
                "⚠️ مسودة فاتورة مبيعات معلقة رقم (#{$invNo})",
                "الفاتورة رقم #{$invNo} لا تزال مسودة منذ أكثر من 7 أيام. المبلغ الإجمالي: {$invoice->total_inclusive_vat}",
                $invoice,
                $invoice->created_by ?? null,
                NotificationItem::CHANNEL_DATABASE,
                ['invoice_number' => $invNo, 'amount' => $invoice->total_inclusive_vat]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Validate whether an expiry date requires an active notification
     */
    protected function shouldCreateNotification($expiryDate, Carbon $today): bool
    {
        if (!$expiryDate || trim((string)$expiryDate) === '') {
            return false;
        }

        $expiryCarbon = null;

        try {
            if (is_string($expiryDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiryDate)) {
                try {
                    $expiryCarbon = Hijrian::gregory($expiryDate);
                } catch (\Exception $e) {
                    $expiryCarbon = Carbon::parse($expiryDate);
                }
            } elseif ($expiryDate instanceof Carbon) {
                $expiryCarbon = $expiryDate;
            } else {
                $expiryCarbon = Carbon::parse($expiryDate);
            }
        } catch (\Exception $e) {
            return false;
        }

        if (!($expiryCarbon instanceof Carbon)) {
            $expiryCarbon = Carbon::parse((string) $expiryCarbon);
        }

        $daysRemaining = $today->diffInDays($expiryCarbon, false);

        if ($daysRemaining < 0) {
            return true;
        }

        foreach (self::ALERT_DAYS as $alertDay) {
            if ($daysRemaining <= $alertDay && $daysRemaining >= 0) {
                return true;
            }
        }

        return false;
    }
}
