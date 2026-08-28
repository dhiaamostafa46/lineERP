<?php

namespace App\Http\Controllers;

use App\Models\DeviceSession;
use App\Repositories\DeviceSessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceSessionController extends Controller
{
    private $DeviceSessionRepository;

    public function __construct(DeviceSessionRepository $DeviceSessionRepository)
    {
        $this->DeviceSessionRepository = $DeviceSessionRepository;
    }

    /**
     * عرض جميع الجلسات للمستخدم الحالي
     */

    public function index(Request $request)
    {

        //dd($request->all());
        // $data['DeviceSessions'] = $this->DeviceSessionRepository->paginate(10);
        $data['DeviceSessions'] = $this->DeviceSessionRepository->allQuery($request->except('pagination'))->latest()->paginate($request->pagination ?? 10);
        $data['statuses'] = $this->DeviceSessionRepository->statuses();
        $data['users'] = $this->DeviceSessionRepository->users();
        return view('DeviceSessions.index', $data);
    }

    public function show($id)
    {
        // البحث عن الجلسة
        $DeviceSession = $this->DeviceSessionRepository->find($id);

        if (empty($DeviceSession)) {
            flash()->error(__('models/DeviceSessions.singular') . ' ' . __('messages.not_found'));
            return redirect()->route('DeviceSessions.index');
        }

        // تبديل حالة التفعيل
        $DeviceSession->is_active = !$DeviceSession->is_active;
        $DeviceSession->save();

        // رسالة نجاح
        flash()->success(__('messages.updated', ['model' => __('models/DeviceSessions.singular')]));

        return redirect()->route('DeviceSessions.index');
    }

    public function update(Request $request, $id)
    {
        $DeviceSession = $this->DeviceSessionRepository->find($id);

       if (empty($DeviceSession)) {
            flash()->error(__('models/DeviceSessions.singular') . ' ' . __('messages.not_found'));
            return redirect()->route('DeviceSessions.index');
        }

        // مثال لتحديث بعض الحقول
        $DeviceSession->is_active = !$DeviceSession->is_active;
        $DeviceSession->save();

       flash()->success(__('messages.updated', ['model' => __('models/DeviceSessions.singular')]));
        return redirect()->route('DeviceSessions.index');
    }
    /**
     * حذف جلسة محددة
     */
    public function destroy($id)
    {
        $employee = $this->DeviceSessionRepository->find($id);

        if (empty($employee)) {
            flash()->error(__('models/employees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('employees.index'));
        }

        $this->DeviceSessionRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/employees.singular')]));

        return redirect(route('DeviceSessions.index'));
    }
}
