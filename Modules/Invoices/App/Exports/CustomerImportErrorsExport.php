<?php

namespace Modules\Invoices\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerImportErrorsExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->errors as $error) {
            $row = $error['row'];
            // Ensure the row has all columns (13 columns)
            $rowData = array_pad($row, 13, '');
            // Append the error message
            $rowData[] = $error['error'];
            $data[] = $rowData;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            __('invoices::models/inv_customers.fields.name') . ' (AR - EN)',
            __('invoices::models/inv_customers.fields.phone'),
            __('invoices::models/inv_customers.fields.email'),
            __('invoices::models/inv_customers.fields.vat_number'),
            __('invoices::models/inv_customers.fields.cr_number'),
            __('invoices::models/inv_customers.fields.country'),
            __('invoices::models/inv_customers.fields.city'),
            __('invoices::models/inv_customers.fields.district'),
            __('invoices::models/inv_customers.fields.street'),
            __('invoices::models/inv_customers.fields.building_number'),
            __('invoices::models/inv_customers.fields.postal_code'),
            __('invoices::models/inv_customers.fields.additional_number'),
            __('invoices::models/inv_customers.fields.credit_limit'),
            __('crud.error'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1416C'] // Red for errors
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(app()->getLocale() == 'ar');

                // Adjust column sizes
                foreach (range('A', 'N') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Highlight the error column
                $highestRow = $sheet->getHighestRow();
                if ($highestRow > 1) {
                    $sheet->getStyle('N2:N' . $highestRow)->getFont()->getColor()->setRGB('F1416C');
                }
            },
        ];
    }
}
