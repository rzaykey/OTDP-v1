<?php

namespace App\Exports;

use App\Models\MOP_T_MOP_HEADER;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class MOPHeaderExport implements FromCollection, WithHeadings
{
    protected $selectedColumns;
    protected $fromDate;
    protected $toDate;

    public function __construct($selectedColumns, $fromDate, $toDate)
    {
        $this->selectedColumns = $selectedColumns;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        // dd($fromDate);
    }

    public function collection()
    {
        $query = MOP_T_MOP_HEADER::query();

        if ($this->fromDate && $this->toDate) {
            $query->whereRaw("(year || '-' || LPAD(month, 2, '0')) BETWEEN ? AND ?", [
                $this->fromDate,
                $this->toDate
            ]);
        }

        return $query->select($this->selectedColumns)->get();
    }

    public function headings(): array
    {
        return $this->selectedColumns;
    }
}
