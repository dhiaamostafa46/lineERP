<?php

namespace App\Exports;

use App\Models\Pilgrim;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomExport2 implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $items;
    public $header;
    public $style;

    public function __construct($items, $header, $style)
    {

        $this->items = $items;
        $this->header = $header;
        $this->style = $style;
    }

    public function collection()
    {
        return new Collection($this->items);
    }

    public function headings(): array
    {
        return $this->header;
    }

    public function map($item): array
    {
        return $item;
    }

    public function styles(Worksheet $sheet)
    {
        return $this->style;
        // [
        //     // Style the first row as bold text.
        //     1    => ['font' => ['bold' => true]],
        //     // Style the first column as bold text.
        //     'A'    => ['font' => ['bold' => true]],
        // ];
    }
}
