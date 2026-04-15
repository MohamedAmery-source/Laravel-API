# دليل API للفرونت (Admin / Institution / Student)

هذا الملف مبني من الكود الفعلي في `routes/api.php` و Controllers.

## 1) قاعدة المسارات المعتمدة

- المسار الأساسي المقترح للاستخدام في الفرونت: `/api/...`
- نفس الـ API مسجل أيضًا بنسختين مكررتين:
- `/api/education/...`
- `/api/education/api/...`
- الأفضل توحيد الفرونت على `/api` فقط.

## 2) المصادقة والتهيئة

- جميع مسارات `/api` (عدا العامة) تتطلب `Bearer Token` (Laravel Sanctum).
- Header:
- `Authorization: Bearer <token>`
- `Accept: application/json`

### مسارات عامة (بدون Token)

| Method | Path | الاستخدام |
|---|---|---|
| `POST` (فعليًا) | `/api/register` | تسجيل طالب جديد فقط (`user_type=student`) |
| `POST` (فعليًا) | `/api/login` | تسجيل الدخول |
| `GET` | `/api/opportunities` | عرض الفرص النشطة |
| `GET` | `/api/opportunities/{id}` | تفاصيل فرصة |
| `GET` | `/api/lookups?type=...` | القيم المرجعية |
| `OPTIONS` | `/api/{any}` | CORS preflight |

ملاحظة: `register/login` معرفان كـ `Route::any` لكن الكود يقبل POST فقط ويرجع 405 لباقي الطرق.

### مسارات مشتركة بعد تسجيل الدخول

| Method | Path | الاستخدام |
|---|---|---|
| `POST` | `/api/logout` | تسجيل الخروج |
| `GET` | `/api/profile` | بيانات المستخدم الحالي |
| `POST` | `/api/change-password` | تغيير كلمة المرور |
| `POST` | `/api/documents/upload` | رفع ملف عام مرتبط بالمستخدم/طلب تدريب |
| `GET` | `/api/notifications` | إشعارات المستخدم الحالي |
| `GET` | `/api/complaints` | قائمة الشكاوى (General API) |
| `POST` | `/api/complaints` | إنشاء شكوى (General API) |
| `GET` | `/api/complaints/{complaint}` | تفاصيل شكوى |
| `GET` | `/api/settings` | إعدادات عامة |
| `PUT` | `/api/settings` | تحديث الإعدادات |
| `GET` | `/api/roles` | الأدوار والصلاحيات |

## 3) مسارات الأدمن (Admin Portal)

> جميعها تتطلب مستخدم `user_type=admin` وحالة `active`.

| Method | Path | الاستخدام |
|---|---|---|
| `GET` | `/api/admin/dashboard-stats` | إحصائيات لوحة الأدمن |
| `GET` | `/api/admin/students` | قائمة الطلاب (فلترة: `q`, `department`, `status`, `per_page`) |
| `POST` | `/api/admin/students` | إنشاء طالب من الأدمن |
| `PUT` | `/api/admin/students/{id}` | تحديث بيانات طالب |
| `PATCH` | `/api/admin/students/{id}/status` | تغيير حالة طالب (`active/suspended`) |
| `GET` | `/api/admin/institutions` | قائمة المؤسسات (فلترة: `q`, `status`, `per_page`) |
| `POST` | `/api/admin/institutions` | إنشاء مؤسسة من الأدمن |
| `PUT` | `/api/admin/institutions/{id}` | تحديث مؤسسة |
| `PATCH` | `/api/admin/institutions/{id}/approve` | اعتماد مؤسسة مباشرة (`active`) |
| `PATCH` | `/api/admin/institutions/{id}/status` | تغيير حالة مؤسسة (`active/pending_approval/suspended`) |
| `GET` | `/api/admin/requests` | مراجعة طلبات التدريب (افتراضيًا `pending_admin/pending`) |
| `GET` | `/api/admin/requests/{id}` | تفاصيل طلب تدريب |
| `PATCH` | `/api/admin/requests/{id}/approve` | اعتماد أكاديمي -> يحول الطلب إلى `pending_institution` |
| `PATCH` | `/api/admin/requests/{id}/reject` | رفض أكاديمي -> `rejected` |
| `GET` | `/api/admin/internships` | مراقبة التدريبات (فلترة: `status`, `student_id`, `institution_id`) |
| `GET` | `/api/admin/internships/{id}` | تفاصيل تدريب + تقارير + تقييمات |

## 4) مسارات المؤسسة (Institution Portal)

> تتطلب `user_type=institution`. أغلب العمليات تتطلب المؤسسة فعالة `active`.

