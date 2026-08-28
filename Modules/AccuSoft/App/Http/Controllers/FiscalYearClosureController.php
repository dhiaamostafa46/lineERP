<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\FiscalYear;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\AccuSoft\App\Services\AccountingClosureService;

class FiscalYearClosureController extends Controller
{
    private AccountingClosureService $closureService;

    public function __construct(AccountingClosureService $closureService)
    {
        $this->closureService = $closureService;
    }

    /**
     * إقفال السنة المالية
     *
     * @param Request $request
     * @param int $fiscalYearId معرف السنة المالية
     * @return JsonResponse
     */
    public function close(Request $request, int $fiscalYearId): JsonResponse
    {
        try {
            $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

            if ($fiscalYear->is_closed) {
                return response()->json([
                    'success' => false,
                    'message' => 'السنة المالية مغلقة بالفعل',
                ], 409);
            }

            // الحصول على المستخدم المصرح
            $user = User::first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // الخيارات المطلوبة
            $options = [
                AccountingClosureService::OPT_AUTO_POST_DRAFTS => $request->boolean('auto_post_drafts', false),
            ];

            // تنفيذ الإقفال
            $closingEntry = DB::transaction(function () use ($fiscalYear, $user, $options) {
                return $this->closureService->closeFiscalYear($fiscalYear, $user, $options);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إقفال السنة المالية بنجاح',
                'data' => [
                    'fiscal_year_id' => $fiscalYear->id,
                    'closing_entry_id' => $closingEntry->id,
                    'closing_entry_number' => $closingEntry->entry_number,
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'السنة المالية غير موجودة',
            ], 404);

        } catch (\Exception $e) {
            Log::error("Fiscal Year Closure Error: ID {$fiscalYearId}", [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * التحقق من أهلية الإقفال
     *
     * @param int $fiscalYearId معرف السنة المالية
     * @return JsonResponse
     */
    public function checkClosureEligibility(int $fiscalYearId): JsonResponse
    {
        try {
            $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

            $validation = $this->closureService->validateClosureEligibility($fiscalYear);

            return response()->json([
                'success' => true,
                'data' => $validation,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'السنة المالية غير موجودة',
            ], 404);

        } catch (\Exception $e) {
            Log::error("Closure Eligibility Check Error: ID {$fiscalYearId}", [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
