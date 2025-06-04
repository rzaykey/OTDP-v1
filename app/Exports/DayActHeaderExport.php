<?php

namespace App\Exports;

use App\Models\MOP_T_TRAINER_DAILY_ACTIVITY;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DayActHeaderExport implements FromCollection, WithHeadings
{
    protected $selectedColumns;
    protected $fromDate;
    protected $toDate;

    public function __construct($selectedColumns, $fromDate, $toDate)
    {
        $this->selectedColumns = $selectedColumns;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

    }

    public function collection()
    {
        // \Log::info('From Date: ' . $this->fromDate . ', To Date: ' . $this->toDate);

        $query = MOP_T_TRAINER_DAILY_ACTIVITY::query();

        if ($this->fromDate && $this->toDate) {
            $query->whereBetween('date_activity', [$this->fromDate, $this->toDate]);
        }

        $records = $query->select($this->selectedColumns)->get();

        $finalCollection = $records->map(function ($item) {
            $data = [];

            foreach ($this->selectedColumns as $col) {
                if ($col === 'date_activity') {
                    $data[$col] = date('d-m-Y', strtotime($item[$col]));
                    $date = $item[$col];
                    $data['Week'] = $date ? date('W', strtotime($date)) : '';
                    $data['Month'] = $date ? date('F', strtotime($date)) : '';
                    $data['Year'] = $date ? date('Y', strtotime($date)) : '';
                } else {
                    $data[$col] = $item[$col];
                }
            }

            return $data;
        });

        return new Collection($finalCollection);
    }

    public function headings(): array
    {
        $headings = [];

        foreach ($this->selectedColumns as $col) {
            $headings[] = $col;
            if ($col === 'date_activity') {
                $headings[] = 'Week';
                $headings[] = 'Month';
                $headings[] = 'Year';
            }
        }

        return $headings;
    }
}
