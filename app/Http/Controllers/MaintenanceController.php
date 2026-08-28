<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\InvSupplier;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function syncAccounts()
    {
        $report = [
            'customers' => [
                'created' => 0,
                'skipped' => 0,
                'errors' => []
            ],
            'suppliers' => [
                'created' => 0,
                'skipped' => 0,
                'errors' => []
            ]
        ];

        // 1. Sync Customers (account_type = 11)
        $customerAccounts = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_CUSTOMERS)
            ->where('is_leaf', true)
            ->get();

        foreach ($customerAccounts as $account) {
            try {
                $exists = InvCustomer::where('tree_account_id', $account->id)->exists();

                if ($exists) {
                    $report['customers']['skipped']++;
                    continue;
                }

                $data = [
                    'tree_account_id' => $account->id,
                    'branch_id' => $account->branch_id,
                    'status' => 1,
                ];

                foreach (['ar', 'en'] as $locale) {
                    $translation = $account->translate($locale);
                    if ($translation && $translation->name) {
                        $data[$locale] = ['name' => $translation->name];
                    }
                }

                // Fallback name if translations are missing
                if (!isset($data['ar']) && !isset($data['en'])) {
                    $data['ar'] = ['name' => 'Customer ' . $account->id];
                }

                InvCustomer::create($data);
                $report['customers']['created']++;

            } catch (\Exception $e) {
                $report['customers']['errors'][] = "Account ID {$account->id}: " . $e->getMessage();
            }
        }

        // 2. Sync Suppliers (account_type = 7)
        $supplierAccounts = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_SUPPLIERS)
            ->where('is_leaf', true)
            ->get();

        foreach ($supplierAccounts as $account) {
            try {
                $exists = InvSupplier::where('tree_account_id', $account->id)->exists();

                if ($exists) {
                    $report['suppliers']['skipped']++;
                    continue;
                }

                $data = [
                    'tree_account_id' => $account->id,
                    'branch_id' => $account->branch_id,
                    'status' => 1,
                ];

                foreach (['ar', 'en'] as $locale) {
                    $translation = $account->translate($locale);
                    if ($translation && $translation->name) {
                        $data[$locale] = ['name' => $translation->name];
                    }
                }

                // Fallback name if translations are missing
                if (!isset($data['ar']) && !isset($data['en'])) {
                    $data['ar'] = ['name' => 'Supplier ' . $account->id];
                }

                InvSupplier::create($data);
                $report['suppliers']['created']++;

            } catch (\Exception $e) {
                $report['suppliers']['errors'][] = "Account ID {$account->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sync completed successfully.',
            'report' => $report
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
