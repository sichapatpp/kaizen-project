@extends('layouts.base')

@section('content')
<div class="role-management-wrapper" style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 700; color: #1e293b; margin: 0;">จัดการบทบาท </h2>
            <p style="color: #64748b; margin: 0;">กำหนดและจัดการบทบาทหน้าที่ของผู้ใช้งานในระบบ</p>
        </div>
        <button class="btn btn-primary" onclick="openAddRoleModal()" style="border-radius: 8px; padding: 10px 20px;">
            <i class="fas fa-plus mr-2"></i> + เพิ่มบทบาทใหม่
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="px-4 py-3 border-0" width="25%">ชื่อบทบาท</th>
                        <th class="px-4 py-3 border-0">รายละเอียด</th>
                        <th class="px-4 py-3 border-0 text-center" width="200px">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $item)
                    @php
                        $isSystemRole = in_array(strtolower($item->role_name), ['admin', 'manager', 'chairman', 'user']);
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="role-icon mr-3" style="width: 36px; height: 36px; background: {{ $isSystemRole ? '#e2e8f0' : '#dcfce7' }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: {{ $isSystemRole ? '#475569' : '#15803d' }};">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="fw-bold" style="color: #334155;">{{ $item->role_name }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div style="font-size: 14px; color: #64748b;">{{ $item->description }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-warning" onclick="openEditRoleModal({{ json_encode($item) }})" style="border-radius: 6px; padding: 5px 12px;">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>
                                
                                <form action="{{ route('roles.destroy', $item->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $item->role_name }}', {{ $isSystemRole ? 'true' : 'false' }})">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Role Modal --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title font-weight-bold" id="roleModalTitle" style="color: #1e293b;">เพิ่มบทบาทใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm" method="POST">
                @csrf
                <div id="roleMethodField"></div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label font-weight-600"> ชื่อบทบาท </label>
                        <input type="text" name="role_name" id="roleNameInput" class="form-control" required style="border-radius: 8px;" placeholder="เช่น marketing, auditor">
                        <small class="text-muted">ชื่อภาษาอังกฤษสำหรับใช้ในระบบ</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-600"> รายละเอียด </label>
                        <textarea name="description" id="roleDescriptionInput" class="form-control" rows="3" style="border-radius: 8px;" placeholder="คำอธิบายสิทธิ์หรือบทบาท..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 20px;">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 8px 24px;">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddRoleModal() {
        $('#roleModalTitle').text('เพิ่มบทบาทใหม่');
        $('#roleForm').attr('action', "{{ route('roles.store') }}");
        $('#roleMethodField').empty();
        $('#roleForm')[0].reset();
        $('#roleModal').modal('show');
    }

    function openEditRoleModal(role) {
        $('#roleModalTitle').text('แก้ไขข้อมูลบทบาท');
        $('#roleForm').attr('action', `/roles/${role.id}`);
        $('#roleMethodField').html('<input type="hidden" name="_method" value="PUT">');
        
        $('#roleNameInput').val(role.role_name);
        $('#roleDescriptionInput').val(role.description);
        
        $('#roleModal').modal('show');
    }

</script>

<style>
    .font-weight-600 { font-weight: 600; }
    .role-icon { font-size: 16px; }
    .btn-outline-warning:hover { color: #fff; }
    .table th { font-size: 13px; text-transform: uppercase; letter-spacing: 0.025em; color: #64748b; font-weight: 700; }
</style>
@endsection
