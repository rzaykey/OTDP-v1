<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PROINT_EMPLOYEE;
use App\Models\MOP_M_ACTIVITY;
use App\Models\MOP_M_KPI;
use App\Models\MOP_M_TYPE_UNIT;
use App\Models\MOP_M_MODEL_UNIT;
use App\Models\MOP_M_UNIT;

class MasterController extends Controller
{
    public function MasterNoUnit()
    {
        $data = MOP_M_UNIT::orderby('no_unit', 'asc')->get();

        return view('pages.master.noUnit', [
            'data' => $data,
        ]);
    }

    public function MasterUnitStore(Request $request)
    {
        try {
            $maxId = MOP_M_UNIT::max('ID');
            $newId = $maxId + 1;

            $data = [
                'ID' => $newId,
                'no_unit' => $request->input('no_unit'),
                'type' => $request->input('type'),
                'site' => $request->input('site'),
                'merk' => $request->input('merk'),
                'model' => $request->input('model'),
                'category_mentoring' => $request->input('class'),
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ];

            MOP_M_UNIT::insert($data);

            // Redirect or return a success message
            return response()->json(['success' => true]);
            // return redirect()->route('DayActIndex')->with('success', 'Unit Data created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in MOPStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data, harap menghubungi admin atau IT'], 500);
        }
    }

