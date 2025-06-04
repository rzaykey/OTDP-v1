<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\MOP_T_MENTORING_HEADER;
use App\Models\MOP_T_MENTORING_DETAIL;
use App\Models\MOP_T_MENTORING_PENILAIAN;
use App\Models\MOP_M_MENTORING_INDICATOR;
use App\Models\MOP_M_TYPE_UNIT;
use App\Models\MASTER_SITE;
use App\Models\MOP_M_UNIT;
use App\Models\PROINT_EMPLOYEE;
use Illuminate\Support\Facades\Validator;




class MentoringController extends Controller
{
    public function MentoringIndex()
    {
        $modelUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
            ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
            ->select('a.id', 'a.model', 'b.type', 'b.class')->get();
        $unit = MOP_M_UNIT::get();
        return view('pages.mentoring.mentoringindex', [
            'getModel' => $modelUnit,
            'getUnit' => $unit,
        ]);
    }

    public function MentoringData(Request $request)
    {

        $mentoring = MOP_T_MENTORING_HEADER::get();

        return response()->json(['data' => $mentoring]);
    }

    public function MentoringCreate($type)
    {
        // List of valid types
        $employeeAuth = PROINT_EMPLOYEE::where('EmployeeId', Auth::user()->username)->first();

        $validTypes = ['DIGGER', 'HAULER', 'BULLDOZER', 'GRADER'];

        // Check if $type is valid
        if (!in_array($type, $validTypes)) {
            return redirect()->back()->with('error', 'TYPE tidak ditemukan');
        }

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

        return view('pages.mentoring.mentoringCreate', [
            'data' => $data,
            'points' => $points,
            'type' => $type,
            'employeeAuth' => $employeeAuth
        ]);
    }

    public function mentoringStore(Request $request)
    {

        $maxId = MOP_T_MENTORING_HEADER::max('ID');
        $newId = $maxId + 1;
        // Observation Averages
        $averageYScoreObservasi = collect([
            $request->observasiPRODUKTIFITASYScore ?? 0,
            $request->observasiSAFETY_AWARENESSYScore ?? 0,
            $request->observasiMACHINE_HEALTHYScore ?? 0,
            $request->observasiFUEL_EFFICIENT_AWARENESSYScore ?? 0,
        ])->average();

        $averagePointObservasi = collect([
            $request->observasiPRODUKTIFITASPoint ?? 0,
            $request->observasiSAFETY_AWARENESSPoint ?? 0,
            $request->observasiMACHINE_HEALTHPoint ?? 0,
            $request->observasiFUEL_EFFICIENT_AWARENESSPoint ?? 0,
        ])->average();

        // Mentoring Averages
        $averageYScoreMentoring = collect([
            $request->mentoringPRODUKTIFITASYScore ?? 0,
            $request->mentoringSAFETY_AWARENESSYScore ?? 0,
            $request->mentoringMACHINE_HEALTHYScore ?? 0,
            $request->mentoringFUEL_EFFICIENT_AWARENESSYScore ?? 0,
        ])->average();

        $averagePointMentoring = collect([
            $request->mentoringPRODUKTIFITASPoint ?? 0,
            $request->mentoringSAFETY_AWARENESSPoint ?? 0,
            $request->MentoringMACHINE_HEALTHPoint ?? 0,
            $request->mentoringFUEL_EFFICIENT_AWARENESSPoint ?? 0,
        ])->average();

        Log::info('Request inputs: ', $request->all());

        try {
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
                'DATE_MENTORING' =>  date('Y-m-d', strtotime("{$request->input('date')}")),
                'START_TIME' =>  $request->time_start,
                'END_TIME' =>  $request->time_end,
                'AVERAGE_YSCORE_OBSERVATION' => $averageYScoreObservasi,
                'AVERAGE_POINT_OBSERVATION' => $averagePointObservasi,
                'AVERAGE_YSCORE_MENTORING' => $averageYScoreMentoring,
                'AVERAGE_POINT_MENTORING' => $averagePointMentoring,
                'CREATED_BY' => auth()->user()->username,
            ]);

            $loopdetails = MOP_M_MENTORING_INDICATOR::where('type', $request->IDTypeMentoring)->get();
            $distinctIndicators = MOP_M_MENTORING_INDICATOR::where('type', $request->IDTypeMentoring)
                ->distinct()
                ->pluck('indicator_type');

            $maxIdPenilaian = MOP_T_MENTORING_PENILAIAN::max('ID');
            $newIdPenilaian = $maxIdPenilaian + 1;

