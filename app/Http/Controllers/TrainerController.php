<?php

namespace App\Http\Controllers;

use App\Exports\DayActHeaderExport;
use App\Imports\DayActImport;
use App\Exports\HMTrainExport;
use App\Imports\HMTrainImport;
use App\Models\MOP_T_HMTRAIN_HOURS;
use App\Models\MOP_T_TRAINER_DAILY_ACTIVITY;
use App\Models\MOP_M_ACTIVITY;
use App\Models\MOP_M_MODEL_UNIT;
use App\Models\MOP_M_TYPE_UNIT;
use App\Models\MOP_M_UNIT;
use App\Models\PROINT_EMPLOYEE;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;



use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function DayActIndex()
    {

        $data = MOP_T_TRAINER_DAILY_ACTIVITY::orderBy('date_activity', 'desc')
            ->limit(100)->get();

        $activity = MOP_M_ACTIVITY::get();
        $unit = MOP_M_MODEL_UNIT::get();

        return view('pages.trainer.dayactindex', [
            'data'           => $data,
            'getActivity'    => $activity,
            'getUnit'        => $unit,
        ]);
    }

    public function DayActData()
    {

        $dayact = MOP_T_TRAINER_DAILY_ACTIVITY::get();

        return response()->json(['data' => $dayact]);
    }

    public function DayActCreate()
    {
            //  return Auth::user();
        $employeeAuth = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();
        // return $employeeAuth;
        return view('pages.trainer.dayactcreate', ['employeeAuth' => $employeeAuth]);
    }

    public function DayActStore(Request $request)
    {
        try {
            $maxId = MOP_T_TRAINER_DAILY_ACTIVITY::max('ID');
            $newId = $maxId + 1;

            $data = [
                'ID' => $newId,
                'jde_no' => $request->input('JDE'),
                'employee_name' => $request->input('name'),
                'site' => $request->input('site'),
                'date_activity' => $request->input('date'),
                'kpi_type' => $request->input('KPI'),
                'activity' => $request->input('activity'),
                'unit_detail' => $request->input('unit_detail'),
                'total_participant' => $request->input('jml_peserta'),
                'total_hour' => $request->input('total_hour'),
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ];

            MOP_T_TRAINER_DAILY_ACTIVITY::insert($data);

            // Redirect or return a success message
            return redirect()->route('DayActIndex')->with('success', 'Operation Performance record created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in MOPStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data, harap menghubungi admin atau IT'], 500);
        }
    }

    public function DayActDelete($id)
    {
        try {
            $record = MOP_T_TRAINER_DAILY_ACTIVITY::findOrFail($id);
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the record.'
            ], 500);
        }
    }

    public function DayActEdit($id)
    {
        $data = MOP_T_TRAINER_DAILY_ACTIVITY::find($id);
        return $data;
        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function DayActUpdate(Request $request)
    {
        // dd($request->all());
        // return $request;
        $data = MOP_T_TRAINER_DAILY_ACTIVITY::find($request->input('edit_id'));

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update([
            'JDE_NO' => $request->input('edit_jde'),
            'EMPLOYEE_NAME' => $request->input('edit_name'),
            'SITE' => $request->input('edit_site'),
            'DATE_ACTIVITY' => $request->input('edit_date'),
            'KPI_TYPE' => $request->input('edit_kpi'),
            'ACTIVITY' => $request->input('edit_activity'),
            'UNIT_DETAIL' => $request->input('edit_unit_detail'),
            'TOTAL_PARTICIPANT' => $request->input('edit_jml_peserta'),
            'TOTAL_HOUR' => $request->input('edit_total_hour'),
            'UPDATED_BY' => Auth::user()->username,
        ]);

        return response()->json(['success' => true]);
    }


    public function DayActExport(Request $request)
    {
        $selectedColumns = $request->input('columns', [
            'JDE',
            'Nama',
            'site',
            'Date',
            'Jenis KPI',
            'Aktivitas',
            'Unit Detail',
            'Jumlah Peserta',
            'Total Hours',
        ]);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fileName = $request->input('file_name') ?: 'Record Daily Activity Trainer';

        return Excel::download(new DayActHeaderExport($selectedColumns, $fromDate, $toDate), $fileName . '.xlsx');
    }

    public function DayActImport(Request $request)
    {

        if ($request->hasFile('import_file')) {
            try {
                Excel::import(new DayActImport, $request->file('import_file'));
                return response()->json(['success' => true, 'message' => 'Import successful.']);
            } catch (\Exception $e) {
                response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
            }
        }
        return response()->json(['success' => false, 'message' => 'No file uploaded.']);
    }

    public function DayActImportTemplate()
    {
        $filePath = 'templates/MOP_Import_Template.xlsx';

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Template file not found.');
        }

        return Storage::download($filePath, 'MOP_Import_Template.xlsx');
    }


    public function HMTrainIndex()
    {
        $data = MOP_T_HMTRAIN_HOURS::orderBy('date_activity', 'desc')
            ->limit(100)->get();

        $typeUnit = MOP_M_TYPE_UNIT::select('class')->distinct()->orderby('class')->get();
        $classUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
                ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.TYPE', '=', 'b.TYPE')
                ->select('a.id', 'a.model', 'a.type', 'b.class')->get();
        $code = MOP_M_UNIT::get();

        return view('pages.trainer.trainhoursindex', [
            'data'      => $data,
            'getTypeUnit'    => $typeUnit,
            'getClassUnit'    => $classUnit,
            'getCode'    => $code,
        ]);
    }

    public function HMTrainData()
    {
        $train = MOP_T_HMTRAIN_HOURS::orderBy('date_activity', 'desc')
            ->get();

        return response()->json(['data' => $train]);
    }

    public function HMTrainCreate()
    {
        $employeeAuth = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();
        return view('pages.trainer.trainhourscreate', ['employeeAuth' => $employeeAuth]);
    }

    public function HMTrainStore(Request $request)
    {
        try {
            $maxId = MOP_T_HMTRAIN_HOURS::max('ID');
            $newId = ($maxId ?? 0) + 1;

            $data = [
                'ID' => $newId,
                'jde_no' => $request->input('JDE'),
                'employee_name' => $request->input('name'),
                'position' => $request->input('position'),
                'site' => $request->input('site'),
                'date_activity' => $request->input('date'),
                'training_type' => $request->input('train_type'),
                'unit_class' => $request->input('unit_class'),
                'unit_type' => $request->input('unit_type'),
                'code' => $request->input('code'),
                'batch' => $request->input('batch'),
                'hm_start' => $request->input('HM_start'),
                'hm_end' => $request->input('HM_end'),
                'total_hm' => $request->input('total_HM'),
                'plan_total_hm' => $request->input('plan_total'),
                'progres' => $request->input('progress'),
                'created_by' => Auth::user()->username,
                // 'updated_by' => $request->input('updated_by'),
            ];

            MOP_T_HMTRAIN_HOURS::insert($data);

            MOP_T_HMTRAIN_HOURS::where('jde_no', $request->input('JDE'),)
            ->where('training_type', $request->input('train_type'))
            ->where('unit_class', $request->input('unit_class'))
            ->where('batch', $request->input('batch'))
            ->update(['progres' => $request->input('progress')]);

            // Redirect or return a success message
            return redirect()->route('HMTrainIndex')->with('success', 'Train Hours record created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in TrainStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data, harap menghubungi admin atau IT'], 500);
        }
    }

    public function HMTrainDelete($id)
    {
        try {
            $record = MOP_T_HMTRAIN_HOURS::findOrFail($id);

            $jde = $record->jde_no;
            $trainType = $record->training_type;
            $unitClass = $record->unit_class;
            $batch = $record->batch;

            $record->delete();

            // if record deleted then progress diupdate lagi
            $newProgres = MOP_T_HMTRAIN_HOURS::where('jde_no', $jde)
                ->where('training_type', $trainType)
                ->where('unit_class', $unitClass)
                ->where('batch', $batch)
                ->sum('total_hm');

            MOP_T_HMTRAIN_HOURS::where('jde_no', $jde)
            ->where('training_type', $trainType)
            ->where('unit_class', $unitClass)
            ->where('batch', $batch)
            ->update(['progres' => $newProgres]);

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the record.'
            ], 500);
        }
    }

    public function HMTrainEdit($id)
    {
        $data = MOP_T_HMTRAIN_HOURS::find($id);
        return $data;
        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function HMTrainUpdate(Request $request)
    {
        // dd($request->all());
        // return $request;
        $data = MOP_T_HMTRAIN_HOURS::find($request->input('edit_id'));

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update([
            'JDE_NO' => $request->input('edit_jde'),
            'EMPLOYEE_NAME' => $request->input('edit_name'),
            'SITE' => $request->input('edit_site'),
            'DATE_ACTIVITY' => $request->input('edit_date'),
            'POSITION' => $request->input('edit_position'),
            'TRAINING_TYPE' => $request->input('edit_train_type'),
            'UNIT_CLASS' => $request->input('edit_unit_class'),
            'UNIT_TYPE' => $request->input('edit_unit_type'),
            'BATCH' => $request->input('edit_batch'),
            'CODE' => $request->input('edit_code'),
            'HM_START' => $request->input('edit_hm_start'),
            'HM_END' => $request->input('edit_hm_end'),
            'TOTAL_HM' => $request->input('edit_total_hm'),
            'PLAN_TOTAL_HM' => $request->input('edit_plan_total'),
            'PROGRES' => $request->input('edit_progress'),
            'UPDATED_BY' => Auth::user()->username,
        ]);

        MOP_T_HMTRAIN_HOURS::where('jde_no', $request->input('edit_jde'),)
            ->where('training_type', $request->input('edit_train_type'))
            ->where('unit_class', $request->input('edit_unit_class'))
            ->where('batch', $request->input('edit_batch'))
            ->update(['progres' => $request->input('edit_progress')]);

        return response()->json(['success' => true]);
    }


    public function HMTrainExport(Request $request)
    {
        $selectedColumns = $request->input('columns', [
            'jde_no',
            'employee_name',
            'position',
            'site',
            'date_activity',
            'training_type',
            'unit_class',
            'unit_type',
            'code',
            'batch',
            'hm_start',
            'hm_end',
            'total_hm',
            'plan_total_hm',
            'progres',
        ]);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fileName = $request->input('file_name') ?: 'Record Train Hours';

        return Excel::download(new HMTrainExport($selectedColumns, $fromDate, $toDate), $fileName . '.xlsx');
    }

    public function HMTrainImport(Request $request)
    {
        if ($request->hasFile('import_file')) {
            try {
                Excel::import(new HMTrainImport, $request->file('import_file'));
                return response()->json(['success' => true, 'message' => 'Import successful.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.']);
    }

    public function HMTrainImportTemplate()
    {
        $filePath = 'templates/HMTrain_Template.xlsx';

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Template file not found.');
        }

        return Storage::download($filePath, 'HMTrain_Template.xlsx');
    }

    public function getTotalHM(Request $request)
    {
        $request->validate([
            'jde' => 'required',
            'training_type' => 'required',
            'unit_class' => 'required',
            'batch' => 'required',
        ]);

        try {
            $query = MOP_T_HMTRAIN_HOURS::where('jde_no', $request->jde)
                ->where('training_type', $request->training_type)
                ->where('unit_class', $request->unit_class)
                ->where('batch', $request->batch);

            // buat edit
            if ($request->filled('id')) {
                $query->where('id', '!=', $request->id);
            }

            $totalHM = $query->sum('total_hm');

            return response()->json(['total_hm' => $totalHM]);
        } catch (\Exception $e) {
            Log::error('Error fetching total HM: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }




}
