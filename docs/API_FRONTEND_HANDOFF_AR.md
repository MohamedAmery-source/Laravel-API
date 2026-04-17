# دليل الفرونت إند - سير العمليات التفصيلي

هذا الملف مخصص لتسليم مطوري الفرونت إند كل ما يلزم لتنفيذ النظام خطوة بخطوة (طالب، مؤسسة، أدمن) مع الحقول والشروط الفعلية من الباك إند.

## 1) قواعد عامة

| البند | المطلوب |
|---|---|
| Base URL | `/api` |
| Headers | `Accept: application/json` |
| المصادقة | كل المسارات المحمية تحتاج `Authorization: Bearer <token>` |
| أخطاء التحقق | `422` مع رسالة في `message` وتفاصيل الأخطاء |
| صلاحيات الدور | أي دور غير مصرح له يرجع `403` |

## 2) سياسة الطلاب في النظام

1. إدارة النظام (الأدمن) هي الجهة الأساسية لإضافة الطلاب.
2. الطالب الذي يضاف من الأدمن يمكنه تسجيل الدخول للنظام حسب حالته (`active`).
3. لا يوجد شرط إلزامي في مسار التقديم يطابق الجامعة مع متغير بيئة.
4. التقييد الأساسي يكون عبر صلاحيات الدور وحالة الحساب وليس عبر تحقق جامعة إجباري داخل `apply`.

## 3) تدفق الطالب الكامل

### المرحلة A: التسجيل والدخول

1. `POST /api/register`
2. Body:
```json
{
  "full_name": "اسم الطالب",
  "email": "student@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "user_type": "student",
  "university": "اسم الجامعة المطابق لإعداد النظام",
  "student_number": "STU-1001",
  "department": "Computer Science",
  "level": "4"
}
```
3. حقول مطلوبة فعليًا: `full_name`, `email`, `password`, `password_confirmation`, `user_type`, `university`.

1. `POST /api/login`
2. Body:
```json
{
  "email": "student@example.com",
  "password": "password123"
}
```
3. خزّن `token`.

### المرحلة B: التصفح بدون إجبار على إكمال الملف

الطالب بعد الدخول يقدر يتصفح:

1. `GET /api/student/dashboard-stats`
2. `GET /api/student/opportunities`
3. `GET /api/student/opportunities/{id}`
4. `GET /api/student/requests`
5. `GET /api/student/timeline`
6. `GET /api/student/complaints`

لا تعمل redirect إجباري لصفحة البروفايل.

### المرحلة C: إدارة ملف الطالب

1. `GET /api/student/profile`
2. `PUT /api/student/profile`
3. Body (الكل اختياري):
```json
{
  "department": "Computer Science",
  "level": "4",
  "gpa": 3.4,
  "city": "Sanaa",
  "university": "اسم الجامعة نفسها",
  "skills": ["Laravel", "SQL"]
}
```

1. `POST /api/student/profile/cv`
2. Form-Data:
   - `cv` (pdf فقط, max 5MB)

### المرحلة D: التقديم على فرصة

1. `POST /api/student/opportunities/{id}/apply`
2. Body:
```json
{
  "student_answers_block": "إجابات الطالب على أسئلة الفرصة",
  "student_notes": "ملاحظات اختيارية"
}
```
3. شروط الباك قبل إنشاء الطلب:
   - الملف مكتمل 100%.
   - الجامعة مطابقة لجامعة النظام.
   - لا يوجد تدريب نشط للطالب.
   - لا يوجد طلب معلق سابق.
   - لا يوجد طلب سابق لنفس الفرصة بحالة نشطة.
4. ردود مهمة:
   - `201` تم إنشاء الطلب.
   - `422` نقص ملف/شرط عمل/جامعة.
   - `404` الفرصة غير متاحة.

### المرحلة E: أثناء التدريب

1. `GET /api/student/my-internship`
2. `POST /api/student/my-internship/reports`
3. Body:
```json
{
  "title": "تقرير أسبوعي 1",
  "description": "ما تم إنجازه...",
  "report_date": "2026-04-17"
}
```
4. `GET /api/student/my-internship/evaluation`

## 4) تدفق المؤسسة الكامل

### المرحلة A: الدخول والتحقق من التفعيل

1. `POST /api/login` بحساب مؤسسة.
2. إذا المؤسسة غير مفعلة (`status != active`) كثير من العمليات ترجع `403`.

### المرحلة B: إدارة ملف المؤسسة

1. `GET /api/institution/profile`
2. `PUT /api/institution/profile`
3. Body (اختياري):
```json
{
  "name": "اسم المؤسسة",
  "description": "وصف",
  "website": "https://example.com",
  "contact_person": "الاسم",
  "contact_phone": "+967700000000",
  "address": "Sanaa, Yemen",
  "social_links": ["https://linkedin.com/company/example"]
}
```
4. `POST /api/institution/profile/logo`
5. Form-Data:
   - `logo` (jpg/jpeg/png/webp, max 2MB)

### المرحلة C: إضافة فرصة تدريبية (تفصيلي)

