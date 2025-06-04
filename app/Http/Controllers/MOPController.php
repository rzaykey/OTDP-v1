<?php

namespace App\Http\Controllers;

use App\Exports\MOPHeaderExport;
use App\Imports\MOPHeaderImport;
use App\Models\MOP_T_MOP_HEADER;
use App\Models\MOP_V_MOP_COMPILE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class MOPController extends Controller
{
    public function MOPIndex()
    {
        return view('pages.mop.mopindex');
    }

    public function MOPData(Request $request)
    {
        $mopheader = MOP_T_MOP_HEADER::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('jde_no', 'asc')
            ->get();

        return response()->json(['data' => $mopheader]);
    }

    public function MOPDataSimple(Request $request)
    {
        $query = MOP_T_MOP_HEADER::select(
            'site',
            'jde_no',
            'employee_name',
            'equipment_type1',
            'month',
            'year',
            'point_a',
            'point_b',
            'point_c',
            'point_d',
            'point_e',
            'point_eligibilitas',
            'point_produksi',
            'total_point',
            'mop_bulanan_grade'
        );

        if ($request->filled('site')) {
            $query->where('site', $request->site);
        }
        if ($request->filled('jde_no')) {
            $query->where('jde_no', $request->jde_no);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('equipment_type1')) {
            $query->where('equipment_type1', $request->equipment_type1);
        }

        // Clone query for totals
        $queryForCount = clone $query;

        // Fetch filtered result
        $mopcompile = $query
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('employee_name', 'asc')
            ->get();

        // Calculate totals
        $total_employee = $queryForCount->distinct('employee_name')->count('employee_name');
        $total_data = $queryForCount->count();

        // Calculate the average of 'total_point'
        // $total_average = $queryForCount
        //     ->selectRaw('avg(total_point) as average_total_point')  // Calculate average of total_point
        //     ->groupBy(
        //         'site',
        //         'jde_no',
        //         'employee_name',
        //         'equipment_type1',
        //         'month',
        //         'year',
        //         'point_a',
        //         'point_b',
        //         'point_c',
        //         'point_d',
        //         'point_e',
        //         'point_eligibilitas',
        //         'point_produksi',
        //         'mop_bulanan_grade'
        //     )
        //     ->get();

        return response()->json([
            'data' => $mopcompile,
            'total_employee' => $total_employee,
            'total_data' => $total_data,
            // 'total_average'
        ]);
    }



    public function MOPDataCompile(Request $request)
    {
        $mopcompile = MOP_V_MOP_COMPILE::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('jde_no', 'asc')
            ->get();

        return response()->json($mopcompile);
    }


    public function MOPCreate()
    {
        return view('pages.mop.mopcreate');
    }

    public function MOPStore(Request $request)
    {
        // return $request;
        // Validate the request

        try {
            $maxId = DB::connection('MSADMIN')
                ->table('MOP_T_MOP_HEADER')
                ->max('ID');

            $newId = $maxId + 1;

            $data = [
                'ID' => $newId,
                'jde_no' => $request->input('jde'),
                'employee_name' => $request->input('name'),
                'equipment_type1' => $request->input('equipment_type1'),
                'site' => $request->input('site'),
                'mop_type' => $request->input('moptype'),
                // 'equipment_type2' => $request->input('equipment_type2'),
                // 'equipment_type3' => $request->input('equipment_type3'),
                // 'equipment_type4' => $request->input('equipment_type4'),
                // 'equipment_type5' => $request->input('equipment_type5'),
                // 'equipment_type6' => $request->input('equipment_type6'),
                // 'input_date' => $request->input('input_date'),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
                'a_attendance_ratio' => $request->input('a_attendance_ratio'),
                'b_discipline' => $request->input('b_discipline'),
                'c_safety_awareness' => $request->input('c_safety_awareness'),
                'd_wh_waste_equiptype1' => $request->input('d_wh_waste_equiptype1'),
                // 'd_wh_waste_equiptype2' => $request->input('d_wh_waste_equiptype2'),
                // 'd_wh_waste_equiptype3' => $request->input('d_wh_waste_equiptype3'),
                // 'd_wh_waste_equiptype4' => $request->input('d_wh_waste_equiptype4'),
                // 'd_wh_waste_equiptype5' => $request->input('d_wh_waste_equiptype5'),
                // 'd_wh_waste_equiptype6' => $request->input('d_wh_waste_equiptype6'),
                'e_pty_equiptype1' => $request->input('e_pty_equiptype1'),
                // 'e_pty_equiptype2' => $request->input('e_pty_equiptype2'),
                // 'e_pty_equiptype3' => $request->input('e_pty_equiptype3'),
                // 'e_pty_equiptype4' => $request->input('e_pty_equiptype4'),
                // 'e_pty_equiptype5' => $request->input('e_pty_equiptype5'),
                // 'e_pty_equiptype6' => $request->input('e_pty_equiptype6'),
                'point_eligibilitas' => $request->input('point_eligibilitas'),
                'point_produksi' => $request->input('point_produksi'),
                'total_point' => $request->input('total_point'),
                'mop_bulanan_grade' => $request->input('mop_bulanan_grade'),
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,

            ];

            // Insert data into the MOP_T_HEADER table on the MSADMIN connection
            DB::connection('MSADMIN')->table('MOP_T_MOP_HEADER')->insert($data);

            // Redirect or return a success message
            return redirect()->route('MOPCreate')->with('success', 'Operation Performance record created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in MOPStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data, harap menghubungi admin atau IT'], 500);
        }
    }

    public function MOPImport(Request $request)
    {
        // $request->validate([
        //     'import_file' => 'required|file|mimes:xlsx,xls,csv'
        // ]);

        if ($request->hasFile('import_file')) {
            try {
                Excel::import(new MOPHeaderImport, $request->file('import_file'));
                return response()->json(['success' => true, 'message' => 'Import successful.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
            }
        }
        return response()->json(['success' => false, 'message' => 'No file uploaded.']);
    }

    public function MOPExport(Request $request)
    {
        $selectedColumns = $request->input('columns', [
            'jde_no',
            'employee_name',
            'equipment_type1',
            'equipment_type2',
            'equipment_type3',
            'month',
            'year',
            'a_attendance_ratio',
            'b_discipline',
            'c_safety_awareness',
            'd_wh_waste_equiptype1',
            'd_wh_waste_equiptype2',
            'd_wh_waste_equiptype3',
            'e_pty_equiptype1',
            'e_pty_equiptype2',
            'e_pty_equiptype3',
            'point_eligibilitas',
            'point_produksi',
            'total_point',
            'mop_bulanan_grade'
        ]);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fileName = $request->input('file_name') ?: 'MOPHeaderData';

        return Excel::download(new MOPHeaderExport($selectedColumns, $fromDate, $toDate), $fileName . '.xlsx');
    }

    public function MOPImportTemplate()
    {
        $filePath = 'templates/MOP_Import_Template.xlsx';

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Template file not found.');
        }

        return Storage::download($filePath, 'MOP_Import_Template.xlsx');
    }

    public function MOPDashboard()
    {
        return view('pages.mop.mopDashboard');
    }
}
