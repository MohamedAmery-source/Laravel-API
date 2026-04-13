<?php

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\InstitutionManagementController;
use App\Http\Controllers\Api\Admin\InternshipMonitorController;
use App\Http\Controllers\Api\Admin\RequestReviewController;
use App\Http\Controllers\Api\Admin\StudentManagementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\Api\InstitutionPortalController;
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
                'message' => 'استخدم طريقة POST للتسجيل.',
                'data' => [
                    'allowed_method' => 'POST',
                ],
            ], 405);
        });

        Route::any('login', function () {
            if (request()->isMethod('post')) {
                return app()->call([app(AuthController::class), 'login']);
            }

            return response()->json([
                'success' => false,
                'message' => 'استخدم طريقة POST لتسجيل الدخول.',
                'data' => [
                    'allowed_method' => 'POST',
                ],
            ], 405);
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

            Route::prefix('institution')->group(function () {
                Route::get('profile', [InstitutionPortalController::class, 'profile']);
                Route::put('profile', [InstitutionPortalController::class, 'updateProfile']);
                Route::post('profile/logo', [InstitutionPortalController::class, 'uploadLogo']);

                Route::get('dashboard-stats', [InstitutionPortalController::class, 'dashboardStats']);

                Route::get('opportunities', [InstitutionPortalController::class, 'listOpportunities']);
                Route::post('opportunities', [InstitutionPortalController::class, 'storeOpportunity']);
                Route::get('opportunities/{id}', [InstitutionPortalController::class, 'showOpportunity']);
                Route::put('opportunities/{id}', [InstitutionPortalController::class, 'updateOpportunity']);
                Route::patch('opportunities/{id}/status', [InstitutionPortalController::class, 'changeOpportunityStatus']);

                Route::get('requests', [InstitutionPortalController::class, 'listRequests']);
                Route::get('requests/{id}', [InstitutionPortalController::class, 'showRequest']);
                Route::patch('requests/{id}/accept', [InstitutionPortalController::class, 'acceptRequest']);
                Route::patch('requests/{id}/reject', [InstitutionPortalController::class, 'rejectRequest']);

                Route::get('internships', [InstitutionPortalController::class, 'listInternships']);
                Route::get('internships/{id}/reports', [InstitutionPortalController::class, 'internshipReports']);
                Route::post('internships/{id}/evaluate', [InstitutionPortalController::class, 'evaluateInternship']);

                Route::get('complaints', [InstitutionPortalController::class, 'listComplaints']);
                Route::post('complaints', [InstitutionPortalController::class, 'storeComplaint']);
            });

            Route::prefix('student')->group(function () {
                Route::post('requests', [InstitutionPortalController::class, 'studentStoreRequest']);
            });
        });
    });

    Route::options('/{any}', function () {
        return response('', 200);
    })->where('any', '.*');
};

$registerEducationRoutes('');
$registerEducationRoutes('education');
$registerEducationRoutes('education/api');
