@extends('admin.layouts.app')

@section('title', 'خطط الحفظ - ' . $chapter->name_ar)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.chapters.index') }}">السور</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.chapters.show', $chapter) }}">{{ $chapter->name_ar }}</a></li>
                        <li class="breadcrumb-item active">خطط الحفظ</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    خطط الحفظ - سورة {{ $chapter->name_ar }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="fas fa-list-check me-2"></i>
                                خطط الحفظ المستخدمة
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-primary fs-6">
                                إجمالي الخطط: {{ $planItems->total() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($planItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">#</th>
                                        <th width="120">المستخدم</th>
                                        <th width="120">اسم الخطة</th>
                                        <th width="100">نوع العنصر</th>
                                        <th width="120">تاريخ البداية</th>
                                        <th width="120">تاريخ الانتهاء</th>
                                        <th width="100">الحالة</th>
                                        <th width="100">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($planItems as $item)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $item->memorizationPlan->user->name ?? 'غير محدد' }}</h6>
                                                    <small class="text-muted">ID: {{ $item->memorizationPlan->user_id ?? 'غير محدد' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $item->memorizationPlan->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $type = $item->type ?? 'verse';
                                                $typeLabels = [
                                                    'verse' => 'آية',
                                                    'chapter' => 'سورة',
                                                    'juz' => 'جزء'
                                                ];
                                                $typeColors = [
                                                    'verse' => 'info',
                                                    'chapter' => 'primary',
                                                    'juz' => 'warning'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$type] ?? 'secondary' }}">
                                                {{ $typeLabels[$type] ?? $type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $item->status ?? 'pending';
                                                $statusColors = [
                                                    'completed' => 'success',
                                                    'in_progress' => 'warning',
                                                    'pending' => 'secondary',
                                                    'overdue' => 'danger',
                                                    'cancelled' => 'dark'
                                                ];
                                                $statusLabels = [
                                                    'completed' => 'مكتمل',
                                                    'in_progress' => 'قيد التقدم',
                                                    'pending' => 'في الانتظار',
                                                    'overdue' => 'متأخر',
                                                    'cancelled' => 'ملغي'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                {{ $statusLabels[$status] ?? $status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#planModal{{ $item->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal for plan details -->
                                    <div class="modal fade" id="planModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        تفاصيل الخطة - {{ $item->memorizationPlan->name ?? 'خطة' }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>معلومات المستخدم</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td><strong>الاسم:</strong></td>
                                                                    <td>{{ $item->memorizationPlan->user->name ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>البريد الإلكتروني:</strong></td>
                                                                    <td>{{ $item->memorizationPlan->user->email ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>تاريخ التسجيل:</strong></td>
                                                                    <td>{{ $item->memorizationPlan->user->created_at ? $item->memorizationPlan->user->created_at->format('Y-m-d') : 'غير محدد' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>تفاصيل الخطة</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td><strong>اسم الخطة:</strong></td>
                                                                    <td>{{ $item->memorizationPlan->name ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>نوع العنصر:</strong></td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $typeColors[$type] ?? 'secondary' }}">
                                                                            {{ $typeLabels[$type] ?? $type }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>الحالة:</strong></td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                                            {{ $statusLabels[$status] ?? $status }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <h6>التواريخ</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td><strong>تاريخ البداية:</strong></td>
                                                                    <td>{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>تاريخ الانتهاء:</strong></td>
                                                                    <td>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>تاريخ الإنشاء:</strong></td>
                                                                    <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>آخر تحديث:</strong></td>
                                                                    <td>{{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $planItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-check fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">لا توجد خطط حفظ</h5>
                            <p class="text-muted">لم يتم العثور على خطط حفظ تستخدم هذه السورة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.875rem;
}

.fw-semibold {
    font-weight: 600;
}
</style>
@endsection
