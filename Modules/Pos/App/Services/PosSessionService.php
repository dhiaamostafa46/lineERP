<?php

namespace Modules\Pos\App\Services;

use Modules\Pos\App\Models\PosSession;
use Modules\Pos\App\Models\PosDevice;
use Illuminate\Support\Str;

class PosSessionService
{
    public function openSession(PosDevice $device, $userId, $openingBalance)
    {
        $session = PosSession::create([
            'device_id' => $device->id,
            'user_id' => $userId,
            'opened_at' => now(),
            'opening_balance' => $openingBalance,
            'calculated_opening_balance' => $openingBalance,
            'status' => 'open',
            'browser_session_token' => Str::random(60)
        ]);

        return $session;
    }

    public function closeSession(PosSession $session, $actualBalance)
    {
        // Calculate expected balance here from transactions
        $calculatedBalance = $session->opening_balance; // Add cash sales, subtract refunds, add/sub movements

        $difference = $actualBalance - $calculatedBalance;

        $session->update([
            'closed_at' => now(),
            'closing_balance_actual' => $actualBalance,
            'closing_balance_calculated' => $calculatedBalance,
            'shortage_amount' => $difference < 0 ? abs($difference) : 0,
            'overage_amount' => $difference > 0 ? $difference : 0,
            'status' => 'closed',
        ]);

        return $session;
    }
}
