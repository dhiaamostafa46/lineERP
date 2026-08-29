<?php

namespace Modules\BasicData\App\Http\Controllers\Concerns;

use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;

trait HasExportActions
{
    /**
     * Export resources to Excel (.xlsx)
     */
    public function excel()
    {
        $headers = $this->repository->header();
        $data = $this->repository->dataExel();
        $filename = ($this->exportFileName ?? 'export') . '.xlsx';

        return Excel::download(new BasicDataExport($data, $headers), $filename);
    }

    /**
     * Export resources to CSV (.csv)
     */
    public function csv()
    {
        $headers = $this->repository->header();
        $data = $this->repository->dataExel();
        $filename = ($this->exportFileName ?? 'export') . '.csv';

        return Excel::download(new BasicDataExport($data, $headers), $filename);
    }

    /**
     * Export resources to PDF (.pdf)
     */
    public function pdf()
    {
        $headers = $this->repository->header();
        $data = $this->repository->dataExel();
        $name = $this->repository->name();

        $mpdf = new Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;
        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDirectionality(app()->getLocale() === 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(view('basicdata::exports.pdf', [
            'headers' => $headers,
            'data' => $data,
            'name' => $name,
        ]));

        return $mpdf->Output();
    }
}
