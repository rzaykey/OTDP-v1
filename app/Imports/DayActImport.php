<?php

namespace App\Imports;

use App\Models\MOP_T_TRAINER_DAILY_ACTIVITY;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DayActImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['jde_no'])) {
            return null; // Skip row if JDE_NO is empty
        }

        return new MOP_T_TRAINER_DAILY_ACTIVITY([
            'ID' => MOP_T_TRAINER_DAILY_ACTIVITY::max('ID') + 1,
            'JDE_NO' => $row['jde_no'],
            'EMPLOYEE_NAME' => $row['employee_name'] ?? null,
            'SITE' => $row['site'] ?? null,
            'DATE_ACTIVITY' => isset($row['date_activity']) ? Carbon::parse($row['date_activity'])->format('Y-m-d H:i:s') : null,
            'KPI_TYPE' => $row['kpi_type'] ?? null,
            'ACTIVITY' => $row['activity'] ?? null,
            'UNIT_DETAIL' => $row['unit_detail'] ?? null,
            'TOTAL_PARTICIPANT' => $row['total_participant'] ?? null,
            'TOTAL_HOUR' => $row['total_hour'] ?? null,
            'CREATED_BY' => Auth::user()->username,
            'UPDATED_BY' => Auth::user()->username,
        ]);
    }
}
