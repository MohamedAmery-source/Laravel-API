<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\InstitutionManagementController;
use App\Http\Controllers\Api\Admin\InternshipMonitorController;
use App\Http\Controllers\Api\Admin\RequestReviewController;
use App\Http\Controllers\Api\Admin\StudentManagementController;
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

$registerEducationRoutes = function (string $prefix = ''): void {
    Route::prefix($prefix)->group(function () {
        Route::any('register', function () {
            if (request()->isMethod('post')) {
                return app()->call([app(AuthController::class), 'register']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Use POST for register.',
            ], 200);
        });

        Route::any('login', function () {
            if (request()->isMethod('post')) {
                return app()->call([app(AuthController::class), 'login']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Use POST for login.',
            ], 200);
        });

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

            Route::prefix('admin')->group(function () {
                Route::get('dashboard-stats', [AdminDashboardController::class, 'stats']);

                Route::get('students', [StudentManagementController::class, 'index']);
                Route::post('students', [StudentManagementController::class, 'store']);
                Route::put('students/{id}', [StudentManagementController::class, 'update']);
                Route::patch('students/{id}/status', [StudentManagementController::class, 'changeStatus']);

                Route::get('institutions', [InstitutionManagementController::class, 'index']);
                Route::post('institutions', [InstitutionManagementController::class, 'store']);
                Route::put('institutions/{id}', [InstitutionManagementController::class, 'update']);
                Route::patch('institutions/{id}/approve', [InstitutionManagementController::class, 'approve']);
                Route::patch('institutions/{id}/status', [InstitutionManagementController::class, 'changeStatus']);

                Route::get('requests', [RequestReviewController::class, 'index']);
                Route::get('requests/{id}', [RequestReviewController::class, 'show']);
                Route::patch('requests/{id}/approve', [RequestReviewController::class, 'approve']);
                Route::patch('requests/{id}/reject', [RequestReviewController::class, 'reject']);

                Route::get('internships', [InternshipMonitorController::class, 'index']);
                Route::get('internships/{id}', [InternshipMonitorController::class, 'show']);
            });
        });
    });
    Route::options('/{any}', function () {
    return response('', 200);
})->where('any', '.*');

};

// Laravel default API prefix: /api/...
$registerEducationRoutes('');

// React: /api/education/...
$registerEducationRoutes('education');

// Flutter: /api/education/api/...
$registerEducationRoutes('education/api');