            foreach ($distinctIndicators as $distinctIndicator) {
                // Format keys dynamically
                $observasiYScoreKey = 'observasi' . str_replace(' ', '_', $distinctIndicator) . 'YScore';
                $observasiPointKey = 'observasi' . str_replace(' ', '_', $distinctIndicator) . 'Point';

                $mentoringYScoreKey = 'mentoring' . str_replace(' ', '_', $distinctIndicator) . 'YScore';
                $mentoringPointKey = 'mentoring' . str_replace(' ', '_', $distinctIndicator) . 'Point';

                // Check if values exist for observation
                if ($request->has($observasiYScoreKey) && $request->has($observasiPointKey)) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $newId,
                        'INDICATOR' => $distinctIndicator,
                        'TYPE_PENILAIAN' => 'observasi',
                        'YSCORE' => $request->$observasiYScoreKey,
                        'POINT' => $request->$observasiPointKey,
                        'CREATED_BY' => auth()->user()->username,
                    ]);
                }

                // Check if values exist for mentoring
                if ($request->has($mentoringYScoreKey) && $request->has($mentoringPointKey)) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $newId,
                        'INDICATOR' => $distinctIndicator,
                        'TYPE_PENILAIAN' => 'mentoring',
                        'YSCORE' => $request->$mentoringYScoreKey,
                        'POINT' => $request->$mentoringPointKey,
                        'CREATED_BY' => auth()->user()->username,
                    ]);
                }
            }

            $maxIddetail = MOP_T_MENTORING_DETAIL::max('ID');
            $newIddetail = $maxIddetail + 1;
            $i = 1;
            foreach ($loopdetails as $loopdetail) {
                $indicatorType = preg_replace('/\s+/', '_', $loopdetail->indicator_type);
                MOP_T_MENTORING_DETAIL::create([
                    'ID' => $newIddetail,
                    'FID_MENTORING' => $newId,
                    'FID_INDICATOR' => $loopdetail->id,
                    'IS_OBSERVASI' => $request->input('observasi' . $indicatorType . $i, 0),
                    'IS_MENTORING' => $request->input('mentoring' . $indicatorType . $i, 0),
                    'NOTE_OBSERVASI' => $request->input('catatan' . $indicatorType . $i, ''),
                ]);
                $i++;
                $newIddetail++;
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function MentoringDashboard()
    {
        return view('pages.mentoring.mentoringDashboard');
    }

    public function MentoringEdit($id)
    {
        $header = MOP_T_MENTORING_HEADER::findOrFail($id);

        $penilaian = MOP_T_MENTORING_PENILAIAN::where('fid_mentoring', $id)->get();
        $details = MOP_T_MENTORING_DETAIL::where('fid_mentoring', $id)->get();

        $type = $header->type_mentoring;
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
        // $typeUnit = MOP_M_TYPE_UNIT::select('class')->distinct()->orderby('class')->get();
        $modelUnit = DB::connection('MSADMIN')->table('MOP_M_MODEL_UNIT as a')
            ->leftJoin('MOP_M_TYPE_UNIT as b', 'a.FID_TYPE', '=', 'b.ID')
            ->select('a.id', 'a.model', 'b.type', 'b.class')->get();
        $unit = MOP_M_UNIT::get();

        return view('pages.mentoring.mentoringEdit', [
            'data' => $data,
            'points' => $points,
            'getModel' => $modelUnit,
            'getUnit' => $unit,
            'header' => $header,
            'penilaian' => $penilaian,
            'details' => $details,
        ]);
    }

    public function mentoringUpdate(Request $request)
    {
        $id = $request->edit_id;

        try {
            // Dynamically fetch indicator sections
            $points = MOP_M_MENTORING_INDICATOR::where('type', $request->edit_IDTypeMentoring)
                ->distinct()
                ->pluck('indicator_type');

            // Collect dynamic observasi scores
            $observasiYScores = collect();
            $observasiPoints = collect();
            $mentoringYScores = collect();
            $mentoringPoints = collect();

            foreach ($points as $section) {
                $safeKey = str_replace(' ', '_', $section);

                $observasiYScores->push($request->input("edit_observasi{$safeKey}YScore", 0));
                $observasiPoints->push($request->input("edit_observasi{$safeKey}Point", 0));
                $mentoringYScores->push($request->input("edit_mentoring{$safeKey}YScore", 0));
                $mentoringPoints->push($request->input("edit_mentoring{$safeKey}Point", 0));
            }

            $averageYScoreObservasi = $observasiYScores->average();
            $averagePointObservasi = $observasiPoints->average();
            $averageYScoreMentoring = $mentoringYScores->average();
            $averagePointMentoring = $mentoringPoints->average();

            // Update header
            MOP_T_MENTORING_HEADER::where('ID', $id)->update([
                'TYPE_MENTORING' => $request->edit_IDTypeMentoring,
                'TRAINER_JDE' => $request->edit_IDtrainer,
                'TRAINER_NAME' => $request->edit_trainer,
                'OPERATOR_JDE' => $request->edit_IDoperator,
                'OPERATOR_NAME' => $request->edit_operator,
                'SITE' => $request->edit_site,
                'AREA' => $request->edit_area,
                'UNIT_TYPE' => $request->edit_type,
                'UNIT_MODEL' => $request->edit_model,
                'UNIT_NUMBER' => $request->edit_unit,
                'DATE_MENTORING' => date('Y-m-d', strtotime($request->edit_date)),
                'START_TIME' => $request->edit_time_start,
                'END_TIME' => $request->edit_time_end,
                'AVERAGE_YSCORE_OBSERVATION' => $averageYScoreObservasi,
                'AVERAGE_POINT_OBSERVATION' => $averagePointObservasi,
                'AVERAGE_YSCORE_MENTORING' => $averageYScoreMentoring,
                'AVERAGE_POINT_MENTORING' => $averagePointMentoring,
                'UPDATED_BY' => auth()->user()->username,
            ]);

            // Delete and recreate penilaian
            MOP_T_MENTORING_PENILAIAN::where('FID_MENTORING', $id)->delete();
            $newIdPenilaian = MOP_T_MENTORING_PENILAIAN::max('ID') + 1;

            foreach ($points as $indicator) {
                $safeKey = str_replace(' ', '_', $indicator);

                $obsYScore = $request->input("edit_observasi{$safeKey}YScore");
                $obsPoint = $request->input("edit_observasi{$safeKey}Point");
                $mentYScore = $request->input("edit_mentoring{$safeKey}YScore");
                $mentPoint = $request->input("edit_mentoring{$safeKey}Point");

                if (!is_null($obsYScore) && !is_null($obsPoint)) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $id,
                        'INDICATOR' => $indicator,
                        'TYPE_PENILAIAN' => 'observasi',
                        'YSCORE' => $obsYScore,
                        'POINT' => $obsPoint,
                        'CREATED_BY' => auth()->user()->username,
                    ]);
                }

                if (!is_null($mentYScore) && !is_null($mentPoint)) {
                    MOP_T_MENTORING_PENILAIAN::create([
                        'ID' => $newIdPenilaian++,
                        'FID_MENTORING' => $id,
                        'INDICATOR' => $indicator,
                        'TYPE_PENILAIAN' => 'mentoring',
                        'YSCORE' => $mentYScore,
                        'POINT' => $mentPoint,
                        'CREATED_BY' => auth()->user()->username,
                    ]);
                }
            }

            // Delete and recreate detail
            MOP_T_MENTORING_DETAIL::where('FID_MENTORING', $id)->delete();
            $loopdetails = MOP_M_MENTORING_INDICATOR::where('type', $request->edit_IDTypeMentoring)->get();
            $newIdDetail = MOP_T_MENTORING_DETAIL::max('ID') + 1;
            $i = 1;

            foreach ($loopdetails as $loopdetail) {
                $indicatorId = $loopdetail->id;

                MOP_T_MENTORING_DETAIL::create([
                    'ID' => $newIdDetail++,
                    'FID_MENTORING' => $id,
                    'FID_INDICATOR' => $indicatorId,
                    'IS_OBSERVASI' => $request->input("edit_observasi.$indicatorId", 0),
                    'IS_MENTORING' => $request->input("edit_mentoring.$indicatorId", 0),
                    'NOTE_OBSERVASI' => $request->input("edit_note_observasi.$indicatorId", ''),
                ]);
            }


            return response()->json(['success' => true, 'message' => 'Data updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function MentoringDataDB(Request $request)
    {

        $query = DB::connection('MSADMIN')->table('MOP_T_MENTORING_HEADER as a')
            ->leftJoin('MOP_T_MENTORING_PENILAIAN as b', 'a.ID', '=', 'b.FID_MENTORING')
            ->leftJoin('MOP_M_UNIT as c', 'a.unit_number', '=', 'c.id');

        if ($request->filled('site')) {
            $query->where('a.site', $request->site);
        }

        if ($request->filled('operator_jde')) {
            $query->where('a.operator_jde', $request->operator_jde);
        }

        if ($request->filled('year')) {
            $query->whereYear('a.date_mentoring', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('a.date_mentoring', $request->month);
        }

        if ($request->filled('unit_type')) {
            $query->where('a.unit_type', $request->unit_type);
        }

        if ($request->filled('no_unit')) {
            $query->where('c.no_unit', $request->no_unit);
        }

        $queryForSkill = clone $query;

        $mentoringcompile = $query
            ->selectRaw('
                a.site,
                a.operator_jde,
                a.operator_name,
                a.unit_type,
                a.type_mentoring,
                a.date_mentoring,
                EXTRACT(YEAR FROM a.date_mentoring) as year,
                EXTRACT(MONTH FROM a.date_mentoring) as month,
                b.indicator,
                b.type_penilaian,
                b.point,
                c.no_unit
            ')
            ->groupBy([
                'a.site',
                'a.operator_jde',
                'a.operator_name',
                'a.unit_type',
                'a.date_mentoring',
                'a.type_mentoring',
                'b.indicator',
                'b.type_penilaian',
                'b.point',
                'c.no_unit',
                DB::raw('EXTRACT(YEAR FROM a.date_mentoring)'),
                DB::raw('EXTRACT(MONTH FROM a.date_mentoring)'),
            ])
            ->where('b.type_penilaian', 'observasi')
            ->orderBy('a.date_mentoring', 'asc')
            ->get();

        $mentoringskill = $queryForSkill
            ->selectRaw('
                b.indicator,
                b.type_penilaian,
                AVG(b.point) as avg_point
            ')
            ->groupBy('b.indicator', 'b.type_penilaian')
            ->get();

        return response()->json([
            'data' => $mentoringcompile,
            'skill' => $mentoringskill,
        ]);
    }

    //API
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
}
