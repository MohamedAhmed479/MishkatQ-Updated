## لوحة التحكم الإدارية (مشكاة) – دليل التخصيص السريع

### أين توجد الملفات؟
- الواجهة: `resources/views/admin/dashboard.blade.php`
- التخطيط العام: `resources/views/admin/layouts/app.blade.php`
- المعطيات: `app/Http/Controllers/Admin/DashboardController.php`

### الفلاتر
- تُقرأ من الطلب في `DashboardController@index` ضمن المصفوفة `$filters`.
- الخيارات متاحة في `$filterOptions` (الفروع، الحلقات، المعلّمون، والفترات).
- لتوسيع الفلاتر:
  1. أضف الحقل إلى `$filters` في الكونترولر.
  2. أضف قائمة/حقل الإدخال في أعلى `dashboard.blade.php`.
  3. مرّر خياراته داخل `$filterOptions`.

### مؤشرات الأداء (KPIs)
- تُبنى في الكونترولر داخل `$kpis` على شكل:
```php
['label' => 'اسم المؤشر', 'value' => 'القيمة', 'delta' => 5.4]
```
- أضف أو عدّل عناصر `$kpis` وسيتم عرضها تلقائيًا كبطاقات.

### الرسوم البيانية
- تُمرَّر بيانات الرسوم في `$charts` من الكونترولر:
  - `cohort`: خطّي متعدد يعرض ثبات المجموعات عبر الزمن.
  - `velocity`: أعمدة لسرعة التقدّم.
  - `heatmap`: مصفوفة حرارية (Chart.js Matrix) لأوقات المراجعة.
  - `coverage`: Polar Area لتغطية الحفظ حسب السور/الأجزاء.
  - `teachers`: Radar لأداء المعلّمين.
  - `attendance`: خطّي لاتجاهات الحضور.
- لإضافة رسم جديد:
  1. أضف بياناته إلى `$charts` في الكونترولر.
  2. أضف `<canvas id="myNewChart"></canvas>` في `dashboard.blade.php`.
  3. أنشئ دالة `buildMyNewChart()` في سكربت الصفحة تستدعي Chart.js.

### الجداول
- اللوائح: المتفوّقون، والطلاب المعرّضون للخطر، والجلسات الأخيرة.
- المصدر: مصفوفات `$topStudents`, `$atRiskStudents`, `$recentSessions` من الكونترولر.
- للتعديل: حدّث المصفوفات أو استبدلها بنتائج استعلامات فعلية.

### الحسابات والتحليلات
- خطر الانقطاع، الالتزام بالمراجعة، سرعة التقدّم، تقييم المعلّمين:
  - أنشئ خدمات/مستودعات (Repositories/Services) لحساب المؤشرات.
  - استخدم `Cache::remember()` لتخزين النتائج الشائعة (مثلاً 5–15 دقيقة) لتحسين الأداء.

### الأداء والأمان
- تأكد من احترام `auth:admin` و`permission:dashboard.view` (موجودة في الكونترولر).
- قلّل عدد الاستعلامات باستخدام eager loading والتجميع `GROUP BY` عندما يلزم.
- استخدم التخزين المؤقت عند بناء الرسوم المكلفة حسابيًا.

### السمة (فاتح/داكن)
- يوجد زر تبديل السمة في الهيدر. الحالة تُحفظ في `localStorage` بمفتاح `theme`.

### الاعتمادات
- Chart.js عبر CDN، وإضافة Matrix: `chartjs-chart-matrix`.
- Tailwind عبر Vite (ضمن `resources/css/app.css` و`resources/js/app.js`).
