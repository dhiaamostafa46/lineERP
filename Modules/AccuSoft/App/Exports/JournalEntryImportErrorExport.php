<?php

namespace Modules\AccuSoft\App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalEntryImportErrorExport implements FromCollection, WithHeadings
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = collect($errors);
    }

    public function collection()
    {
        return $this->errors;
    }

    public function headings(): array
    {
        if ($this->errors->isEmpty()) {
            return [];
        }
        
        $firstRow = $this->errors->first();
        if ($firstRow instanceof \Illuminate\Support\Collection) {
            $headings = array_keys($firstRow->toArray());
        } else {
            $headings = array_keys((array)$firstRow);
        }

        if (!in_array('error_reason', $headings)) {
            $headings[] = 'error_reason';
        }
        return $headings;
        return $headings;
    }
}
