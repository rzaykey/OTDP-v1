<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function MOPIndex()
    {
        $mopheader = DB::connection('MSADMIN')->table('MOP_T_HEADER')->get();
        return view('data', compact('mopheader'));
    }

    public function MOPCreate()
    {
        $employees = DB::connection('MSADMIN')->table('V_EMP_ALL')->get();
        return view('form', compact('employees'));
    }

    public function MOPStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'jde' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'month' => 'nullable|string|max:2',
            'equipment_type1' => 'nullable|string|max:255',
            'equipment_type2' => 'nullable|string|max:255',
            'equipment_type3' => 'nullable|string|max:255',
            'equipment_type4' => 'nullable|string|max:255',
            'equipment_type5' => 'nullable|string|max:255',
            'equipment_type6' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'a_attendance_ratio' => 'nullable|string|max:255',
            'b_discipline' => 'nullable|string|max:255',
            'c_safety_awareness' => 'nullable|string|max:255',
            'd_wh_waste_equiptype1' =>'nullable|numeric|min:0',
            'd_wh_waste_equiptype2' =>'nullable|numeric|min:0',
            'd_wh_waste_equiptype3' =>'nullable|numeric|min:0',
            'd_wh_waste_equiptype4' =>'nullable|numeric|min:0',
            'd_wh_waste_equiptype5' =>'nullable|numeric|min:0',
            'd_wh_waste_equiptype6' =>'nullable|numeric|min:0',
            'e_pty_equiptype1' =>'nullable|numeric|min:0',
            'e_pty_equiptype2' =>'nullable|numeric|min:0',
            'e_pty_equiptype3' =>'nullable|numeric|min:0',
            'e_pty_equiptype4' =>'nullable|numeric|min:0',
            'e_pty_equiptype5' =>'nullable|numeric|min:0',
            'e_pty_equiptype6' =>'nullable|numeric|min:0',
            'point_eligibilitas' => 'nullable|numeric|min:0',
            'point_produksi' => 'nullable|numeric|min:0',
            'total_point' => 'nullable|numeric|min:0',
            'mop_bulanan_grade' => 'nullable|string|max:255',
        ]);

        $maxId = DB::connection('MSADMIN')
                ->table('MOP_T_HEADER')
                ->max('ID');

        $newId = $maxId + 1;

        $data = [
            'ID' => $newId,
            'jde_no' => $request->input('jde'),
            'employee_name' => $request->input('name'),
            'equipment_type1' => $request->input('equipment_type1'),
            'equipment_type2' => $request->input('equipment_type2'),
            'equipment_type3' => $request->input('equipment_type3'),
            'equipment_type4' => $request->input('equipment_type4'),
            'equipment_type5' => $request->input('equipment_type5'),
            'equipment_type6' => $request->input('equipment_type6'),
            // 'input_date' => $request->input('input_date'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'a_attendance_ratio' => $request->input('a_attendance_ratio'),
            'b_discipline' => $request->input('b_discipline'),
            'c_safety_awareness' => $request->input('c_safety_awareness'),
            'd_wh_waste_equiptype1' => $request->input('d_wh_waste_equiptype1'),
            'd_wh_waste_equiptype2' => $request->input('d_wh_waste_equiptype2'),
            'd_wh_waste_equiptype3' => $request->input('d_wh_waste_equiptype3'),
            'd_wh_waste_equiptype4' => $request->input('d_wh_waste_equiptype4'),
            'd_wh_waste_equiptype5' => $request->input('d_wh_waste_equiptype5'),
            'd_wh_waste_equiptype6' => $request->input('d_wh_waste_equiptype6'),
            'e_pty_equiptype1' => $request->input('e_pty_equiptype1'),
            'e_pty_equiptype2' => $request->input('e_pty_equiptype2'),
            'e_pty_equiptype3' => $request->input('e_pty_equiptype3'),
            'e_pty_equiptype4' => $request->input('e_pty_equiptype4'),
            'e_pty_equiptype5' => $request->input('e_pty_equiptype5'),
            'e_pty_equiptype6' => $request->input('e_pty_equiptype6'),
            'point_eligibilitas' => $request->input('point_eligibilitas'),
            'point_produksi' => $request->input('point_produksi'),
            'total_point' => $request->input('total_point'),
            'mop_bulanan_grade' => $request->input('mop_bulanan_grade'),
            // 'created_by' => $request->input('created_by'),
            // 'updated_by' => $request->input('updated_by'),
        ];

        // Insert data into the MOP_T_HEADER table on the MSADMIN connection
        DB::connection('MSADMIN')->table('MOP_T_HEADER')->insert($data);

        // Redirect or return a success message
        return redirect('/')->with('success', 'Operation Performance record created successfully.');
    }
}
