<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\Api\InternshipController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TrainingReportController;
use App\Http\Controllers\Api\TrainingRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

$registerEducationRoutes = function (string $prefix): void {
    Route::prefix($prefix)->middleware('api')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::get('opportunities', [OpportunityController::class, 'index']);
        Route::get('opportunities/{id}', [OpportunityController::class, 'show']);
        Route::get('lookups', [LookupController::class, 'index']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [AuthController::class, 'profile']);
            Route::post('change-password', [AuthController::class, 'changePassword']);

            Route::apiResource('students', StudentController::class);
            Route::apiResource('institutions', InstitutionController::class);
            Route::apiResource('opportunities', OpportunityController::class)->only(['store', 'update', 'destroy']);

            Route::apiResource('training-requests', TrainingRequestController::class)->only(['index', 'store']);
            Route::put('training-requests/{id}/status', [TrainingRequestController::class, 'changeStatus']);

            Route::apiResource('internships', InternshipController::class)->only(['index']);
            Route::apiResource('reports', TrainingReportController::class)->only(['index', 'store']);
            Route::apiResource('evaluations', EvaluationController::class)->only(['index', 'store']);

            Route::post('documents/upload', [DocumentController::class, 'upload']);
            Route::apiResource('complaints', ComplaintController::class)->only(['index', 'store', 'show']);
            Route::apiResource('notifications', NotificationController::class)->only(['index']);

            Route::get('settings', [SettingController::class, 'index']);
            Route::put('settings', [SettingController::class, 'update']);
            Route::get('roles', [RoleController::class, 'index']);
        });
    });
};

$registerEducationRoutes('education');
$registerEducationRoutes('education/api');
