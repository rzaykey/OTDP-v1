<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MOP_T_MENTORING_HEADER;
use App\Models\MOP_T_MENTORING_DETAIL;
use App\Models\MOP_T_MENTORING_PENILAIAN;
use App\Models\MOP_T_TRAINER_DAILY_ACTIVITY;
use App\Models\MOP_T_HMTRAIN_HOURS;
use App\Models\MOP_M_MENTORING_INDICATOR;
use App\Models\MOP_M_UNIT;
use App\Models\MOP_M_KPI;
use App\Models\MOP_M_TYPE_UNIT;
use App\Models\MOP_T_MOP_HEADER;
use App\Models\MOP_M_MODEL_UNIT;
use App\Models\MASTER_SITE;
use App\Models\PROINT_EMPLOYEE;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DayActHeaderExport;
use App\Imports\DayActImport;
use App\Exports\HMTrainExport;
use App\Imports\HMTrainImport;
use Carbon\Carbon;

class ApiMobileController extends Controller
{
    public function apiModelUnit(Request $request)
    {
        try {
            $query = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
                ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
                ->select('a.id', 'a.model', 'a.FID_TYPE as type', 'b.class')
                ->orderBy('b.class');


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

    public function MentoringData(Request $request)
    {

        $mentoring = MOP_T_MENTORING_HEADER::get();

        return response()->json(['data' => $mentoring]);
    }

    public function apiMentoringCreate(Request $request)
    {
        // Jika ada parameter tipe mentoring, bisa filter indikator
        $siteList = MASTER_SITE::all(); // ✅ ambil semua site
        $typeMentoring = $request->query('type_mentoring');

        $query = DB::connection('MSADMIN')->table('MOP_M_MENTORING_INDICATOR');
        if ($typeMentoring) {
            $query->where('TYPE', $typeMentoring);
        }
        $data = $query->get()->groupBy('indicator_type');
        // Siapkan points default (bisa kosong karena ini baru create)
        $points = [];
        foreach ($data as $section => $indicators) {
            $points[$section] = [
                'y_score' => 0,
                'point' => 0,
            ];
        }

        $modelUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
            ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
            ->select('a.id', 'a.model', 'b.type', 'b.class')->get();

        $unit = MOP_M_UNIT::all();

        return response()->json([
            'success' => true,
            'data' => [
                'indicators' => $data,
                'points' => $points,
                'models' => $modelUnit,
                'units' => $unit,
                'siteList' => $siteList,
            ]
        ]);
    }

    public function apiMentoringStore(Request $request)
    {
        \Log::info('Request Input for Mentoring Store', $request->all());

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - No user logged in'
            ], 401);
        }

        $user = auth()->user();

        DB::beginTransaction();

        try {
            $maxId = MOP_T_MENTORING_HEADER::max('ID');
            $newId = $maxId + 1;

            // Ambil langsung dari request tanpa hitung ulang
            $average_yscore_observation = $request->average_yscore_observation ?? 0;
            $average_point_observation = $request->average_point_observation ?? 0;
            $average_yscore_mentoring = $request->average_yscore_mentoring ?? 0;
            $average_point_mentoring = $request->average_point_mentoring ?? 0;

            \Log::info('Mentoring header data:', [
                'TYPE_MENTORING' => $request->IDTypeMentoring,
                'AVERAGE_YSCORE_OBSERVATION' => $average_yscore_observation,
                'AVERAGE_POINT_OBSERVATION' => $average_point_observation,
                'AVERAGE_YSCORE_MENTORING' => $average_yscore_mentoring,
                'AVERAGE_POINT_MENTORING' => $average_point_mentoring,
            ]);

            MOP_T_MENTORING_HEADER::create([
                'ID' => $newId,
                'TYPE_MENTORING' => $request->IDTypeMentoring,
                'TRAINER_JDE' => $request->IDtrainer,
                'TRAINER_NAME' => $request->trainer,
                'OPERATOR_JDE' => $request->IDoperator,
                'OPERATOR_NAME' => $request->operator,
                'SITE' => $request->site,
                'AREA' => $request->area,
                'UNIT_TYPE' => $request->type,
                'UNIT_MODEL' => $request->model,
                'UNIT_NUMBER' => $request->unit,
                'DATE_MENTORING' => date('Y-m-d', strtotime($request->date)),
                'START_TIME' => $request->time_start,
                'END_TIME' => $request->time_end,
                'AVERAGE_YSCORE_OBSERVATION' => $average_yscore_observation,
                'AVERAGE_POINT_OBSERVATION' => $average_point_observation,
                'AVERAGE_YSCORE_MENTORING' => $average_yscore_mentoring,
                'AVERAGE_POINT_MENTORING' => $average_point_mentoring,
                'CREATED_BY' => $user->username,
            ]);

            // Penilaian
            $maxIdPenilaian = MOP_T_MENTORING_PENILAIAN::max('ID');
            $newIdPenilaian = $maxIdPenilaian + 1;

            foreach ($request->indicators as $indicator) {
                if (isset($indicator['is_observasi']) && $indicator['is_observasi'] === '1') {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $newId,
                        'INDICATOR' => $indicator['fid_indicator'],
                        'TYPE_PENILAIAN' => 'observasi',
                        'YSCORE' => $indicator['yscore'] ?? null,
                        'POINT' => $indicator['point'] ?? null,
                        'CREATED_BY' => $user->username,
                    ]);
                }

