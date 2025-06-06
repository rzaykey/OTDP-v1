<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MentoringController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\MOPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiMobileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/mentoring/{id}/update', [ApiMobileController::class, 'apiMentoringUpdate']);
    Route::delete('/mentoring/{id}/delete', [ApiMobileController::class, 'apiMentorDelete']);
    Route::post('/mentoring/store', [ApiMobileController::class, 'apiMentoringStore']);
    Route::post('/dayActivities', [ApiMobileController::class, 'apiDayActStore']);
    Route::get('/dayActivities/createDailyAct', [ApiMobileController::class, 'apiDayActCreate']);
    Route::put('/dayActivities/{id}/update', [ApiMobileController::class, 'apiDayActUpdate']);
    Route::delete('/dayActivities/{id}/delete', [ApiMobileController::class, 'apiDayActDelete']);

    // Tambahkan endpoint lain yang butuh login di sini
});
// Route::middleware('auth')->group(function () {
Route::get('/getEmployeeOperator', [MasterController::class, 'EmployeeOperator'])->name('employee.operator');
Route::get('/getEmployeeAuth', [MasterController::class, 'getEmployeeAuth'])->name('employee.auth');

Route::get('/getMasterClassUnit', [MasterController::class, 'getMasterClassUnit'])->name('unit.classunit');
Route::get('/getMasterTypeUnit', [MasterController::class, 'getMasterTypeUnit'])->name('unit.typeunit');
Route::get('/getMasterModelUnit', [MasterController::class, 'getMasterModelUnit'])->name('unit.modelunit');
Route::get('/getMasterModelUnitbasedType', [MasterController::class, 'getMasterModelUnitbasedType'])->name('unit.modelunitbasedtype');
Route::get('/getMasterUnit', [MasterController::class, 'getMasterUnit'])->name('unit.unit');

Route::post('/getTotalHM', [TrainerController::class, 'getTotalHM'])->name('trainer.totalHM');
Route::get('/getActivity', [MasterController::class, 'getActivity'])->name('activity.master');
Route::get('/getKPI', [MasterController::class, 'getKPI'])->name('activity.kpi');

Route::get('/mop-data', [MOPController::class, 'MOPData'])->name('MOPData');
Route::get('/mop-dataSimple', [MOPController::class, 'MOPDataSimple'])->name('MOPDataSimple');
Route::get('/mop-dataCompile', [MOPController::class, 'MOPDataCompile'])->name('MOPDataCompile');

Route::get('/mentoringdashboard-table', [MentoringController::class, 'MentoringDashboard_Table'])->name('MentoringDashboard_Table');
Route::get('/mentoring-dataDB', [MentoringController::class, 'MentoringDataDB'])->name('MentoringDataDB');

Route::get('/dayact-data', [TrainerController::class, 'DayActData'])->name('DayActData');
Route::get('/trainhour-data', [TrainerController::class, 'HMTrainData'])->name('HMTrainData');

// Route API mobile
Route::get('/getModelUnit', [ApiMobileController::class, 'apiModelUnit']);
Route::get('/mentoring-data', [ApiMobileController::class, 'MentoringData'])->name('MentoringData');
Route::get('/mentoring/{id}/edit', [ApiMobileController::class, 'apiMentoringEdit']);
Route::get('/mentoring/createData', [ApiMobileController::class, 'apiMentoringCreate']);
Route::get('/dayActivities', [ApiMobileController::class, 'apiDayActIndex']);
Route::get('/dayActivities/{id}/edit', [ApiMobileController::class, 'apiDayActEdit']);
// });