| Method | Path | الاستخدام |
|---|---|---|
| `GET` | `/api/institution/profile` | ملف المؤسسة |
| `PUT` | `/api/institution/profile` | تحديث ملف المؤسسة |
| `POST` | `/api/institution/profile/logo` | رفع شعار المؤسسة |
| `GET` | `/api/institution/dashboard-stats` | إحصائيات المؤسسة |
| `GET` | `/api/institution/opportunities` | فرص المؤسسة |
| `POST` | `/api/institution/opportunities` | إنشاء فرصة (`status: active/closed`) |
| `GET` | `/api/institution/opportunities/{id}` | تفاصيل فرصة |
| `PUT` | `/api/institution/opportunities/{id}` | تحديث فرصة |
| `PATCH` | `/api/institution/opportunities/{id}/status` | تغيير حالة فرصة (`active/closed`) |
| `GET` | `/api/institution/requests` | طلبات المرشحين بانتظار المؤسسة (`pending_institution`) |
| `GET` | `/api/institution/requests/{id}` | تفاصيل طلب متقدم |
| `PATCH` | `/api/institution/requests/{id}/accept` | قبول الطلب -> `approved` + إنشاء `Internship(active)` |
| `PATCH` | `/api/institution/requests/{id}/reject` | رفض الطلب -> `rejected` |
| `GET` | `/api/institution/internships` | قائمة تدريبات المؤسسة |
| `GET` | `/api/institution/internships/{id}/reports` | تقارير تدريب |
| `POST` | `/api/institution/internships/{id}/evaluate` | تقييم متدرب (score 1..100) |
| `GET` | `/api/institution/complaints` | شكاوى المؤسسة |
| `POST` | `/api/institution/complaints` | إنشاء شكوى مؤسسة |

## 5) مسارات الطالب (Student Portal)

> تتطلب `user_type=student` وحساب فعال.

| Method | Path | الاستخدام |
|---|---|---|
| `GET` | `/api/student/profile` | ملف الطالب + نسبة اكتمال |
| `PUT` | `/api/student/profile` | تحديث ملف الطالب |
| `POST` | `/api/student/profile/cv` | رفع السيرة الذاتية PDF |
| `GET` | `/api/student/dashboard-stats` | إحصائيات الطالب + فرص مقترحة |
| `GET` | `/api/student/timeline` | Timeline لحالة آخر طلب |
| `GET` | `/api/student/opportunities` | فرص متاحة للطالب (فلترة: `city`, `company`, `type`, `per_page`) |
| `GET` | `/api/student/opportunities/{id}` | تفاصيل فرصة |
| `POST` | `/api/student/opportunities/{id}/apply` | التقديم على فرصة |
| `GET` | `/api/student/requests` | طلبات الطالب |
| `GET` | `/api/student/requests/{id}` | تفاصيل طلب محدد |
| `GET` | `/api/student/my-internship` | التدريب الحالي/الأخير الموافق عليه |
| `POST` | `/api/student/my-internship/reports` | رفع تقرير أسبوعي |
| `GET` | `/api/student/my-internship/evaluation` | التقييم النهائي للطالب |
| `GET` | `/api/student/complaints` | شكاوى الطالب |
| `POST` | `/api/student/complaints` | إنشاء شكوى طالب |

## 6) مسارات CRUD العامة (موجودة أيضًا بالنظام)

هذه مسارات API عامة ومفيدة غالبًا للباك-أوفيس أو الدمج الداخلي:

| Method | Path | الاستخدام |
|---|---|---|
| `GET` | `/api/students` | قائمة الطلاب |
| `POST` | `/api/students` | إنشاء سجل طالب (يتطلب `user_id` موجود) |
| `GET` | `/api/students/{student}` | تفاصيل طالب |
| `PUT/PATCH` | `/api/students/{student}` | تحديث طالب |
| `DELETE` | `/api/students/{student}` | تعطيل طالب (`is_active=false`) |
| `GET` | `/api/institutions` | قائمة المؤسسات |
| `POST` | `/api/institutions` | تسجيل مؤسسة (ينشئ User + Institution بحالة `pending_approval`) |
| `GET` | `/api/institutions/{institution}` | تفاصيل مؤسسة |
| `PUT/PATCH` | `/api/institutions/{institution}` | تحديث مؤسسة |
| `DELETE` | `/api/institutions/{institution}` | تعطيل مؤسسة |
| `POST` | `/api/opportunities` | إنشاء فرصة (عام) |
| `PUT/PATCH` | `/api/opportunities/{opportunity}` | تحديث فرصة (عام) |
| `DELETE` | `/api/opportunities/{opportunity}` | تعطيل فرصة |
| `GET` | `/api/training-requests` | قائمة طلبات التدريب |
| `POST` | `/api/training-requests` | إنشاء طلب تدريب (عام) |
| `PUT` | `/api/training-requests/{id}/status` | تغيير حالة طلب |
| `GET` | `/api/internships` | قائمة التدريبات |
| `GET` | `/api/reports` | قائمة التقارير |
| `POST` | `/api/reports` | إنشاء تقرير |
| `GET` | `/api/evaluations` | قائمة التقييمات |
| `POST` | `/api/evaluations` | إنشاء تقييم |