1. `POST /api/institution/opportunities`
2. Body:
```json
{
  "title": "Backend Intern",
  "department": "Computer Science",
  "description": "تفاصيل الفرصة",
  "required_skills": "PHP, Laravel, MySQL",
  "start_date": "2026-06-01",
  "end_date": "2026-08-31",
  "application_deadline": "2026-05-20",
  "available_seats": 5,
  "city": "Sanaa",
  "training_type": "summer",
  "custom_questions": ["لماذا تريد هذه الفرصة؟"],
  "status": "active"
}
```
3. حقول مطلوبة: `title`, `start_date`, `end_date`, `available_seats`.
4. قيود:
   - `end_date >= start_date`
   - `application_deadline <= start_date` إذا أرسلت
   - `training_type` قيمته: `summer` أو `cooperative`
   - `status` قيمته: `active` أو `closed`
5. الباك يضيف تلقائيًا:
   - `institution_id` من المؤسسة المسجلة.
   - `is_active` حسب `status`.

### المرحلة D: تعديل وإغلاق الفرصة

1. `PUT /api/institution/opportunities/{id}` (نفس حقول الإضافة لكن كلها اختيارية)
2. `PATCH /api/institution/opportunities/{id}/status`
3. Body:
```json
{
  "status": "closed"
}
```

### المرحلة E: مراجعة الطلبات

1. `GET /api/institution/requests`
2. `GET /api/institution/requests/{id}`
3. قبول:
   - `PATCH /api/institution/requests/{id}/accept`
   - شرط: الحالة الحالية `pending_institution`.
   - النتيجة: `approved` وإنشاء/تفعيل internship.
4. رفض:
   - `PATCH /api/institution/requests/{id}/reject`
   - Body:
```json
{
  "institution_notes": "سبب الرفض"
}
```

### المرحلة F: إدارة التدريب والتقييم

1. `GET /api/institution/internships`
2. `GET /api/institution/internships/{id}/reports`
3. `POST /api/institution/internships/{id}/evaluate`
4. Body:
```json
{
  "score": 85,
  "notes": "أداء ممتاز"
}
```
5. قيود: `score` من 1 إلى 100.

## 5) تدفق الأدمن الكامل

### المرحلة A: المتابعة العامة

1. `POST /api/login` بحساب أدمن.
2. `GET /api/admin/dashboard-stats`

### المرحلة B: إدارة الطلاب

1. `GET /api/admin/students`
2. `POST /api/admin/students`
3. Body:
```json
{
  "full_name": "اسم الطالب",
  "email": "student2@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+967700000001",
  "student_number": "STU-2026-1002",
  "department": "IT",
  "level": "3",
  "gpa": 3.1,
  "status": "active"
}
```
4. `PUT /api/admin/students/{id}` (حقول تحديث مرنة).
5. `PATCH /api/admin/students/{id}/status`
6. Body:
```json
{
  "status": "suspended"
}
```

### المرحلة C: إدارة المؤسسات

1. `GET /api/admin/institutions`
2. `POST /api/admin/institutions`
3. Body:
```json
{
  "full_name": "مسؤول المؤسسة",
  "email": "inst@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+967700000002",
  "name": "شركة مثال",
  "address": "Aden, Yemen",
  "description": "وصف المؤسسة",
  "website": "https://example.org",
  "contact_person": "مسؤول الموارد البشرية",
  "contact_phone": "+967700000003",
  "status": "pending_approval"
}
```
4. `PUT /api/admin/institutions/{id}`
5. `PATCH /api/admin/institutions/{id}/approve`
6. `PATCH /api/admin/institutions/{id}/status`

### المرحلة D: دورة اعتماد طلبات التدريب

1. `GET /api/admin/requests`
2. `GET /api/admin/requests/{id}`
3. موافقة أكاديمية:
   - `PATCH /api/admin/requests/{id}/approve`
   - Body:
```json
{
  "admin_notes": "موافق"
}
```
   - النتيجة: الطلب يتحول إلى `pending_institution`.
4. رفض أكاديمي:
   - `PATCH /api/admin/requests/{id}/reject`
   - Body:
```json
{
  "admin_notes": "سبب الرفض (مطلوب)"
}
```

### المرحلة E: متابعة التدريب

1. `GET /api/admin/internships`
2. `GET /api/admin/internships/{id}`

## 6) الحالات المهمة للفرونت

### training request status
- `pending_admin`
- `pending_institution`
- `approved`
- `rejected`
- `completed`

### institution user status
- `pending_approval`
- `active`
- `suspended`

### opportunity status
- `active`
- `closed`

## 7) قواعد UI/UX مطلوبة

1. لا تعمل redirect إجباري لإكمال ملف الطالب بعد الدخول.
2. اسمح بالتصفح الكامل.
3. امنع التنفيذ فقط عند العمليات المقيدة (خصوصا التقديم).
4. عند فشل التقديم بـ `422` بسبب نقص بيانات، اعرض الحقول الناقصة للمستخدم مباشرة.
5. عند فشل الجامعة، اعرض رسالة صريحة: الحساب ليس ضمن جامعة النظام.