    public function MasterUnitDelete($id)
    {
        try {
            $record = MOP_M_UNIT::findOrFail($id);
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

    public function MasterUnitEdit($id)
    {
        $data = MOP_M_UNIT::find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function MasterUnitUpdate(Request $request)
    {

        $data = MOP_M_UNIT::find($request->input('edit_id'));

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update([
            'NO_UNIT' => $request->input('no_unit'),
            'TYPE' => $request->input('type'),
            'SITE' => $request->input('site'),
            'MERK' => $request->input('merk'),
            'MODEL' => $request->input('model'),
            'CATEGORY_MENTORING' => $request->input('class'),
            'UPDATED_BY' => Auth::user()->username,
        ]);


        return response()->json(['success' => true]);
    }

    public function MasterActivity()
    {
        $data = MOP_M_ACTIVITY::orderby('kpi', 'asc')->get();

        return view('pages.master.activity', [
            'data' => $data,
        ]);
    }

    public function MasterActivityStore(Request $request)
    {
        try {
            $maxId = MOP_M_ACTIVITY::max('ID');
            $newId = $maxId + 1;

            $data = [
                'ID' => $newId,
                'kpi' => $request->input('kpi'),
                'activity' => $request->input('activity'),
                'site' => $request->input('site'),
            ];

            MOP_M_ACTIVITY::insert($data);

            // Redirect or return a success message
            return response()->json(['success' => true]);
            // return redirect()->route('DayActIndex')->with('success', 'Unit Data created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in MasterActivityStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data, harap menghubungi admin atau IT'], 500);
        }
    }

    public function MasterActivityDelete($id)
    {
        try {
            $record = MOP_M_ACTIVITY::findOrFail($id);
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

    public function MasterActivityEdit($id)
    {
        $data = MOP_M_ACTIVITY::find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function MasterActivityUpdate(Request $request)
    {

        $data = MOP_M_ACTIVITY::find($request->input('edit_id'));

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update([
            'KPI' => $request->input('kpi'),
            'ACTIVITY' => $request->input('activity'),
            'SITE' => $request->input('site'),
        ]);


        return response()->json(['success' => true]);
    }

    public function EmployeeOperator(Request $request)
    {
        try {
            $startTime = microtime(true);

            $query = PROINT_EMPLOYEE::where('PayStatus', 'ACTIVE')
                ->whereIn('LocationGroupName', ['ACP', 'BCP', 'KCP', 'WKP'])
                ->where('OrgGroupName', 'Operation')
                ->whereIn('joblevelgroup', ['Operator', 'Junior Staff', 'Supervisor'])
                ->where('OrgGroupName', 'Operation');

            // Add search functionality
            if ($request->has('q')) {
                $query->where(function ($q) use ($request) {
                    $q->where('EmployeeName', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('employeeId', 'LIKE', '%' . $request->q . '%');
                });
            }

            $employees = $query->select(
                'employeeId',
                'EmployeeName',
                'LocationGroupName',
                'DivisionName',
                'OrgGroupName',
                'PositionName'
            )->limit(100)->get();

            $endTime = microtime(true);
            Log::info('EmployeeOperator query time: ' . ($endTime - $startTime) . ' seconds');

            return response()->json($employees);
        } catch (\Exception $e) {
            Log::error('Error in EmployeeOperator: ' . $e->getMessage());
            return response()->json(['error' => $e], 500);
        }
    }

    public function EmployeeOperatorAll()
    {
        try {
            $startTime = microtime(true);

            $query = PROINT_EMPLOYEE::where('PayStatus', 'ACTIVE')
                ->whereIn('LocationGroupName', ['ACP', 'BCP', 'KCP', 'WKP'])
                ->where('OrgGroupName', 'Operation')
                ->whereIn('joblevelgroup', ['Operator', 'Junior Staff', 'Supervisor'])
                ->where('OrgGroupName', 'Operation');

            $employees = $query->select(
                'employeeId',
                'EmployeeName',
                'LocationGroupName',
                'DivisionName',
                'OrgGroupName',
                'PositionName'
            )->get();

            $endTime = microtime(true);
            Log::info('EmployeeOperator query time: ' . ($endTime - $startTime) . ' seconds');

            return response()->json($employees);
        } catch (\Exception $e) {
            Log::error('Error in EmployeeOperator: ' . $e->getMessage());
            return response()->json(['error' => $e], 500);
        }
    }

    public function ModelUnitAll()
    {
        try {
            $data = MOP_M_MODEL_UNIT::orderby('id', 'asc')->get();
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in ModelUnit: ' . $e->getMessage());
            return response()->json(['error' => $e], 500);
        }
    }

    public function getEmployeeAuth()
    {
        try {
            $employeeAuth = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();

            if (!$employeeAuth) {
                return response()->json(['error' => 'Employee data not found.'], 404);
            }

            return response()->json($employeeAuth);
        } catch (\Exception $e) {
            Log::error('Error in EmployeeAuth API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getKPI()
    {
        try {
            // Ambil semua data KPI tanpa filter
            $data = MOP_M_KPI::all();

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getActivity(Request $request)
    {
        try {
            $site = $request->input('site');
            $role = $request->input('role');
            $kpi = $request->input('kpi'); // HARUS id, bukan string

            \Log::info('request: ' . json_encode($request->all()) . ' | role: ' . $role . ' | site: ' . $site . ' | KPI: ' . $kpi);

            if ($role === 'Full') {
                // Full boleh lihat semua activity di KPI tertentu
                $query = \App\Models\MOP_M_ACTIVITY::where('KPI', $kpi)->get();
            } else {
                // Selain full, filter juga by site (pastikan kolom di DB benar)
                $query = \App\Models\MOP_M_ACTIVITY::where('KPI', $kpi)
                    ->where('site', $site)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $query
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Activity API: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getActivityAll(Request $request)
    {
        try {
            // Selain full, filter juga by site (pastikan kolom di DB benar)
            $query = \App\Models\MOP_M_ACTIVITY::get();
            return response()->json([
                'success' => true,
                'data' => $query
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Activity API: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function getMasterClassUnit(Request $request)
    {
        try {

            $search = $request->get('q');

            $query = MOP_M_TYPE_UNIT::select('class')->distinct()->orderby('class');

            if ($search) {
                $query->where('class', 'like', '%' . $search . '%');
            }

            $class = $query->get();

            if ($class->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($class);
        } catch (\Exception $e) {
            Log::error('Error in MOP_M_TYPE_UNIT API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMasterTypeUnit(Request $request)
    {
        try {

            $search = $request->get('q');

            $query = MOP_M_TYPE_UNIT::select('type')->distinct()->orderby('type');

            if ($search) {
                $query->where('type', 'like', '%' . $search . '%');
            }

            $type = $query->get();

            if ($type->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($type);
        } catch (\Exception $e) {
            Log::error('Error in MOP_M_TYPE_UNIT API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMasterModelUnit(Request $request)
    {
        try {
            $query = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
                ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.TYPE', '=', 'b.TYPE')
                ->select('a.id', 'a.model', 'a.type', 'b.class');

            $models = $query->orderBy('b.class')->get();

            if ($models->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($models);
        } catch (\Exception $e) {
            Log::error('Error in MOP_M_MODEL_UNIT API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMasterModelUnitbasedType(Request $request)
    {
        try {
            $search = $request->get('q');

            $query = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
                ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.TYPE', '=', 'b.TYPE')
                ->select('a.id', 'a.model', 'a.type', 'b.class');

            if ($request->has('type')) {
                $query->where('b.class', $request->type); // adjust the column name if needed
            }

            if ($request->has('unit')) {
                $query->where('a.type', $request->unit);
            }

            if ($search) {
                $query->where('model', 'like', '%' . $search . '%');
            }

            $models = $query->get();

            if ($models->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($models);
        } catch (\Exception $e) {
            Log::error('Error in MOP_M_MODEL_UNIT API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMasterUnit(Request $request)
    {
        try {

            $search = $request->get('q');

            $query = MOP_M_UNIT::query();

            if ($request->has('model')) {
                $query->where('model', $request->model); // Adjust column name if needed
            }

            if ($search) {
                $query->where('no_unit', 'like', '%' . $search . '%');
            }

            $units = $query->get();

            if ($units->isEmpty()) {
                return response()->json([]);
            }
            return $units;
            return response()->json($units);
        } catch (\Exception $e) {
            Log::error('Error in getMasterUnit API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
