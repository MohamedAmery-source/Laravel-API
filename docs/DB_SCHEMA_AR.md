# مخطط قاعدة البيانات (جداول المشروع فقط)


إجمالي جداول المشروع: **19**

## complaints

> الاسم العربي: **الشكاوى**
> الوظيفة: حفظ شكاوى المستخدمين وحالة معالجتها

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| complaint_id | bigint unsigned | NO | - | PK |
| user_id | bigint unsigned | YES | - | IDX |
| title | varchar(150) | NO | - | - |
| description | text | NO | - | - |
| status | enum('pending','in_progress','resolved') | NO | pending | - |
| resolved_at | timestamp | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## documents

> الاسم العربي: **المستندات**
> الوظيفة: ملفات مرفوعة مثل CV والوثائق المرتبطة بالطلبات

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| document_id | bigint unsigned | NO | - | PK |
| user_id | bigint unsigned | YES | - | IDX |
| request_id | bigint unsigned | YES | - | IDX |
| title | varchar(150) | YES | - | - |
| file_url | varchar(255) | NO | - | - |
| file_type | varchar(50) | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## evaluations

> الاسم العربي: **التقييمات**
> الوظيفة: نتائج تقييم المتدرب من المؤسسة/المشرف/الطالب

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| evaluation_id | bigint unsigned | NO | - | PK |
| internship_id | bigint unsigned | YES | - | IDX |
| evaluator_type | enum('institution','supervisor','student') | NO | - | - |
| technical_skills | int | YES | - | - |
| commitment | int | YES | - | - |
| teamwork | int | YES | - | - |
| attendance | int | YES | - | - |
| overall_rating | int | YES | - | - |
| final_score | int | YES | - | - |
| comments | text | YES | - | - |
| evaluation_date | date | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## general_settings

> الاسم العربي: **الإعدادات العامة**
> الوظيفة: إعدادات النظام العامة مثل اسم الموقع وسياسات النظام

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| setting_id | bigint unsigned | NO | - | PK |
| site_name | varchar(100) | NO | - | - |
| site_logo | varchar(255) | YES | - | - |
| system_status | bigint unsigned | YES | - | IDX |
| content_email | varchar(150) | NO | - | UK |
| content_phone | varchar(20) | YES | - | - |
| privacy_policy | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## institutions

> الاسم العربي: **المؤسسات التدريبية**
> الوظيفة: بيانات الجهات التي تطرح فرص التدريب

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| institution_id | bigint unsigned | NO | - | PK |
| user_id | bigint unsigned | YES | - | UK |
| name | varchar(150) | NO | - | - |
| commercial_register | varchar(100) | YES | - | - |
| address | text | YES | - | - |
| description | text | YES | - | - |
| website | varchar(255) | YES | - | - |
| social_links | json | YES | - | - |
| contact_person | varchar(100) | YES | - | - |
| contact_phone | varchar(20) | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## internships

> الاسم العربي: **التدريبات**
> الوظيفة: سجل التدريب الفعلي بعد قبول طلب التدريب

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| internship_id | bigint unsigned | NO | - | PK |
| request_id | bigint unsigned | YES | - | UK |
| actual_start_date | date | YES | - | - |
| actual_end_date | date | YES | - | - |
| mentor_name | varchar(100) | YES | - | - |
| assigned_tasks | text | YES | - | - |
| status | enum('active','completed','cancelled') | NO | active | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## lookup_masters

> الاسم العربي: **مراجع القيم**
> الوظيفة: تعريف أنواع Lookup الرئيسية

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| lookup_id | bigint unsigned | NO | - | PK |
| lookup_code | varchar(50) | NO | - | UK |
| description | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## lookup_values

> الاسم العربي: **قيم المراجع**
> الوظيفة: القيم التابعة لكل Lookup مثل الحالات

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| value_id | bigint unsigned | NO | - | PK |
| lookup_id | bigint unsigned | YES | - | IDX |
| value_code | varchar(50) | NO | - | - |
| description | text | YES | - | - |
| value_data | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## menu_types

> الاسم العربي: **أنواع القوائم**
> الوظيفة: تعريف وتصنيف عناصر القوائم

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| menu_type_id | bigint unsigned | NO | - | PK |
| type_name | varchar(100) | NO | - | - |
| order_index | int | YES | 0 | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## notifications

> الاسم العربي: **الإشعارات**
> الوظيفة: إشعارات النظام المرسلة للمستخدمين

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| notification_id | bigint unsigned | NO | - | PK |
| user_id | bigint unsigned | YES | - | IDX |
| message | text | NO | - | - |
| notification_type | varchar(50) | YES | - | - |
| related_request_id | bigint unsigned | YES | - | IDX |
| is_read | tinyint(1) | YES | 0 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## permissions

