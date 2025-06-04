<?php

namespace App\Imports;

use App\Models\MOP_T_HMTRAIN_HOURS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HMTrainImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['jde_no'])) {
            return null; // Skip row if JDE_NO is empty
        }

        return new MOP_T_HMTRAIN_HOURS([
            'ID' => MOP_T_HMTRAIN_HOURS::max('ID') + 1,
            'JDE_NO' => $row['jde_no'],
            'EMPLOYEE_NAME' => $row['employee_name'] ?? null,
            'POSITION' => $row['position'] ?? null,
            'SITE' => $row['site'] ?? null,
            'DATE_ACTIVITY' => DB::raw("TO_TIMESTAMP('". Carbon::parse($row['date'])->format('Y-m-d H:i:s') ."', 'YYYY-MM-DD HH24:MI:SS')"),
            'TRAINING_TYPE' => $row['training_type'] ?? null,
            'UNIT_CLASS' => $row['unit_class'] ?? null,
            'UNIT_TYPE' => $row['unit_type'] ?? null,
            'CODE' => $row['code'] ?? null,
            'BATCH' => $row['batch'] ?? null,
            'HM_START' => $row['hm_start'] ?? null,
            'HM_END' => $row['hm_end'] ?? null,
            'TOTAL_HM' => $row['total_hm'] ?? null,
            'PLAN_TOTAL_HM' => $row['plan_total'] ?? null,
            'PROGRES' => $row['progress'] ?? null,
            'created_by' => Auth::user()->username,
        ]);
    }
}
