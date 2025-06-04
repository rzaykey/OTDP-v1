<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentoringController;
use App\Http\Controllers\MOPController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\MOP_T_HEADER;
use Illuminate\Http\Client\Request;

// Dashboard


// Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('cekLogin');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');


// Protected routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');  // Create this view as your main content
    })->name('dashboard');

    Route::group(['prefix' => 'master'], function () {
        Route::get('/no-unit', [MasterController::class, 'MasterNoUnit'])->name('MasterNoUnit');
        Route::post('/no-unit/store', [MasterController::class, 'MasterUnitStore'])->name('MasterUnitStore');
        Route::get('/no-unit{id}/edit', [MasterController::class, 'MasterUnitEdit'])->name('MasterUnitEdit');
        Route::post('/no-unit/update', [MasterController::class, 'MasterUnitUpdate'])->name('MasterUnitUpdate');
        Route::delete('/no-unit/{id}', [MasterController::class, 'MasterUnitDelete'])->name('MasterUnitDelete');


        Route::get('/activity', [MasterController::class, 'MasterActivity'])->name('MasterActivity');
        Route::post('/activity/store', [MasterController::class, 'MasterActivityStore'])->name('MasterActivityStore');
        Route::get('/activity{id}/edit', [MasterController::class, 'MasterActivityEdit'])->name('MasterActivityEdit');
        Route::post('/activity/update', [MasterController::class, 'MasterActivityUpdate'])->name('MasterActivityUpdate');
        Route::delete('/activity/{id}', [MasterController::class, 'MasterActivityDelete'])->name('MasterActivityDelete');
    });

    Route::group(['prefix' => 'mop'], function () {
        Route::get('/', [MOPController::class, 'MOPIndex'])->name('MOPIndex');
        Route::get('/create', [MOPController::class, 'MOPCreate'])->name('MOPCreate');
        Route::post('/store', [MOPController::class, 'MOPStore'])->name('MOPStore');
        Route::post('/export-mop', [MOPController::class, 'MOPExport'])->name('MOPExport');
        Route::post('/import-mop', [MOPController::class, 'MOPImport'])->name('MOPImport');
        Route::get('/importemplate-mop', [MOPController::class, 'MOPImportTemplate'])->name('MOPImportTemplate');
        Route::get('/dashboard', [MOPController::class, 'MOPDashboard'])->name('MOPDashboard');
    });

    Route::group(['prefix' => 'Mentoring'], function () {
        Route::get('/', [MentoringController::class, 'MentoringIndex'])->name('MentoringIndex');
        Route::get('create/{type}', [MentoringController::class, 'MentoringCreate'])->name('MentoringCreate');
        Route::post('/store', [MentoringController::class, 'MentoringStore'])->name('MentoringStore');
        Route::get('/dashboard', [MentoringController::class, 'MentoringDashboard'])->name('MentoringDashboard');
        Route::get('/edit/{id}', [MentoringController::class, 'MentoringEdit'])->name('MentoringEdit');
        Route::post('/update', [MentoringController::class, 'MentoringUpdate'])->name('MentoringUpdate');
        Route::get('/dashboard', [MentoringController::class, 'MentoringDashboard'])->name('MentoringDashboard');
        Route::get('/dashboard2', [MentoringController::class, 'MentoringDashboard2'])->name('MentoringDashboard2');
    });

    Route::group(['prefix' => 'Trainer'], function () {
        Route::group(['prefix' => 'daily-activity'], function () {
            Route::get('/', [TrainerController::class, 'DayActIndex'])->name('DayActIndex');
            Route::get('/create', [TrainerController::class, 'DayActCreate'])->name('DayActCreate');
            Route::post('/store', [TrainerController::class, 'DayActStore'])->name('DayActStore');
            Route::delete('/{id}', [TrainerController::class, 'DayActDelete'])->name('DayActDelete');
            Route::get('/{id}/edit', [TrainerController::class, 'DayActEdit'])->name('DayActEdit');
            Route::post('/update', [TrainerController::class, 'DayActUpdate'])->name('DayActUpdate');

            Route::post('/export-dayact', [TrainerController::class, 'DayActExport'])->name('DayActExport');
            Route::post('/import-dayact', [TrainerController::class, 'DayActImport'])->name('DayActImport');
            Route::get('/importemplate-dayact', [TrainerController::class, 'DayActImportTemplate'])->name('DayActImportTemplate');
        });


        Route::group(['prefix' => 'hmtrain-hours'], function () {
            Route::get('/', [TrainerController::class, 'HMTrainIndex'])->name('HMTrainIndex');
            Route::get('/create', [TrainerController::class, 'HMTrainCreate'])->name('HMTrainCreate');
            Route::post('/store', [TrainerController::class, 'HMTrainStore'])->name('HMTrainStore');

            Route::delete('/{id}', [TrainerController::class, 'HMTrainDelete'])->name('HMTrainDelete');
            Route::get('/{id}/edit', [TrainerController::class, 'HMTrainEdit'])->name('HMTrainEdit');
            Route::post('/update', [TrainerController::class, 'HMTrainUpdate'])->name('HMTrainUpdate');

            Route::post('/export-hmtrain-hours', [TrainerController::class, 'HMTrainExport'])->name('HMTrainExport');
            Route::post('/import-hmtrain-hours', [TrainerController::class, 'HMTrainImport'])->name('HMTrainImport');
            Route::get('/importemplate-hmtrain-hours', [TrainerController::class, 'HMTrainImportTemplate'])->name('HMTrainImportTemplate');
        });
    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('/mop', [ReportController::class, 'ReportMOP'])->name('ReportMOP');
        Route::get('/mop-grade', [ReportController::class, 'ReportGradeMOP'])->name('ReportGradeMOP');
        Route::get('/mop-24-month', [ReportController::class, 'ReportMOP24'])->name('ReportMOP24');
        Route::post('/mop-monthly', [ReportController::class, 'MOPSearch'])->name('MOPSearch');
        Route::post('/mop-grade-monthly', [ReportController::class, 'MOPGradeDistribution'])->name('MOPGradeDistribution');
        Route::post('/mop-record-24month', [ReportController::class, 'MOP24Month'])->name('MOP24Month');

        Route::get('/HM-train-hours', [ReportController::class, 'ReportHMT'])->name('ReportHMT');
        Route::post('/HM-train-search', [ReportController::class, 'HMTSearch'])->name('HMTSearch');

        Route::get('/daily-activity-KPI', [ReportController::class, 'ReportDayKPI'])->name('ReportDayKPI');
        Route::post('/daily-KPI-search', [ReportController::class, 'DayKPISearch'])->name('DayKPISearch');
    });
});