## 7) دورة العمل الكاملة (Workflow)

## 7.1 مسار الطالب

1. يسجل حساب: `POST /api/register`
2. يسجل دخول: `POST /api/login`
3. يكمل ملفه ويرفع CV:
- `PUT /api/student/profile`
- `POST /api/student/profile/cv`
4. يستعرض الفرص:
- `GET /api/student/opportunities`
- `GET /api/student/opportunities/{id}`
5. يقدم على فرصة:
- `POST /api/student/opportunities/{id}/apply`
- الحالة الابتدائية للطلب: `pending_admin`
6. يتابع الحالة:
- `GET /api/student/requests`
- `GET /api/student/timeline`
7. بعد القبول النهائي يبدأ التدريب:
- `GET /api/student/my-internship`
- `POST /api/student/my-internship/reports`
- `GET /api/student/my-internship/evaluation`

## 7.2 مسار الأدمن

1. مراجعة الطلبات الأكاديمية:
- `GET /api/admin/requests`
- `GET /api/admin/requests/{id}`
2. القرار:
- قبول أكاديمي: `PATCH /api/admin/requests/{id}/approve` -> `pending_institution`
- رفض أكاديمي: `PATCH /api/admin/requests/{id}/reject` -> `rejected`
3. إدارة الكيانات:
- الطلاب: `/api/admin/students...`
- المؤسسات: `/api/admin/institutions...`
4. متابعة التدريبات:
- `GET /api/admin/internships`
- `GET /api/admin/internships/{id}`

## 7.3 مسار المؤسسة

1. إدارة الحساب والملف:
- `GET/PUT /api/institution/profile`
- `POST /api/institution/profile/logo`
2. إدارة الفرص:
- `GET/POST /api/institution/opportunities`
- `PUT/PATCH /api/institution/opportunities/{id}...`
3. مراجعة طلبات الترشيح الواردة من الجامعة:
- `GET /api/institution/requests`
- `GET /api/institution/requests/{id}`
4. القرار النهائي:
- قبول: `PATCH /api/institution/requests/{id}/accept` -> `approved` + إنشاء Internship
- رفض: `PATCH /api/institution/requests/{id}/reject` -> `rejected`
5. أثناء التدريب:
- `GET /api/institution/internships`
- `GET /api/institution/internships/{id}/reports`
- `POST /api/institution/internships/{id}/evaluate`

## 8) حالات النظام المهمة للفرونت

### حالات طلب التدريب `training_requests.status`

- `pending_admin`: بانتظار موافقة الجامعة
- `pending_institution`: بانتظار رد المؤسسة
- `approved`: مقبول نهائيًا
- `rejected`: مرفوض
- `completed`: منتهي
- تظهر أيضًا في الـ validation العامة: `pending`, `under_review`

### حالات حساب المؤسسة `users.status`

- `pending_approval`
- `active`
- `suspended`

### حالات حساب الطالب (من إدارة الأدمن)

- `active`
- `suspended`

### حالات الفرصة `training_opportunities.status`

- `active`
- `closed`

## 9) أهم حقول الإدخال السريعة

### Auth

- `POST /api/register`: `full_name, email, password, password_confirmation, user_type=student`
- `POST /api/login`: `email, password`
- `POST /api/change-password`: `current_password, new_password, new_password_confirmation`

### Student Apply

- `POST /api/student/opportunities/{id}/apply`
- `student_answers_block` (required)
- `student_notes` (optional)

### Admin Request Decision

- `PATCH /api/admin/requests/{id}/approve`
- `admin_notes` (optional)
- `PATCH /api/admin/requests/{id}/reject`
- `admin_notes` (required)

### Institution Decision

- `PATCH /api/institution/requests/{id}/accept`
- body اختياري
- `PATCH /api/institution/requests/{id}/reject`
- `institution_notes` (optional)

### رفع ملفات

- `POST /api/student/profile/cv`: حقل `cv` (PDF, max 5MB)
- `POST /api/institution/profile/logo`: حقل `logo` (image, max 2MB)
- `POST /api/documents/upload`: حقل `file` (pdf/jpg/jpeg/png, max 10MB) + `title?` + `request_id?`

