<?php

namespace App\Imports;

use App\Models\MOP_T_MOP_HEADER;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MOPHeaderImport implements ToModel, WithHeadingRow //OnEachRow, WithValidation, WithHeadingsRow
{
    /**
     * @param array $row
     *
     * @return \|null
     */
    public function model(array $row)
    {
        $id = MOP_T_MOP_HEADER::max('id');
        $urut = abs($id + 1);

        // Point A Calculation
        $A = 0;
        $attendanceRatio = floatval($row['attendance_ratio']);
        if ($attendanceRatio <= 96) $A = 1;
        elseif ($attendanceRatio <= 97) $A = 2;
        elseif ($attendanceRatio <= 98) $A = 3;
        elseif ($attendanceRatio <= 99) $A = 4;
        else $A = 5;

        // Point B Calculation
        $B = match ($row['discipline']) {
            'Tidak Ada' => 5,
            'ST' => 4,
            'SP1' => 3,
            'SP2' => 2,
            'SP3' => 1,
            default => 0
        };

        // Point C Calculation
        $C = ($row['safety_awareness'] === 'Tidak Ada Insiden') ? 5 : 1;

        // Point D Calculation
        $D = 0;
        $whWaste = round(floatval($row['ewh']), 2);
        if ($whWaste < 3) $D = 1;
        elseif ($whWaste <= 4.5) $D = 2;
        elseif ($whWaste <= 6) $D = 3;
        elseif ($whWaste <= 7.5) $D = 4;
        else $D = 5;

        // Point E Calculation
        $E = 0;
        $pty = round(floatval($row['pty']), 2);
        if ($pty < 76) $E = 1;
        elseif ($pty <= 84) $E = 2;
        elseif ($pty <= 94) $E = 3;
        elseif ($pty <= 99) $E = 4;
        else $E = 5;

        // Calculate Point Eligibility (rounded to 2 decimal places)
        $point_eligibilitas = number_format((($A + $B + $C) / 3) * 0.3, 2, '.', '');

        // Conditional Calculation for Point Produksi based on 'MOP_TYPE'
        if ($row['mop_type'] === 'LOADER') {
            $point_produksi = number_format((($D + $E) / 2) * 0.7, 2, '.', '');
        } else {
            $point_produksi = number_format(($D * 0.7), 2, '.', '');
        }

        // Total Point Calculation (rounded to 2 decimal places)
        $total_point = number_format($point_eligibilitas + $point_produksi, 2, '.', '');


        // MOP Bulanan Grade Calculation
        $mop_bulanan_grade = match (true) {
            $total_point < 2 => "K",
            $total_point >= 2.0 && $total_point <= 2.49 => "C",
            $total_point >= 2.5 && $total_point <= 2.99 => "C+",
            $total_point >= 3.0 && $total_point <= 3.49 => "B",
            $total_point >= 3.5 && $total_point <= 3.99 => "B+",
            $total_point >= 4.0 && $total_point <= 4.49 => "BS",
            $total_point >= 4.5 && $total_point <= 4.75 => "BS+",
            $total_point > 4.75 => "ISTIMEWA",
            default => ""
        };

        // Insert data into the database
        return new MOP_T_MOP_HEADER([
            'ID' => $urut,
            'EMPLOYEE_NAME' => $row['employee_name'],
            'JDE_NO' => $row['jde_no'],
            'SITE' => $row['site'],
            'MOP_TYPE' => $row['mop_type'],
            'EQUIPMENT_TYPE1' => $row['equipment_desc'],
            'TARGET_AVG_HM' => $row['target_avg_hm'],
            'MONTH' => $row['month'],
            'YEAR' => $row['year'],
            'A_ATTENDANCE_RATIO' => $row['attendance_ratio'],
            'B_DISCIPLINE' => $row['discipline'],
            'C_SAFETY_AWARENESS' => $row['safety_awareness'],
            'D_WH_WASTE_EQUIPTYPE1' => number_format($row['ewh'], 2, '.', ''),
            'E_PTY_EQUIPTYPE1' => number_format($row['pty'], 2, '.', ''),
            'POINT_A' => $A,
            'POINT_B' => $B,
            'POINT_C' => $C,
            'POINT_D' => $D,
            'POINT_E' => $E,
            'POINT_ELIGIBILITAS' => $point_eligibilitas,
            'POINT_PRODUKSI' => $point_produksi,
            'TOTAL_POINT' => $total_point,
            'MOP_BULANAN_GRADE' => $mop_bulanan_grade,
        ]);
    }
}
