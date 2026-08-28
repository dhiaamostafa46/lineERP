<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasBulkActions
{
    /**
     * Universal Bulk Delete Action for any resource controller.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required',
        ]);

        $ids = $request->input('ids');
        $repository = property_exists($this, 'repository') 
            ? $this->repository 
            : (isset($this->dbProductRepository) ? $this->dbProductRepository : null);

        // Auto-detect repository from properties
        if (!$repository) {
            foreach (get_object_vars($this) as $prop) {
                if ($prop instanceof \App\Repositories\BaseRepository) {
                    $repository = $prop;
                    break;
                }
            }
        }

        if (!$repository) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر العثور على مستودع البيانات المناسب للحذف.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $deletedCount = 0;
            foreach ($ids as $id) {
                $repository->delete($id);
                $deletedCount++;
            }
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "تم حذف {$deletedCount} عنصر بنجاح.",
                    'count' => $deletedCount
                ]);
            }

            flash()->success("تم حذف {$deletedCount} عنصر بنجاح.");
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف الجماعي: ' . $e->getMessage()
                ], 500);
            }

            flash()->error('حدث خطأ أثناء الحذف الجماعي: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
