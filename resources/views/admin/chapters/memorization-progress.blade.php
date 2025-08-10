@extends('admin.layouts.app')

@section('title', 'تقدم الحفظ - ' . $chapter->name_ar)

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
                        <li class="breadcrumb-item active">تقدم الحفظ</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fas fa-chart-line me-2"></i>
                    تقدم الحفظ - سورة {{ $chapter->name_ar }}
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
                                <i class="fas fa-users me-2"></i>
                                تقدم الحفظ للمستخدمين
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-primary fs-6">
                                إجمالي المستخدمين: {{ $progress->total() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($progress->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">#</th>
                                        <th width="120">المستخدم</th>
                                        <th width="120">البريد الإلكتروني</th>
                                        <th width="100">نسبة التقدم</th>
                                        <th width="120">آخر تحديث</th>
                                        <th width="120">الحالة</th>
                                        <th width="100">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($progress as $item)
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
                                                    <h6 class="mb-0">{{ $item->user->name ?? 'غير محدد' }}</h6>
                                                    <small class="text-muted">ID: {{ $item->user_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $item->user->email ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $percentage = $item->progress_percentage ?? 0;
                                                $colorClass = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $colorClass }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%"
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $item->updated_at ? $item->updated_at->diffForHumans() : 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $item->status ?? 'pending';
                                                $statusColors = [
                                                    'completed' => 'success',
                                                    'in_progress' => 'warning',
                                                    'pending' => 'secondary',
                                                    'reviewing' => 'info'
                                                ];
                                                $statusLabels = [
                                                    'completed' => 'مكتمل',
                                                    'in_progress' => 'قيد التقدم',
                                                    'pending' => 'في الانتظار',
                                                    'reviewing' => 'قيد المراجعة'
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
                                                        data-bs-target="#progressModal{{ $item->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal for progress details -->
                                    <div class="modal fade" id="progressModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        تفاصيل التقدم - {{ $item->user->name ?? 'مستخدم' }}
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
                                                                    <td>{{ $item->user->name ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>البريد الإلكتروني:</strong></td>
                                                                    <td>{{ $item->user->email ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>تاريخ التسجيل:</strong></td>
                                                                    <td>{{ $item->user->created_at ? $item->user->created_at->format('Y-m-d') : 'غير محدد' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>تفاصيل التقدم</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td><strong>نسبة التقدم:</strong></td>
                                                                    <td>{{ $item->progress_percentage ?? 0 }}%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>الحالة:</strong></td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                                            {{ $statusLabels[$status] ?? $status }}
                                                                        </span>
                                                                    </td>
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
                            {{ $progress->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-chart-line fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">لا يوجد تقدم حفظ</h5>
                            <p class="text-muted">لم يتم العثور على مستخدمين لديهم تقدم حفظ لهذه السورة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

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
</style>
@endsection
