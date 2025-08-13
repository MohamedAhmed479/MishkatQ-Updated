@extends('admin.layouts.app')

@section('page-title', 'إضافة سجل تدقيق')
@section('page-subtitle', 'إنشاء سجل جديد يدويًا للاختبار أو التوثيق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 p-6">
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent">إضافة سجل جديد</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mt-1">إنشاء سجل تدقيق يدوي للاختبار والتوثيق</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-center">
                    <div class="text-xs text-slate-400 mb-1">الحالة</div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                        ✏️ نموذج جديد
                    </span>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl shadow-lg transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Form Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">📝 نموذج إضافة سجل التدقيق</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.audit-logs.store') }}" class="p-8">
            @csrf

            <!-- Essential Information Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">📋 المعلومات الأساسية</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">⚡ العملية المنفذة <span class="text-red-400">*</span></label>
                        <input name="action" 
                               required 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="created, updated, deleted, login, logout...">
                        <p class="text-xs text-slate-400">مثل: user.created, post.updated, login.success</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">✅ حالة العملية</label>
                        <input name="status" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="success, failed, pending...">
                        <p class="text-xs text-slate-400">حالة تنفيذ العملية</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">⚠️ مستوى الخطورة</label>
                        <select name="severity" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                            <option value="">اختر مستوى الخطورة</option>
                            <option value="low">🟢 منخفض (Low)</option>
                            <option value="medium">🟡 متوسط (Medium)</option>
                            <option value="high">🟠 عالي (High)</option>
                            <option value="critical">🔴 خطير (Critical)</option>
                        </select>
                        <p class="text-xs text-slate-400">تحديد أهمية هذه العملية</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📂 فئة العملية</label>
                        <input name="category" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="auth, users, posts, settings...">
                        <p class="text-xs text-slate-400">تصنيف العملية حسب النوع</p>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="mb-10">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">📝 وصف العملية</label>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none"
                              placeholder="وصف تفصيلي للعملية التي تم تنفيذها..."></textarea>
                    <p class="text-xs text-slate-400">شرح مفصل عن طبيعة العملية والغرض منها</p>
                </div>
            </div>

            <!-- User Information Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">👤 معلومات المستخدم</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">👤 اختيار المستخدم</label>
                        <select name="user_id" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                            <option value="">اختر مستخدم موجود</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400">اختر من المستخدمين المسجلين</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🏷️ نوع المستخدم</label>
                        <input name="user_type" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="admin, user, moderator...">
                        <p class="text-xs text-slate-400">صنف المستخدم الذي نفذ العملية</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">✏️ اسم المستخدم</label>
                        <input name="user_name" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="اسم المستخدم">
                        <p class="text-xs text-slate-400">يمكن كتابة الاسم يدوياً إذا لم يكن موجود في القائمة</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📧 البريد الإلكتروني</label>
                        <input name="user_email" 
                               type="email" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="user@example.com">
                        <p class="text-xs text-slate-400">عنوان البريد الإلكتروني للمستخدم</p>
                    </div>
                </div>
            </div>

            <!-- Target Entity Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">🎯 الكيان المستهدف</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🏗️ نوع النموذج</label>
                        <input name="model_type" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="App\Models\User">
                        <p class="text-xs text-slate-400">المسار الكامل لكلاس النموذج</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🔢 معرف الكيان</label>
                        <input name="model_id" 
                               type="number" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="123">
                        <p class="text-xs text-slate-400">ID الخاص بالسجل المستهدف</p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-medium text-white">🏷️ اسم الكيان</label>
                        <input name="model_name" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="اسم أو عنوان السجل المستهدف">
                        <p class="text-xs text-slate-400">وصف مفهوم للكيان المستهدف</p>
                    </div>
                </div>
            </div>

            <!-- Data Changes Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">🔄 التغييرات في البيانات</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🔴 القيم القديمة (JSON)</label>
                        <textarea name="old_values" 
                                  rows="4" 
                                  class="font-mono text-sm w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none" 
                                  placeholder='{"name": "القيمة القديمة", "status": "old"}'></textarea>
                        <p class="text-xs text-slate-400">القيم قبل التعديل بصيغة JSON</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🟢 القيم الجديدة (JSON)</label>
                        <textarea name="new_values" 
                                  rows="4" 
                                  class="font-mono text-sm w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none" 
                                  placeholder='{"name": "القيمة الجديدة", "status": "new"}'></textarea>
                        <p class="text-xs text-slate-400">القيم بعد التعديل بصيغة JSON</p>
                    </div>
                </div>
            </div>

            <!-- Request Information Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">🌐 معلومات الطلب</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📡 عنوان IP</label>
                        <input name="ip_address" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="192.168.1.1">
                        <p class="text-xs text-slate-400">عنوان IP الخاص بالمستخدم</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🔧 HTTP Method</label>
                        <select name="method" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                            <option value="">اختر طريقة الطلب</option>
                            <option value="GET">🔍 GET</option>
                            <option value="POST">📝 POST</option>
                            <option value="PUT">🔄 PUT</option>
                            <option value="PATCH">✏️ PATCH</option>
                            <option value="DELETE">🗑️ DELETE</option>
                        </select>
                        <p class="text-xs text-slate-400">نوع طلب HTTP</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🔗 معرف الجلسة</label>
                        <input name="session_id" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="sess_abc123xyz">
                        <p class="text-xs text-slate-400">معرف جلسة المستخدم</p>
                    </div>

                    <div class="md:col-span-2 lg:col-span-3 space-y-2">
                        <label class="block text-sm font-medium text-white">🌐 عنوان URL</label>
                        <input name="url" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="/admin/users/123/edit">
                        <p class="text-xs text-slate-400">المسار الكامل للصفحة المطلوبة</p>
                    </div>

                    <div class="md:col-span-2 lg:col-span-3 space-y-2">
                        <label class="block text-sm font-medium text-white">🖥️ User Agent</label>
                        <input name="user_agent" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300"
                               placeholder="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36">
                        <p class="text-xs text-slate-400">معلومات المتصفح والنظام</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">⚙️ معلومات إضافية</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🔒 عملية حساسة؟</label>
                        <select name="is_sensitive" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                            <option value="">اختر...</option>
                            <option value="1">🔴 نعم - عملية حساسة</option>
                            <option value="0">🟢 لا - عملية عادية</option>
                        </select>
                        <p class="text-xs text-slate-400">هل تحتوي على بيانات حساسة؟</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📅 تاريخ التنفيذ</label>
                        <input type="datetime-local" 
                               name="performed_at" 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                        <p class="text-xs text-slate-400">وقت تنفيذ العملية (اختياري)</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📱 معلومات الجهاز</label>
                        <textarea name="device_info" 
                                  rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none"
                                  placeholder="معلومات عن الجهاز المستخدم..."></textarea>
                        <p class="text-xs text-slate-400">تفاصيل إضافية عن الجهاز</p>
                    </div>
                </div>
            </div>

            <!-- Technical Data Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-slate-100 dark:bg-slate-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">💾 البيانات التقنية</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📄 بيانات الطلب (JSON)</label>
                        <textarea name="request_data" 
                                  rows="4" 
                                  class="font-mono text-sm w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none"
                                  placeholder='{"form_data": {"field1": "value1"}, "files": []}'></textarea>
                        <p class="text-xs text-slate-400">البيانات المرسلة مع الطلب</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">🏷️ Metadata (JSON)</label>
                        <textarea name="metadata" 
                                  rows="4" 
                                  class="font-mono text-sm w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none"
                                  placeholder='{"context": "additional", "tags": ["tag1", "tag2"]}'></textarea>
                        <p class="text-xs text-slate-400">معلومات إضافية ومعاملات السياق</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-slate-200 dark:border-slate-700 pt-8">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-400">
                        <span class="font-medium">مطلوب:</span> الحقول المميزة بـ <span class="text-red-400">*</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.audit-logs.index') }}" 
                           class="inline-flex items-center gap-2 px-8 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            إلغاء الأمر
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            حفظ السجل
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


