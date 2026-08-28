<?php

namespace Modules\AccuSoft\App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GenericViewExport implements FromView, ShouldAutoSize
{
    private $viewFile;
    private $viewData;

    public function __construct(string $viewFile, array $viewData = [])
    {
        $this->viewFile = $viewFile;
        $this->viewData = $viewData;
    }

    public function view(): View
    {
        // Adding an extra variable to distinguish if we are rendering for excel, to hide buttons/styling
        $this->viewData['is_excel'] = true;
        return view($this->viewFile, $this->viewData);
    }
}
