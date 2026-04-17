<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Document;
use App\Models\Evaluation;
use App\Models\GeneralSetting;
use App\Models\Institution;
use App\Models\Internship;
use App\Models\LookupMaster;
use App\Models\LookupValue;
use App\Models\MenuType;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Student;
use App\Models\TrainingOpportunity;
use App\Models\TrainingReport;
use App\Models\TrainingRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@education.local',
            'password' => Hash::make('Password123!'),
            'user_type' => 'admin',
            'status' => 'active',
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'full_name' => 'Omar Hassan',
            'email' => 'omar@student.local',
            'password' => Hash::make('Password123!'),
            'user_type' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        $institutionUser = User::create([
            'full_name' => 'Future Tech Inc',
            'email' => 'hr@futuretech.local',
            'password' => Hash::make('Password123!'),
            'user_type' => 'institution',
            'status' => 'active',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->user_id,
            'student_number' => 'STU-1001',
            'department' => 'Computer Science',
            'level' => 'Level 4',
            'gpa' => 4.2,
            'is_active' => true,
        ]);

        $institution = Institution::create([
            'user_id' => $institutionUser->user_id,
            'name' => 'Future Tech Inc',
            'address' => 'Sana\'a, Zubairi Street',
            'description' => 'A technology company specializing in software platforms.',
            'website' => 'https://futuretech.local',
            'contact_person' => 'Sara Ali',
            'contact_phone' => '+967712345678',
            'is_active' => true,
        ]);

        $opportunity = TrainingOpportunity::create([
            'institution_id' => $institution->institution_id,
            'title' => 'Backend Developer Internship',
            'description' => 'Work on APIs and database optimization.',
            'required_skills' => 'Laravel, SQL, REST',
            'available_seats' => 3,
            'start_date' => now()->addDays(14)->toDateString(),
            'end_date' => now()->addDays(104)->toDateString(),
            'application_deadline' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ]);

        $trainingRequest = TrainingRequest::create([
            'student_id' => $student->student_id,
            'opportunity_id' => $opportunity->opportunity_id,
            'submission_date' => now()->toDateString(),
            'status' => 'approved',
            'student_notes' => 'Interested in backend work.',
            'admin_notes' => null,
            'institution_notes' => 'Good academic record.',
            'is_active' => true,
        ]);

        $internship = Internship::create([
            'request_id' => $trainingRequest->request_id,
            'actual_start_date' => now()->addDays(20)->toDateString(),
            'actual_end_date' => now()->addDays(110)->toDateString(),
            'mentor_name' => 'Mahmoud Saleh',
            'assigned_tasks' => 'API development, integration testing',
            'status' => 'active',
            'is_active' => true,
        ]);

        TrainingReport::create([
            'internship_id' => $internship->internship_id,
            'title' => 'Week 1 Report',
            'content' => 'Worked on authentication and user models.',
            'report_file' => 'reports/week-1.pdf',
            'submitted_by' => 'student',
            'submission_date' => now()->toDateString(),
            'is_approved' => false,
            'supervisor_comments' => null,
            'is_active' => true,
        ]);

        Evaluation::create([
            'internship_id' => $internship->internship_id,
            'evaluator_type' => 'supervisor',
            'technical_skills' => 4,
            'commitment' => 5,
            'teamwork' => 4,
            'attendance' => 5,
            'overall_rating' => 4,
            'comments' => 'Strong performer with good communication.',
            'evaluation_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        Document::create([
            'user_id' => $studentUser->user_id,
            'request_id' => $trainingRequest->request_id,
            'title' => 'CV',
            'file_url' => 'documents/cv-omar.pdf',
            'file_type' => 'pdf',
            'is_active' => true,
        ]);

        Complaint::create([
            'user_id' => $studentUser->user_id,
            'title' => 'Login Issue',
            'description' => 'I cannot login from mobile network.',
            'status' => 'pending',
            'resolved_at' => null,
        ]);

        Notification::create([
            'user_id' => $studentUser->user_id,
            'message' => 'تم قبول طلب التدريب الخاص بك.',
            'notification_type' => 'request_status',
            'related_request_id' => $trainingRequest->request_id,
            'is_read' => false,
        ]);

        $roleAdmin = Role::create([
            'role_name' => 'admin',
            'description' => 'System Administrator',
            'is_active' => true,
        ]);

        $roleStudent = Role::create([
            'role_name' => 'student',
            'description' => 'Student User',
            'is_active' => true,
        ]);

        $roleInstitution = Role::create([
            'role_name' => 'institution',
            'description' => 'Institution User',
            'is_active' => true,
        ]);

        $permManageUsers = Permission::create([
            'permission_name' => 'manage_users',
            'module' => 'users',
            'description' => 'Manage users and roles',
            'is_active' => true,
        ]);

        $permManageOpportunities = Permission::create([
            'permission_name' => 'manage_opportunities',
            'module' => 'opportunities',
            'description' => 'Create and update opportunities',
            'is_active' => true,
        ]);

        RolePermission::create([
            'role_id' => $roleAdmin->role_id,
            'permission_id' => $permManageUsers->permission_id,
            'granted' => true,
            'is_active' => true,
        ]);

        RolePermission::create([
            'role_id' => $roleInstitution->role_id,
            'permission_id' => $permManageOpportunities->permission_id,
            'granted' => true,
            'is_active' => true,
        ]);

        UserRole::create([
            'user_id' => $admin->user_id,
            'role_id' => $roleAdmin->role_id,
            'is_active' => true,
        ]);

        UserRole::create([
            'user_id' => $studentUser->user_id,
            'role_id' => $roleStudent->role_id,
            'is_active' => true,
        ]);

        UserRole::create([
            'user_id' => $institutionUser->user_id,
            'role_id' => $roleInstitution->role_id,
            'is_active' => true,
        ]);

        $lookupCities = LookupMaster::create([
            'lookup_code' => 'cities',
            'description' => 'Available cities',
            'is_active' => true,
        ]);

        $sanaa = LookupValue::create([
            'lookup_id' => $lookupCities->lookup_id,
            'value_code' => 'sanaa',
            'description' => 'Sana\'a',
            'value_data' => 'Sana\'a',
            'is_active' => true,
        ]);

        LookupValue::create([
            'lookup_id' => $lookupCities->lookup_id,
            'value_code' => 'aden',
            'description' => 'Aden',
            'value_data' => 'Aden',
            'is_active' => true,
        ]);

        GeneralSetting::create([
            'site_name' => 'Education Training Portal',
            'site_logo' => 'logos/main.png',
            'system_status' => $sanaa->value_id,
            'content_email' => 'support@education.local',
            'content_phone' => '+967100000000',
            'privacy_policy' => 'Default privacy policy',
            'is_active' => true,
        ]);

        MenuType::create([
            'type_name' => 'main',
            'order_index' => 1,
            'is_active' => true,
        ]);
    }
}
