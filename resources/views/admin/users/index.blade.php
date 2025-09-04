@extends('admin.layouts.app')

@section('page-title', 'إدارة المستخدمين')
@section('page-subtitle', 'عرض وإدارة حسابات جميع المستخدمين')

@section('content')
    <!-- Custom Styles -->
    <style>
        .card-elegant {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .dark .card-elegant {
            background: #1f2937;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .card-elegant:hover {
            transform: translateY(-2px);
        }
        .table-header {
            background: linear-gradient(to right, #f1f5f9, #e2e8f0);
        }
        .dark .table-header {
            background: linear-gradient(to right, #374151, #1f2937);
        }
        .action-btn {
            transition: all 0.2s ease-in-out;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .search-container {
            position: relative;
            max-width: 600px;
            margin-bottom: 1.5rem;
        }
        .search-input {
            border-radius: 9999px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.2s ease-in-out;
        }
        .dark .search-input {
            border-color: #4b5563;
            background: #1f2937;
        }
        .search-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
            outline: none;
        }
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }
        .search-btn {
            border-radius: 9999px;
            padding: 0.75rem 2rem;
            background: linear-gradient(to right, #f59e0b, #f97316);
            color: #fff;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .search-btn:hover {
            background: linear-gradient(to right, #d97706, #ea580c);
            transform: translateY(-1px);
        }
        .clear-btn {
            border-radius: 9999px;
            padding: 0.75rem 2rem;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .dark .clear-btn {
            border-color: #4b5563;
            color: #9ca3af;
        }
        .clear-btn:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }
        .dark .clear-btn:hover {
            background: #374151;
        }
        .add-user-btn {
            border-radius: 9999px;
            padding: 0.75rem 2rem;
            background: linear-gradient(to right, #10b981, #059669);
            color: #fff;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .add-user-btn:hover {
            background: linear-gradient(to right, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 2px;
            transition: all 0.2s ease-in-out;
        }
        .pagination .page-link:hover {
            background-color: #f59e0b;
            color: #fff;
        }
        .dark .pagination .page-link {
            background-color: #374151;
            border-color: #4b5563;
        }
        .dark .pagination .page-link:hover {
            background-color: #f59e0b;
            color: #fff;
        }
    </style>

<!-- Elegant Toolbar -->
<div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 
            bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 
            dark:from-slate-800 dark:via-slate-700 dark:to-slate-600
            rounded-xl shadow-lg mb-6">

    <!-- Left: Title -->
    <div class="flex items-center gap-3 text-white">
        <div class="p-2 bg-white/20 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold">إدارة المستخدمين</h2>
            <p class="text-sm opacity-80">إجمالي {{ $users->total() }} مستخدم</p>
        </div>
    </div>

    <!-- Right: Search + Actions -->
    <div class="flex items-center gap-3 w-full md:w-auto">

        <!-- Search -->
        <form method="GET" class="flex items-center bg-white dark:bg-slate-700 rounded-lg overflow-hidden shadow-sm w-full md:w-64">
            <input type="text" name="q" value="{{ $search }}" 
                   placeholder="ابحث بالاسم أو البريد..." 
                   class="flex-1 px-3 py-2 text-sm text-slate-700 dark:text-slate-100 bg-transparent outline-none" />
            <button type="submit" class="px-3 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" 
                   class="px-3 text-red-500 hover:text-red-700">
                    ×
                </a>
            @endif
        </form>

        <!-- Add Button -->
        <a href="{{ route('admin.users.create') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-white text-blue-600 hover:bg-slate-100 
                  dark:bg-blue-500 dark:hover:bg-blue-400 dark:text-white 
                  rounded-lg shadow-sm font-medium transition @permClass('users.create')" @permDisabled('users.create')>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            مستخدم جديد
        </a>

    </div>
</div>


    <!-- Users Table -->
    <div class="card-elegant rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-header">
                    <tr>
                        <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">#</th>
                        <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">المستخدم</th>
                        <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">البريد الإلكتروني</th>
                        <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">تاريخ التسجيل</th>
                        <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                        <td class="p-4">
                            <span class="text-sm font-mono text-slate-500 dark:text-slate-400">#{{ $user->id }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-slate-800 dark:text-slate-200 text-base hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ $user->name }}
                                    </a>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">مستخدم عادي</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm font-mono text-slate-600 dark:text-slate-300 dir-ltr">{{ $user->email }}</span>
                        </td>
                        <td class="p-4">
                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $user->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $user->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="p-2.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors action-btn @permClass('users.view')" 
                                   title="عرض التفاصيل" @permDisabled('users.view')>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="p-2.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors action-btn @permClass('users.edit')" 
                                   title="تعديل" @permDisabled('users.edit')>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline @permClass('users.delete')" 
                                      onsubmit="return confirm('هل تريد حذف هذا المستخدم نهائياً؟\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" @permDisabled('users.delete')
                                            class="p-2.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors action-btn" 
                                            title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <div>
                                    <div class="text-slate-600 dark:text-slate-400 font-medium text-lg mb-2">لم يتم العثور على مستخدمين</div>
                                    <div class="text-sm text-slate-500 dark:text-slate-500">
                                        @if($search)
                                            جرب تغيير مصطلح البحث أو <a href="{{ route('admin.users.index') }}" class="text-amber-600 hover:underline">عرض جميع المستخدمين</a>
                                        @else
                                            قم بإضافة أول مستخدم للنظام
                                        @endif
                                    </div>
                                </div>
                                @if(!$search)
                                    <a href="{{ route('admin.users.create') }}" 
                                       class="add-user-btn mt-4 flex items-center gap-2">
                                        إضافة مستخدم جديد
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
    {{ $users->links('pagination::tailwind') }}
    </div>
@endsection