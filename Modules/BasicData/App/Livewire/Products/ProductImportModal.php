<?php

namespace Modules\BasicData\App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\ProductTemplateExport;
use Modules\BasicData\App\Imports\ProductsImport;

class ProductImportModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public $file = null;
    public array $failures = [];
    public bool $isSuccess = false;
    public int $importedCount = 0;
    public ?string $errorMessage = null;

    #[On('openImportModal')]
    public function openImport(): void
    {
        $this->reset(['file', 'failures', 'isSuccess', 'importedCount', 'errorMessage']);
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->reset(['file', 'failures', 'isSuccess', 'importedCount', 'errorMessage']);
        $this->resetErrorBag();
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport(), 'Product_Import_Template.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ], [
            'file.required' => 'يرجى اختيار ملف الإكسل للاستيراد.',
            'file.mimes' => 'يجب أن يكون الملف بصيغة xlsx, xls أو csv.',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 20 ميغابايت.',
        ]);

        $this->reset(['failures', 'errorMessage']);

        try {
            $import = new ProductsImport();
            Excel::import($import, $this->file);

            flash()->success(__('messages.imported', ['model' => __('basicdata::models/db_products.plural')]));
            $this->closeModal();
            return $this->redirect(route('basicdata.products.index'), navigate: true);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failuresList = [];
            foreach ($e->failures() as $failure) {
                $failuresList[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }
            $this->failures = $failuresList;
        } catch (\Exception $e) {
            $this->errorMessage = 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('basicdata::livewire.products.product-import-modal');
    }
}
