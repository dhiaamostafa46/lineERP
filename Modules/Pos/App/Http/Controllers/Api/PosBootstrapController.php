<?php

namespace Modules\Pos\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosSession;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\invApp\InvCustomer;

class PosBootstrapController extends Controller
{
    public function index(Request $request)
    {
        $deviceUuid = $request->header('X-Device-UUID');
        if (!$deviceUuid) {
            return response()->json(['error' => 'X-Device-UUID header is required'], 400);
        }

        $device = PosDevice::with(['branch', 'store', 'paymentMethods'])->where('uuid', $deviceUuid)->first();
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $user = auth()->user();
        
        $currentShift = PosSession::where('device_id', $device->id)
                                  ->where('user_id', $user->id)
                                  ->whereNull('closed_at')
                                  ->first();

        // Build the payload
        $data = [
            'device' => $device,
            'user_permissions' => [], // TODO: load permissions
            'current_shift' => $currentShift,
            'default_customer' => $device->default_customer_id ? InvCustomer::find($device->default_customer_id) : null,
            'tax_settings' => [
                'vat_rate' => 15, // Example
            ],
            'currency' => [
                'symbol' => 'SAR',
                'decimals' => 2
            ],
            'system_options' => [
                'allow_offline' => false
            ]
        ];

        $configHash = md5(json_encode($data));

        return response()->json([
            'version' => '1.0.0',
            'config_hash' => $configHash,
            'last_updated' => now()->toIso8601String(),
            'data' => $data
        ]);
    }
}
