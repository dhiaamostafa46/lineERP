<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Modules\Pos\App\Models\PosDevice;

class VerifyPosDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $deviceUuid = $request->header('X-Device-UUID');

        if (!$deviceUuid) {
            return response()->json(['status' => false, 'message' => 'Missing X-Device-UUID header'], 400);
        }

        $device = PosDevice::where('uuid', $deviceUuid)->first();

        if (!$device) {
            return response()->json(['status' => false, 'message' => 'Device not found'], 404);
        }

        if (!$device->is_active) {
            return response()->json(['status' => false, 'message' => 'Device is inactive'], 403);
        }

        // Verify user is linked if the device requires it
        if ($device->is_users_linked && $request->user()) {
            $linkedUsers = is_array($device->linked_users) ? $device->linked_users : (json_decode($device->linked_users, true) ?? []);
            if (!in_array($request->user()->id, $linkedUsers)) {
                return response()->json(['status' => false, 'message' => 'User is not authorized for this device'], 403);
            }
        }

        // Add device to request for downstream use if needed
        $request->attributes->set('pos_device', $device);

        return $next($request);
    }
}
