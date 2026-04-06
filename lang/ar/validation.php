<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'email' => 'صيغة :attribute غير صحيحة.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'min' => [
        'string' => 'يجب ألا يقل :attribute عن :min أحرف.',
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
    ],
    'max' => [
        'string' => 'يجب ألا يزيد :attribute عن :max أحرف.',
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
    ],
    'unique' => ':attribute مستخدم مسبقًا.',
    'in' => 'القيمة المختارة في :attribute غير صحيحة.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'boolean' => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
    'date' => ':attribute ليس تاريخًا صحيحًا.',
    'exists' => 'القيمة المختارة في :attribute غير موجودة.',
    'mimes' => 'يجب أن يكون :attribute من النوع: :values.',
    'file' => 'يجب أن يكون :attribute ملفًا.',
    'image' => 'يجب أن يكون :attribute صورة.',

    'attributes' => [
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'full_name' => 'الاسم الكامل',
        'student_number' => 'الرقم الجامعي',
        'department' => 'القسم',
        'level' => 'المستوى',
        'status' => 'الحالة',
        'institution_id' => 'معرّف الجهة التدريبية',
        'type' => 'النوع',
    ],
];
