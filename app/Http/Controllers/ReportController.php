<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MOP_T_MOP_HEADER;
use App\Models\MOP_T_HMTRAIN_HOURS;
use App\Models\MOP_T_TRAINER_DAILY_ACTIVITY;


class ReportController extends Controller
{
    public function ReportMOP()
    {

        $Month   = MOP_T_MOP_HEADER::select('month')->groupBy('month')->orderBy('month', 'asc')->get();
        $Year    = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();
        $data    = MOP_T_MOP_HEADER::select(
            'month',
            'year',
            'jde_no',
            'employee_name',
            DB::raw("SUM(CASE WHEN mop_type = 'LOADER' THEN TO_NUMBER(total_point) ELSE 0 END) as point_produksi"),
            DB::raw("SUM(CASE WHEN mop_type = 'SUPPORT' THEN TO_NUMBER(total_point) ELSE 0 END) as point_support")
        )
            ->groupBy('month', 'year', 'jde_no', 'employee_name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('jde_no', 'asc')
            ->limit(1)
            ->get();

        foreach ($data as $score) {
            $produksi = (float) $score->point_produksi;
            $support = (float) $score->point_support;

            if ($produksi > 0 && $support > 0) {
                $score->final_score = round(($produksi + $support) / 2, 2);
            } else {
                $score->final_score = $produksi > 0 ? $produksi : $support;
            }

            $final = $score->final_score;
            if ($final < 2) {
                $score->grade = 'K';
            } elseif ($final >= 2.0 && $final <= 2.49) {
                $score->grade = 'C';
            } elseif ($final >= 2.5 && $final <= 2.99) {
                $score->grade = 'C+';
            } elseif ($final >= 3.0 && $final <= 3.49) {
                $score->grade = 'B';
            } elseif ($final >= 3.5 && $final <= 3.99) {
                $score->grade = 'B+';
            } elseif ($final >= 4.0 && $final <= 4.49) {
                $score->grade = 'BS';
            } elseif ($final >= 4.5 && $final <= 4.75) {
                $score->grade = 'BS+';
            } elseif ($final > 4.75) {
                $score->grade = 'ISTIMEWA';
            } else {
                $score->grade = '-';
            }
        }

        return view('pages.report.mop.monthly', [
            'Data' => $data,
            'Year' => $Year,
            'Month' => $Month,
        ]);
    }

    public function MOPSearch(Request $request)
    {
        $data   = $request->all();
        $Month   = MOP_T_MOP_HEADER::select('month')->groupBy('month')->orderBy('month', 'asc')->get();
        $Year    = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();

        $query = MOP_T_MOP_HEADER::select(
            'month',
            'year',
            'jde_no',
            'employee_name',
            DB::raw("SUM(CASE WHEN mop_type = 'LOADER' THEN TO_NUMBER(total_point) ELSE NULL END) as point_produksi"),
            DB::raw("SUM(CASE WHEN mop_type = 'SUPPORT' THEN TO_NUMBER(total_point) ELSE NULL END) as point_support")
        )
            ->groupBy('month', 'year', 'jde_no', 'employee_name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('jde_no', 'asc');

        $year = $data['Year'] ?? "";
        $month = $data['Month'] ?? "";


        if ($month !== "") {
            $query->where('month', $month);
        }

        if ($year !== "") {
            $query->where('year', $year);
        }

        $Data = $query->orderBy('jde_no', 'asc')->get();

        foreach ($Data as $score) {
            $produksi = (float) $score->point_produksi;
            $support = (float) $score->point_support;

            if ($produksi > 0 && $support > 0) {
                $score->final_score = round(($produksi + $support) / 2, 2);
            } else {
                $score->final_score = $produksi > 0 ? $produksi : $support;
            }

            $final = $score->final_score;
            if ($final < 2) {
                $score->grade = 'K';
            } elseif ($final >= 2.0 && $final <= 2.49) {
                $score->grade = 'C';
            } elseif ($final >= 2.5 && $final <= 2.99) {
                $score->grade = 'C+';
            } elseif ($final >= 3.0 && $final <= 3.49) {
                $score->grade = 'B';
            } elseif ($final >= 3.5 && $final <= 3.99) {
                $score->grade = 'B+';
            } elseif ($final >= 4.0 && $final <= 4.49) {
                $score->grade = 'BS';
            } elseif ($final >= 4.5 && $final <= 4.75) {
                $score->grade = 'BS+';
            } elseif ($final > 4.75) {
                $score->grade = 'ISTIMEWA';
            } else {
                $score->grade = '-';
            }
        }

        return view('pages.report.mop.monthly', [
            'Year' => $Year,
            'Month' => $Month,
            'Data' => $Data,
        ]);
    }

    public function ReportGradeMOP()
    {
        $Month = MOP_T_MOP_HEADER::select('month')->groupBy('month')->orderBy('month', 'asc')->get();
        $Year  = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();


        $data = MOP_T_MOP_HEADER::select(
            'jde_no',
            'employee_name',
            DB::raw("SUM(CASE WHEN mop_type = 'LOADER' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_produksi"),
            DB::raw("SUM(CASE WHEN mop_type = 'SUPPORT' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_support")
        )
        ->groupBy('jde_no', 'employee_name')
        ->limit(1)
        ->get();

        $gradeDistribution = [
            'K' => 0,
            'C' => 0,
            'C+' => 0,
            'B' => 0,
            'B+' => 0,
            'BS' => 0,
            'BS+' => 0,
            'ISTIMEWA' => 0,
        ];

        foreach ($data as $score) {
            $produksi = (float) $score->point_produksi;
            $support = (float) $score->point_support;

            if ($produksi > 0 && $support > 0) {
                $final_score = round(($produksi + $support) / 2, 2);
            } else {
                $final_score = $produksi > 0 ? $produksi : $support;
            }

            $grade = '';
            if ($final_score < 2.0) {
                $grade = 'K';
            } elseif ($final_score >= 2.0 && $final_score <= 2.49) {
                $grade = 'C';
            } elseif ($final_score >= 2.5 && $final_score <= 2.99) {
                $grade = 'C+';
            } elseif ($final_score >= 3.0 && $final_score <= 3.49) {
                $grade = 'B';
            } elseif ($final_score >= 3.5 && $final_score <= 3.99) {
                $grade = 'B+';
            } elseif ($final_score >= 4.0 && $final_score <= 4.49) {
                $grade = 'BS';
            } elseif ($final_score >= 4.5 && $final_score <= 4.75) {
                $grade = 'BS+';
            } else {
                $grade = 'ISTIMEWA';
            }

            $gradeDistribution[$grade]++;
        }

        return view('pages.report.mop.gradedist', [
            'Year' => $Year,
            'Month' => $Month,
            'Data' => $gradeDistribution,
        ]);
    }

    public function MOPGradeDistribution(Request $request)
    {
        $Month = MOP_T_MOP_HEADER::select('month')->groupBy('month')->orderBy('month', 'asc')->get();
        $Year  = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();

        $selectedMonth = $request->input('MonthGrade');
        $selectedYear = $request->input('YearGrade');

        $query = MOP_T_MOP_HEADER::select(
            'jde_no',
            'employee_name',
            DB::raw("SUM(CASE WHEN mop_type = 'LOADER' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_produksi"),
            DB::raw("SUM(CASE WHEN mop_type = 'SUPPORT' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_support")
        )
        ->where('month', $selectedMonth)
        ->where('year', $selectedYear)
        ->groupBy('jde_no', 'employee_name');

        $results = $query->get();

        $gradeDistribution = [
            'K' => 0,
            'C' => 0,
            'C+' => 0,
            'B' => 0,
            'B+' => 0,
            'BS' => 0,
            'BS+' => 0,
            'ISTIMEWA' => 0,
        ];

        foreach ($results as $score) {
            $produksi = (float) $score->point_produksi;
            $support = (float) $score->point_support;

            if ($produksi > 0 && $support > 0) {
                $final_score = round(($produksi + $support) / 2, 2);
            } else {
                $final_score = $produksi > 0 ? $produksi : $support;
            }

            $grade = '';
            if ($final_score < 2.0) {
                $grade = 'K';
            } elseif ($final_score >= 2.0 && $final_score <= 2.49) {
                $grade = 'C';
            } elseif ($final_score >= 2.5 && $final_score <= 2.99) {
                $grade = 'C+';
            } elseif ($final_score >= 3.0 && $final_score <= 3.49) {
                $grade = 'B';
            } elseif ($final_score >= 3.5 && $final_score <= 3.99) {
                $grade = 'B+';
            } elseif ($final_score >= 4.0 && $final_score <= 4.49) {
                $grade = 'BS';
            } elseif ($final_score >= 4.5 && $final_score <= 4.75) {
                $grade = 'BS+';
            } else {
                $grade = 'ISTIMEWA';
            }

            $gradeDistribution[$grade]++;
        }

        return view('pages.report.mop.gradedist', [
            'Year' => $Year,
            'Month' => $Month,
            'Data' => $gradeDistribution,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear
        ]);
    }

    public function ReportMOP24()
    {
        $Year  = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();

        $Data = [];

        return view('pages.report.mop.24month', [
            'Year' => $Year,
            'Data' => $Data,
        ]);
    }


    public function MOP24Month(Request $request)
    {
        $Year_1 = $request->input('Year_1');
        $Year_2 = $Year_1 ? ($Year_1 + 1) : null;

        $Year = MOP_T_MOP_HEADER::select('year')->groupBy('year')->orderBy('year', 'asc')->get();
        $Data = [];

        if ($Year_1 && $Year_2) {
            $allEmployees = MOP_T_MOP_HEADER::select('jde_no', 'employee_name', 'equipment_type1')
                ->groupBy('jde_no', 'employee_name', 'equipment_type1')
                ->get();

            foreach ($allEmployees as $employee) {
                $row = [
                    'jde_no' => $employee->jde_no,
                    'employee_name' => $employee->employee_name,
                    'equipment_type1' => $employee->equipment_type1,
                    'year_1' => [],
                    'year_2' => [],
                    'avg_point' => 0,
                    'grade' => ''
                ];

                $totalPoint = 0;
                $monthCount = 0;

                foreach ([$Year_1, $Year_2] as $index => $year) {
                    $yearKey = $index === 0 ? 'year_1' : 'year_2';

                    for ($m = 1; $m <= 12; $m++) {
                        $pointData = MOP_T_MOP_HEADER::selectRaw("
                                SUM(CASE WHEN mop_type = 'LOADER' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_produksi,
                                SUM(CASE WHEN mop_type = 'SUPPORT' THEN TO_NUMBER(total_point) ELSE 0 END) AS point_support
                            ")
                            ->where('jde_no', $employee->jde_no)
                            ->where('year', $year)
                            ->where('month', $m)
                            ->first();

                        $produksi = $pointData->point_produksi ?? 0;
                        $support  = $pointData->point_support ?? 0;

                        if ($produksi > 0 && $support > 0) {
                            $monthlyPoint = ($produksi + $support) / 2;
                        } elseif ($produksi > 0) {
                            $monthlyPoint = $produksi;
                        } elseif ($support > 0) {
                            $monthlyPoint = $support;
                        } else {
                            $monthlyPoint = 0;
                        }

                        $row[$yearKey][] = $monthlyPoint;

                        if ($monthlyPoint > 0) {
                            $totalPoint += $monthlyPoint;
                            $monthCount++;
                        }
                    }
                }

                $avg = $monthCount > 0 ? round($totalPoint / $monthCount, 2) : 0;
                $row['avg_point'] = $avg;

                if ($avg < 2.0) {
                    $row['grade'] = 'K';
                } elseif ($avg <= 2.49) {
                    $row['grade'] = 'C';
                } elseif ($avg <= 2.99) {
                    $row['grade'] = 'C+';
                } elseif ($avg <= 3.49) {
                    $row['grade'] = 'B';
                } elseif ($avg <= 3.99) {
                    $row['grade'] = 'B+';
                } elseif ($avg <= 4.49) {
                    $row['grade'] = 'BS';
                } elseif ($avg <= 4.75) {
                    $row['grade'] = 'BS+';
                } else {
                    $row['grade'] = 'ISTIMEWA';
                }

                $Data[] = $row;
            }
        }

        return view('pages.report.mop.24month', [
            'Year' => $Year,
            'Data' => $Data,
            'selectedYear1' => $Year_1,
            'selectedYear2' => $Year_2,
        ]);
    }

    public function ReportHMT()
    {

        $Month   = MOP_T_HMTRAIN_HOURS::selectRaw("TO_CHAR(date_activity, 'MM') as Month")->groupByRaw("TO_CHAR(date_activity, 'MM')")->orderByRaw("TO_CHAR(date_activity, 'MM') ASC")->get();
        $Year    = MOP_T_HMTRAIN_HOURS::selectRaw("TO_CHAR(date_activity, 'YYYY') as Year")->groupByRaw("TO_CHAR(date_activity, 'YYYY')")->orderByRaw("TO_CHAR(date_activity, 'YYYY') ASC")->get();
        $Site = MOP_T_HMTRAIN_HOURS::select('site')->groupBy('site')->orderBy('site', 'asc')->get();
        $Unit_class = MOP_T_HMTRAIN_HOURS::select('unit_class')->groupBy('unit_class')->orderBy('unit_class', 'asc')->get();
        $Training_type = MOP_T_HMTRAIN_HOURS::select('training_type')->groupBy('training_type')->orderBy('training_type', 'asc')->get();

        $Data = MOP_T_HMTRAIN_HOURS::selectRaw("
                            employee_name,
                            progres,
                            batch,
                            AVG(progres) as avg_progres
                        ")
                        ->groupBy('batch', 'employee_name', 'progres')
                        ->orderBy('batch', 'asc')
                        ->get();

        // $GroupedData = $Data->groupBy('batch');


        return view('pages.report.hmtrain.trainbatch', [
            // 'GroupedData' => $GroupedData,
            'Data' => $Data,
            'Year' => $Year,
            'Month' => $Month,
            'Site' => $Site,
            'Unit_class' => $Unit_class,
            'Training_type' => $Training_type,
        ]);
    }

    public function HMTSearch(Request $request)
    {

        $data   = $request->all();
        $Month   = MOP_T_HMTRAIN_HOURS::selectRaw("TO_CHAR(date_activity, 'MM') as Month")->groupByRaw("TO_CHAR(date_activity, 'MM')")->orderByRaw("TO_CHAR(date_activity, 'MM') ASC")->get();
        $Year    = MOP_T_HMTRAIN_HOURS::selectRaw("TO_CHAR(date_activity, 'YYYY') as Year")->groupByRaw("TO_CHAR(date_activity, 'YYYY')")->orderByRaw("TO_CHAR(date_activity, 'YYYY') ASC")->get();
        $Site = MOP_T_HMTRAIN_HOURS::select('site')->groupBy('site')->orderBy('site', 'asc')->get();
        $Unit_class = MOP_T_HMTRAIN_HOURS::select('unit_class')->groupBy('unit_class')->orderBy('unit_class', 'asc')->get();
        $Training_type = MOP_T_HMTRAIN_HOURS::select('training_type')->groupBy('training_type')->orderBy('training_type', 'asc')->get();

        $query = MOP_T_HMTRAIN_HOURS::selectRaw("
                            employee_name,
                            progres,
                            site,
                            date_activity,
                            unit_class,
                            training_type,
                            batch,
                            AVG(progres) as avg_progres
                        ")
                        ->groupBy('batch', 'employee_name', 'progres', 'site', 'date_activity', 'unit_class', 'training_type')
                        ->orderBy('batch', 'asc');

        // $GroupedData = $Data->groupBy('batch');

        $month = $data['Month'] ?? "";
        $year = $data['Year'] ?? "";
        $site = $data['Site'] ?? "";
        $unit = $data['Unit_class'] ?? "";
        $trtype = $data['Training_type'] ?? "";


        if ($month !== "") {
            $query->whereRaw("TO_CHAR(date_activity, 'MM') = ?", [$month]);
        }

        if ($year !== "") {
            $query->whereRaw("TO_CHAR(date_activity, 'YYYY') = ?", [$year]);
        }

        if ($site !== "") {
            $query->where('site', 'like', '%' . $site . '%');
        }

        if ($unit !== "") {
            $query->where('unit_class', 'like', '%' . $unit . '%');
        }

        if ($trtype !== "") {
            $query->where('training_type', 'like', '%' . $trtype . '%');
        }


        $Data = $query->orderBy('batch', 'asc')->get();


        return view('pages.report.hmtrain.trainbatch', [
            // 'GroupedData' => $GroupedData,
            'Data' => $Data,
            'Year' => $Year,
            'Month' => $Month,
            'Site' => $Site,
            'Unit_class' => $Unit_class,
            'Training_type' => $Training_type,
        ]);
    }

    public function ReportDayKPI()
    {
        $Week = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'IW') as Week")
            ->groupByRaw("TO_CHAR(date_activity, 'IW')")
            ->orderByRaw("TO_CHAR(date_activity, 'IW') ASC")
            ->get();
        $Month = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'MM') as Month")
            ->groupByRaw("TO_CHAR(date_activity, 'MM')")
            ->orderByRaw("TO_CHAR(date_activity, 'MM') ASC")
            ->get();

        $Year = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'YYYY') as Year")
            ->groupByRaw("TO_CHAR(date_activity, 'YYYY')")
            ->orderByRaw("TO_CHAR(date_activity, 'YYYY') ASC")
            ->get();

        $query = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("
                site,
                kpi_type,
                COUNT(*) as count
            ")
            ->groupBy('site', 'kpi_type')
            ->orderBy('kpi_type', 'asc')
            ->get();


        $kpiLabels = $query->pluck('kpi_type')->unique()->values();
        $sites = $query->pluck('site')->unique()->values();

        $ChartDatasets = [];
        foreach ($sites as $site) {
            $data = [];
            foreach ($kpiLabels as $kpi) {
                $count = $query->firstWhere(fn($item) => $item->kpi_type === $kpi && $item->site === $site);
                $data[] = $count ? $count->count : 0;
            }

            $ChartDatasets[] = [
                'label' => $site,
                'data' => $data,
            ];
        }

        $PieData = [];
        foreach ($sites as $site) {
            $data = [];
            foreach ($kpiLabels as $kpi) {
                $count = $query->firstWhere(fn($item) => $item->kpi_type === $kpi && $item->site === $site);
                $data[] = $count ? $count->count : 0;
            }

            $PieData[$site] = [
                'labels' => $kpiLabels,
                'data' => $data
            ];
        }

        return view('pages.report.dailyact.persebaranKPI', [
            'Month' => $Month,
            'Year' => $Year,
            'Week' => $Week,
            'ChartLabels' => $kpiLabels,
            'ChartDatasets' => $ChartDatasets,
            'PieData' => $PieData
        ]);
    }

    public function DayKPISearch(Request $request)
    {
        $data   = $request->all();
        $Week = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'IW') as Week")
            ->groupByRaw("TO_CHAR(date_activity, 'IW')")
            ->orderByRaw("TO_CHAR(date_activity, 'IW') ASC")
            ->get();
        $Month = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'MM') as Month")
            ->groupByRaw("TO_CHAR(date_activity, 'MM')")
            ->orderByRaw("TO_CHAR(date_activity, 'MM') ASC")
            ->get();

        $Year = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("TO_CHAR(date_activity, 'YYYY') as Year")
            ->groupByRaw("TO_CHAR(date_activity, 'YYYY')")
            ->orderByRaw("TO_CHAR(date_activity, 'YYYY') ASC")
            ->get();

        $query = MOP_T_TRAINER_DAILY_ACTIVITY::selectRaw("
                site,
                kpi_type,
                COUNT(*) as count
            ")
            ->groupBy('site', 'kpi_type')
            ->orderBy('kpi_type', 'asc');


        // seacrh
        $month = $data['Month'] ?? "";
        $year = $data['Year'] ?? "";
        $week = $data['Week'] ?? "";


        if ($month !== "") {
            $query->whereRaw("TO_CHAR(date_activity, 'MM') = ?", [$month]);
        }

        if ($year !== "") {
            $query->whereRaw("TO_CHAR(date_activity, 'YYYY') = ?", [$year]);
        }

        if ($week !== "") {
            $query->whereRaw("TO_CHAR(date_activity, 'IW') = ?", [$week]);
        }


        $dataquery = $query->get();

        $kpiLabels = $dataquery->pluck('kpi_type')->unique()->values();
        $sites = $dataquery->pluck('site')->unique()->values();

        $ChartDatasets = [];
        foreach ($sites as $site) {
            $data = [];
            foreach ($kpiLabels as $kpi) {
                $count = $dataquery->firstWhere(fn($item) => $item->kpi_type === $kpi && $item->site === $site);
                $data[] = $count ? $count->count : 0;
            }

            $ChartDatasets[] = [
                'label' => $site,
                'data' => $data,
            ];
        }

        $PieData = [];
        foreach ($sites as $site) {
            $data = [];
            foreach ($kpiLabels as $kpi) {
                $count = $dataquery->firstWhere(fn($item) => $item->kpi_type === $kpi && $item->site === $site);
                $data[] = $count ? $count->count : 0;
            }

            $PieData[$site] = [
                'labels' => $kpiLabels,
                'data' => $data
            ];
        }

        return view('pages.report.dailyact.persebaranKPI', [
            'Month' => $Month,
            'Year' => $Year,
            'Week' => $Week,
            'ChartLabels' => $kpiLabels,
            'ChartDatasets' => $ChartDatasets,
            'PieData' => $PieData
        ]);
    }


}