> الاسم العربي: **الصلاحيات**
> الوظيفة: الصلاحيات التفصيلية المرتبطة بالأدوار

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| permission_id | bigint unsigned | NO | - | PK |
| permission_name | varchar(100) | NO | - | UK |
| module | varchar(50) | YES | - | - |
| description | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## role_permissions

> الاسم العربي: **صلاحيات الأدوار**
> الوظيفة: ربط الأدوار بالصلاحيات

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| role_id | bigint unsigned | YES | - | PK |
| permission_id | bigint unsigned | YES | - | PK, IDX |
| granted | tinyint(1) | YES | 1 | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## roles

> الاسم العربي: **الأدوار**
> الوظيفة: تعريف أدوار النظام

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| role_id | bigint unsigned | NO | - | PK |
| role_name | varchar(50) | NO | - | UK |
| description | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## students

> الاسم العربي: **الطلاب**
> الوظيفة: الملف الأكاديمي والمهاري للطالب

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| student_id | bigint unsigned | NO | - | PK |
| user_id | bigint unsigned | YES | - | UK |
| student_number | varchar(20) | NO | - | UK |
| university | varchar(150) | YES | - | - |
| department | varchar(100) | NO | - | - |
| level | varchar(10) | NO | - | - |
| gpa | decimal(3,2) | YES | - | - |
| city | varchar(100) | YES | - | - |
| skills | json | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## training_opportunities

> الاسم العربي: **الفرص التدريبية**
> الوظيفة: إعلانات فرص التدريب المطروحة من المؤسسات

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| opportunity_id | bigint unsigned | NO | - | PK |
| institution_id | bigint unsigned | YES | - | IDX |
| title | varchar(200) | NO | - | - |
| department | varchar(150) | YES | - | - |
| description | text | YES | - | - |
| required_skills | text | YES | - | - |
| available_seats | int | YES | 1 | - |
| city | varchar(120) | YES | - | - |
| training_type | enum('summer','cooperative') | YES | - | - |
| custom_questions | json | YES | - | - |
| status | enum('active','closed') | NO | active | - |
| start_date | date | YES | - | - |
| end_date | date | YES | - | - |
| application_deadline | date | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## training_reports

> الاسم العربي: **تقارير التدريب**
> الوظيفة: التقارير الدورية المرفوعة أثناء التدريب

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| report_id | bigint unsigned | NO | - | PK |
| internship_id | bigint unsigned | YES | - | IDX |
| title | varchar(200) | YES | - | - |
| content | text | YES | - | - |
| report_file | varchar(255) | YES | - | - |
| week_number | int | YES | - | - |
| submitted_by | enum('student','institution','supervisor') | NO | - | - |
| submission_date | date | YES | - | - |
| is_approved | tinyint(1) | YES | 0 | - |
| supervisor_comments | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## training_requests

> الاسم العربي: **طلبات التدريب**
> الوظيفة: طلبات تقديم الطلاب على الفرص وحالتها

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| request_id | bigint unsigned | NO | - | PK |
| student_id | bigint unsigned | YES | - | IDX |
| opportunity_id | bigint unsigned | YES | - | IDX |
| submission_date | date | YES | - | - |
| status | enum('pending','pending_admin','pending_institution','under_review','approved','rejected','completed') | NO | pending_admin | - |
| student_notes | text | YES | - | - |
| student_answers | text | YES | - | - |
| admin_notes | text | YES | - | - |
| institution_notes | text | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## user_roles

> الاسم العربي: **أدوار المستخدمين**
> الوظيفة: ربط المستخدمين بالأدوار

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| user_id | bigint unsigned | YES | - | PK |
| role_id | bigint unsigned | YES | - | PK, IDX |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| assigned_at | timestamp | YES | CURRENT_TIMESTAMP | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

## users

> الاسم العربي: **المستخدمون**
> الوظيفة: الحسابات الأساسية لجميع أنواع المستخدمين

| الحقل | النوع | Null | Default | Key |
|---|---|---|---|---|
| user_id | bigint unsigned | NO | - | PK |
| full_name | varchar(150) | NO | - | - |
| email | varchar(150) | NO | - | UK |
| password | varchar(191) | NO | - | - |
| phone | varchar(20) | YES | - | - |
| user_type | enum('student','institution','supervisor','admin') | NO | - | - |
| profile_picture | varchar(255) | YES | - | - |
| status | enum('active','inactive','pending_approval','suspended') | NO | active | - |
| email_verified_at | timestamp | YES | - | - |
| last_login_at | timestamp | YES | - | - |
| is_active | tinyint(1) | YES | 1 | - |
| created_by | bigint unsigned | YES | - | - |
| updated_by | bigint unsigned | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