                if (isset($indicator['is_mentoring']) && $indicator['is_mentoring'] === '1') {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $newId,
                        'INDICATOR' => $indicator['fid_indicator'],
                        'TYPE_PENILAIAN' => 'mentoring',
                        'YSCORE' => $indicator['yscore'] ?? null,
                        'POINT' => $indicator['point'] ?? null,
                        'CREATED_BY' => $user->username,
                    ]);
                }
            }


            // Detail
            $maxIddetail = MOP_T_MENTORING_DETAIL::max('ID');
            $newIddetail = $maxIddetail + 1;

            \Log::info('Insert indicator detail', $indicator);


            \Log::info('Mentoring header data:', [
                'TYPE_MENTORING' => $request->IDTypeMentoring,
                'AVERAGE_YSCORE_OBSERVATION' => $average_yscore_observation,
                'AVERAGE_POINT_OBSERVATION' => $average_point_observation,
                'AVERAGE_YSCORE_MENTORING' => $average_yscore_mentoring,
                'AVERAGE_POINT_MENTORING' => $average_point_mentoring,
            ]);
            foreach ($request->indicators as $indicator) {
                MOP_T_MENTORING_DETAIL::create([
                    'ID' => $newIddetail++,
                    'FID_MENTORING' => $newId,
                    'FID_INDICATOR' => $indicator['fid_indicator'],
                    'IS_OBSERVASI' => $indicator['is_observasi'] === '1' ? 1 : 0,
                    'IS_MENTORING' => $indicator['is_mentoring'] === '1' ? 1 : 0,
                    'NOTE_OBSERVASI' => $indicator['note_observasi'] ?? '',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mentoring data created successfully',
                'data' => ['mentoring_id' => $newId],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to store mentoring:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mentoring data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function apiMentoringEdit($id)
    {
        try {
            $header = MOP_T_MENTORING_HEADER::findOrFail($id);

            $siteList = MASTER_SITE::all(); // ✅ ambil semua site
            $penilaian = MOP_T_MENTORING_PENILAIAN::where('fid_mentoring', $id)->get();
            $details = MOP_T_MENTORING_DETAIL::where('fid_mentoring', $id)->get();

            $type = strtoupper($header->type_mentoring);
            $data = DB::connection('MSADMIN')
                ->table('MOP_M_MENTORING_INDICATOR')
                ->where('TYPE', $type)
                ->get()
                ->groupBy('indicator_type');

            $points = [];
            foreach ($data as $section => $indicators) {
                $points[$section] = [
                    'y_score' => 0,
                    'point' => 0,
                ];
            }

            $modelUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
                ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
                ->select('a.id', 'a.model', 'b.type', 'b.class')->get();
            $unit = MOP_M_UNIT::get();

            return response()->json([
                'success' => true,
                'data' => [
                    'header' => $header,
                    'penilaian' => $penilaian,
                    'details' => $details,
                    'indicators' => $data,
                    'points' => $points,
                    'model_unit' => $modelUnit,
                    'unit' => $unit,
                    'siteList' => $siteList,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function apiMentoringUpdate(Request $request, $id)
    {
        \Log::debug('Request data:', $request->all());
        $mentoringHeader = MOP_T_MENTORING_HEADER::find($id);
        if (!$mentoringHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Mentoring ID not found in DB'
            ], 404);
        }

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - No user logged in'
            ], 401);
        }

        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Update mentoring header
            $mentoringHeader->update([
                'OPERATOR_JDE' => $request->input('operator_jde'),
                'OPERATOR_NAME' => $request->input('operator_name'),
                'UNIT_TYPE' => $request->input('unit_type'),
                'UNIT_MODEL' => $request->input('unit_model'),
                'UNIT_NUMBER' => $request->input('unit_number'),
                'SITE' => $request->input('site'),
                'AVERAGE_YSCORE_OBSERVATION' => $request->input('average_yscore_observation'),
                'AVERAGE_POINT_OBSERVATION' => $request->input('average_point_observation'),
                'AVERAGE_YSCORE_MENTORING' => $request->input('average_yscore_mentoring'),
                'AVERAGE_POINT_MENTORING' => $request->input('average_point_mentoring'),
                'DATE_MENTORING' => date('Y-m-d', strtotime($request->input('date_mentoring'))),
                'START_TIME' => $request->input('start_time'),
                'END_TIME' => $request->input('end_time'),
                'UPDATED_BY' => $user->username,
            ]);

            // Delete existing penilaian
            MOP_T_MENTORING_PENILAIAN::where('FID_MENTORING', $id)->delete();

            // Ambil max ID penilaian
            $newIdPenilaian = MOP_T_MENTORING_PENILAIAN::max('ID') ?? 0;
            $newIdPenilaian++;

            // Ambil indikator dari request
            $indicators = $request->input('indicators', []);

            foreach ($indicators as $indicator) {
                $fidIndicator = $indicator['fid_indicator'];

                if (isset($indicator['is_observasi']) && $indicator['is_observasi'] == 1) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $id,
                        'INDICATOR' => $fidIndicator,
                        'TYPE_PENILAIAN' => 'observasi',
                        'YSCORE' => $indicator['yscore_observasi'] ?? 1.0,
                        'POINT' => $indicator['point_observasi'] ?? 1.0,
                        'CREATED_BY' => $user->username,
                    ]);
                }

                if (isset($indicator['is_mentoring']) && $indicator['is_mentoring'] == 1) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $id,
                        'INDICATOR' => $fidIndicator,
                        'TYPE_PENILAIAN' => 'mentoring',
                        'YSCORE' => $indicator['yscore_mentoring'] ?? 1.0,
                        'POINT' => $indicator['point_mentoring'] ?? 1.0,
                        'CREATED_BY' => $user->username,
                    ]);
                }
            }

            // Delete existing detail
            MOP_T_MENTORING_DETAIL::where('FID_MENTORING', $id)->delete();

            // Ambil indikator dari database untuk detail
            $loopdetails = MOP_M_MENTORING_INDICATOR::where('type', $request->input('edit_IDTypeMentoring'))->get();
            \Log::debug('Loopdetails:', $loopdetails->toArray());

            // Delete existing detail
            // MOP_T_MENTORING_DETAIL::where('FID_MENTORING', $id)->delete();

            // Ambil array indikator dari request
            $indicators = $request->input('indicators', []);

            // Dapatkan username user login
            $user = auth()->user();

            // Jika ID tidak auto-increment, siapkan ID baru
            $newIdDetail = MOP_T_MENTORING_DETAIL::max('ID') ?? 0;
            $newIdDetail++;

            foreach ($indicators as $item) {
                // Pastikan semua nilai ada dan gunakan default jika tidak
                $fid_indicator = $item['fid_indicator'] ?? null;
                $is_observasi = $item['is_observasi'] ?? 0;
                $is_mentoring = $item['is_mentoring'] ?? 0;
                $note_observasi = $item['note_observasi'] ?? '';

                // Skip jika FID_INDICATOR kosong
                if (!$fid_indicator) continue;

                MOP_T_MENTORING_DETAIL::create([
                    'ID' => $newIdDetail++, // Hanya jika tidak auto-increment
                    'FID_MENTORING' => $id,
                    'FID_INDICATOR' => $fid_indicator,
                    'IS_OBSERVASI' => $is_observasi,
                    'IS_MENTORING' => $is_mentoring,
                    'NOTE_OBSERVASI' => $note_observasi,
                    'UPDATED_BY' => $user->username,
                ]);
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mentoring data updated successfully',
                'data' => ['mentoring_id' => $id],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update mentoring:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mentoring data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function apiMentorDelete(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login first'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $mentoring = MOP_T_MENTORING_HEADER::find($id);
            if (!$mentoring) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mentoring data not found'
                ], 404);
            }

            // Hard delete related details
            MOP_T_MENTORING_DETAIL::where('FID_MENTORING', $id)->delete();

            // Hard delete related penilaian
            MOP_T_MENTORING_PENILAIAN::where('FID_MENTORING', $id)->delete();

            // Hard delete main record
            $mentoring->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mentoring data deleted successfully',
                'data' => [
                    'deleted_id' => $id,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete mentoring data', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete mentoring data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiDayActIndex(Request $request)
    {
        try {
            $query = DB::connection('MSADMIN')->table('MOP_T_TRAINER_DAILY_ACTIVITY as d')
                ->leftJoin('MOP_M_MODEL_UNIT as u', 'd.UNIT_DETAIL', '=', 'u.ID')
                ->leftJoin('MOP_M_ACTIVITY as a', 'd.ACTIVITY', '=', 'a.ID')
                ->select(
                    'd.ID',
                    'd.JDE_NO',
                    'd.EMPLOYEE_NAME',
                    'd.SITE',
                    'd.DATE_ACTIVITY',
                    'd.KPI_TYPE',
                    'a.ACTIVITY as ACTIVITY_NAME',
                    'u.MODEL as UNIT_MODEL',
                    'd.TOTAL_PARTICIPANT',
                    'd.TOTAL_HOUR',
                    'd.CREATED_AT',
                    'd.CREATED_BY',
                    'd.UPDATED_AT',
                    'd.UPDATED_BY'
                );

            $dayact = $query->get();

            if ($dayact->isEmpty()) {
                return response()->json([]);
            }

            return response()->json(['data' => $dayact]);
        } catch (\Exception $e) {
            \Log::error('Error in apiDayActIndex: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apiDayActCreate()
    {

        try {
            $employee = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();
            $unit = MOP_M_MODEL_UNIT::all(); // Tidak filter site

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'unit' => $unit
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in apiDayActCreate: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiDayActStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'jde_no' => 'required|string',
                'employee_name' => 'required|string',
                'site' => 'required|string',
                'date_activity' => 'required|date',
                'kpi_type' => 'required|string',
                'activity' => 'required',
                'unit_detail' => 'required',
                'total_participant' => 'required|string',
                'total_hour' => 'required|string',
            ]);

            $maxId = MOP_T_TRAINER_DAILY_ACTIVITY::max('ID') ?? 0;
            $newId = $maxId + 1;

            $data = array_merge($validated, [
                'ID' => $newId,
                'created_by' => auth()->user()->username,
                'updated_by' => auth()->user()->username,
            ]);

            MOP_T_TRAINER_DAILY_ACTIVITY::insert($data);

            return response()->json([
                'success' => true,
                'message' => 'Daily activity successfully created!',
                'data' => $data,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('apiDayActStore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }
    public function apiDayActEdit($id)

    {
        try {

            $dayact = MOP_T_TRAINER_DAILY_ACTIVITY::where('ID', $id)
                ->first();;

            if (!$dayact) {
                return response()->json(['data' => null], 404);
            }

            return response()->json(['data' => $dayact]);
        } catch (\Exception $e) {
            \Log::error('Error in apiDayActIndex: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apiDayActUpdate(Request $request)
    {
        Log::info('Request to apiDayActUpdate:', $request->all());

        $data = MOP_T_TRAINER_DAILY_ACTIVITY::find($request->input('edit_id'));

        if (!$data) {
            Log::warning('Data not found with ID: ' . $request->input('edit_id'));
            return response()->json(['error' => 'Data not found'], 404);
        }

        $updateData = [
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
        ];

        Log::info('Update data payload:', $updateData);

        try {
            $data->update($updateData);
            Log::info('Update successful for ID: ' . $request->input('edit_id'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update data'], 500);
        }
    }



    public function apiDayActDelete($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login first'
            ], 401);
        }

        try {
            $record = MOP_T_TRAINER_DAILY_ACTIVITY::findOrFail($id);
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Daily activity deleted successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('apiDayActDelete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the record.',
            ], 500);
        }
    }

    public function apiHMTrainIndex()
    {

        $data = DB::connection('MSADMIN')
            ->table('MOP_T_HMTRAIN_HOURS as a')
            ->leftJoin('MOP_M_MODEL_UNIT as b', 'a.UNIT_CLASS', '=', 'b.ID')
            ->leftJoin('MOP_M_UNIT as c', 'a.CODE', '=', 'c.ID')
            // ambil unit_class = model dari b, code = no_unit dari c
            ->select([
                'a.ID as id',
                'a.JDE_NO as jde_no',
                'a.EMPLOYEE_NAME as employee_name',
                'a.POSITION as position',
                'a.TRAINING_TYPE as training_type',
                'b.MODEL as unit_class',    // Model dari m_model
                'a.UNIT_TYPE as unit_type',
                'c.NO_UNIT as code',        // no_unit dari m_unit
                'a.BATCH as batch',
                'a.PLAN_TOTAL_HM as plan_total_hm',
                'a.HM_START as hm_start',
                'a.HM_END as hm_end',
                'a.TOTAL_HM as total_hm',
                'a.PROGRES as progres',
                'a.SITE as site',
                'a.CREATED_AT as created_at',
                'a.CREATED_BY as created_by',
                'a.UPDATED_AT as updated_at',
                'a.UPDATED_BY as updated_by',
                'a.DATE_ACTIVITY as date_activity'
            ])
            ->get();

        // Return sebagai JSON response
        return response()->json([
            'status' => true,
            'message' => 'Success get HM Train data',
            'data' => [
                'data' => $data,
            ]
        ]);
    }

    public function apiHMTrainCreate()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 401);
        }
        $employeeAuth = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();
        $kpi = MOP_M_KPI::get();
        $typeUnit = MOP_M_TYPE_UNIT::select('class')->distinct()->orderby('class')->get();
        $classUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
            ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
            ->select('a.id', 'a.model', 'b.type', 'b.class')
            ->get();
        $codeUnit = MOP_M_UNIT::get();

        return response()->json([
            'status' => true,
            'message' => 'Get employee data for form',
            'data' => [
                'employeeAuth' => $employeeAuth,
                'typeUnit' => $typeUnit,
                'classUnit' => $classUnit,
                'kpi' => $kpi,
                'codeUnit' => $codeUnit,
            ]
        ]);
    }

    public function apiHMTrainStore(Request $request)
    {
        try {
            $maxId = MOP_T_HMTRAIN_HOURS::max('ID');
            $newId = ($maxId ?? 0) + 1;

            $data = [
                'ID' => $newId,
                'JDE_NO' => $request->input('jde_no'),
                'EMPLOYEE_NAME' => $request->input('employee_name'),
                'POSITION' => $request->input('position'),
                'SITE' => $request->input('site'),
                'DATE_ACTIVITY' => $request->input('date_activity'),
                'TRAINING_TYPE' => $request->input('training_type'),
                'UNIT_CLASS' => $request->input('unit_class'),
                'UNIT_TYPE' => $request->input('unit_type'),
                'CODE' => $request->input('code'),
                'BATCH' => $request->input('batch'),
                'HM_START' => $request->input('hm_start'),
                'HM_END' => $request->input('hm_end'),
                'TOTAL_HM' => $request->input('total_hm'),
                'PLAN_TOTAL_HM' => $request->input('plan_total_hm'),
                'PROGRES' => $request->input('progres'),
                'CREATED_BY' => Auth::user()->username,
            ];


            MOP_T_HMTRAIN_HOURS::insert($data);

            // Update progress (jika diperlukan)
            MOP_T_HMTRAIN_HOURS::where('JDE_NO', $request->input('JDE'))
                ->where('TRAINING_TYPE', $request->input('train_type'))
                ->where('UNIT_CLASS', $request->input('unit_class'))
                ->where('BATCH', $request->input('batch'))
                ->update(['PROGRES' => $request->input('progress')]);

            return response()->json([
                'status' => true,
                'message' => 'Train Hours record created successfully.',
                'data' => [
                    'id' => $newId
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in apiHMTrainStore: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data, harap menghubungi admin atau IT',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/trainHours/{id}
    public function apiHMTrainShow($id)
    {
        $data = MOP_T_HMTRAIN_HOURS::find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        // Ambil semua codeUnit untuk dropdown
        $codeUnit = MOP_M_UNIT::get();
        return response()->json([
            'status' => true,
            'data' => $data,
            'codeUnit' => $codeUnit, // untuk dropdown di form edit
        ]);
    }


    // PUT /api/trainHours/{id}
    public function apiHMTrainUpdate(Request $request, $id)
    {
        $data = MOP_T_HMTRAIN_HOURS::find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        $data->update([
            'JDE_NO' => $request->input('jde_no'),
            'EMPLOYEE_NAME' => $request->input('employee_name'),
            'SITE' => $request->input('site'),
            'DATE_ACTIVITY' => $request->input('date_activity'),
            'POSITION' => $request->input('position'),
            'TRAINING_TYPE' => $request->input('training_type'),
            'UNIT_CLASS' => $request->input('unit_class'),
            'UNIT_TYPE' => $request->input('unit_type'),
            'BATCH' => $request->input('batch'),
            'CODE' => $request->input('code'),
            'HM_START' => $request->input('hm_start'),
            'HM_END' => $request->input('hm_end'),
            'TOTAL_HM' => $request->input('total_hm'),
            'PLAN_TOTAL_HM' => $request->input('plan_total_hm'),
            'PROGRES' => $request->input('progres'),
            'UPDATED_BY' => Auth::user()->username,
        ]);

        // Optional: Update progres, jika masih dibutuhkan (logic lama-mu)
        MOP_T_HMTRAIN_HOURS::where('JDE_NO', $request->input('jde_no'))
            ->where('TRAINING_TYPE', $request->input('training_type'))
            ->where('UNIT_CLASS', $request->input('unit_class'))
            ->where('BATCH', $request->input('batch'))
            ->update(['PROGRES' => $request->input('progres')]);

        return response()->json([
            'status' => true,
            'message' => 'Train Hours record updated successfully.',
            'data' => [
                'id' => $id
            ]
        ]);
    }

    public function summary()
    {
        $today = Carbon::today()->toDateString(); // format: 'YYYY-MM-DD'
        $mentoringToday = MOP_T_MENTORING_HEADER::whereDate('date_mentoring', $today)->count();
        $dailyToday = MOP_T_TRAINER_DAILY_ACTIVITY::whereDate('date_activity', $today)->count();
        $trainHoursToday = MOP_T_HMTRAIN_HOURS::whereDate('date_activity', $today)->count();
        $unitTotal = MOP_M_UNIT::get()->count();
        $typeTotal = MOP_M_TYPE_UNIT::get()->count();
        $modelTotal = MOP_M_MODEL_UNIT::get()->count();
        $siteTotal = MASTER_SITE::get()->count(); // Jumlah jenis class unik (distinct)
        $classTotal = MOP_M_TYPE_UNIT::select('class')->distinct()->count('class');

        return response()->json([
            'success' => true,
            'data' => [
                'mentoringToday' => $mentoringToday,
                'dailyToday' => $dailyToday,
                'trainHoursToday' => $trainHoursToday,
                'unitTotal' => $unitTotal,
                'typeTotal' => $typeTotal,
                'modelTotal' => $modelTotal,
                'siteTotal' => $siteTotal,
                'classTotal' => $classTotal,
            ]
        ]);
    }

    public function apiMopData()
    {
        $mopheader = MOP_T_MOP_HEADER::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('jde_no', 'asc')
            ->get();

        return response()->json(['data' => $mopheader]);
    }
}
