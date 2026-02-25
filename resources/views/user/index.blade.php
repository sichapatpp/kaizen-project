@extends('layouts.base')

@section('content')
<div class="user-management-wrapper" style="padding: 24px; max-width: 1400px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 700; color: #1e293b; margin: 0;">จัดการผู้ใช้</h2>
            <p style="color: #64748b; margin: 0;">จัดการข้อมูลผู้ใช้งานและกำหนดสิทธิ์ในระบบ</p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()" style="border-radius: 8px; padding: 10px 20px;">
            <i class="fas fa-plus mr-2"></i> + เพิ่มผู้ใช้ใหม่
        </button>
    </div>

    {{-- Filter Section --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('user.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">🔍 ค้นหา</label>
                    <input type="text" name="search" class="form-control" placeholder="ชื่อ, อีเมล, หรือแผนก..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">🏷️ สิทธิ์</label>
                    <select name="role_id" class="form-select">
                        <option value="">ทั้งหมด</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">📊 สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (ใช้งาน)</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (ไม่ใช้งาน)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100" style="border-radius: 8px; padding: 10px;">กรองข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    {{-- User Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="px-4 py-3 border-0">ชื่อ-นามสกุล</th>
                        <th class="px-4 py-3 border-0">รายละเอียด</th>
                        <th class="px-4 py-3 border-0">สิทธิ์</th>
                        <th class="px-4 py-3 border-0">สถานะ</th>
                        <th class="px-4 py-3 border-0 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-3" style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;">
                                    {{ substr($item->name, 0, 1) }}
                                </div>
                                <div class="fw-bold" style="color: #334155;">{{ $item->name }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div style="font-size: 13px; color: #64748b;">
                                📧 {{ $item->email }}<br>
                                🏢 {{ $item->department ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge" style="background-color: {{ $item->role_id == 1 ? '#fee2e2' : '#e0f2fe' }}; color: {{ $item->role_id == 1 ? '#b91c1c' : '#0369a1' }}; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                {{ $item->role->description ?? $item->role->role_name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status_{{ $item->id }}" {{ $item->status == 'active' ? 'checked' : '' }} onchange="toggleUserStatus({{ $item->id }})">
                                <label class="form-check-label" for="status_{{ $item->id }}" id="status_label_{{ $item->id }}">
                                    {{ $item->status == 'active' ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn btn-sm btn-outline-warning" onclick="openEditModal({{ json_encode($item) }})" style="border-radius: 6px; padding: 5px 12px;">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div style="color: #94a3b8;">
                                <div style="font-size: 40px;">🚫</div>
                                <div>ไม่พบข้อมูลผู้ใช้งาน</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title font-weight-bold" id="modalTitle" style="color: #1e293b;">เพิ่มผู้ใช้ใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label font-weight-600">👤 ชื่อ-นามสกุล</label>
                        <input type="text" name="name" id="userName" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-600">📧 อีเมล</label>
                        <input type="email" name="email" id="userEmail" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-600">🏢 แผนก/หน่วยงาน</label>
                        <input type="text" name="department" id="userDepartment" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-600">🏷️ สิทธิ์</label>
                            <select name="role_id" id="userRole" class="form-select" required style="border-radius: 8px;">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-600">📊 สถานะ</label>
                            <select name="status" id="userStatus" class="form-select" required style="border-radius: 8px;">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-600">🔑 รหัสผ่าน <small class="text-muted">(ว่างไว้หากไม่ต้องการเปลี่ยน)</small></label>
                        <input type="password" name="password" id="userPassword" class="form-control" style="border-radius: 8px;">
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
    function openAddModal() {
        $('#modalTitle').text('เพิ่มผู้ใช้ใหม่');
        $('#userForm').attr('action', "{{ route('user.store') }}");
        $('#methodField').empty();
        $('#userForm')[0].reset();
        $('#userModal').modal('show');
    }

    function openEditModal(user) {
        $('#modalTitle').text('แก้ไขข้อมูลผู้ใช้');
        $('#userForm').attr('action', `/user/${user.id}`);
        $('#methodField').html('<input type="hidden" name="_method" value="PUT">');
        
        $('#userName').val(user.name);
        $('#userEmail').val(user.email);
        $('#userDepartment').val(user.department);
        $('#userRole').val(user.role_id);
        $('#userStatus').val(user.status);
        $('#userPassword').val('');
        
        $('#userModal').modal('show');
    }

    function toggleUserStatus(userId) {
        const checkbox = document.getElementById(`status_${userId}`);
        const label = document.getElementById(`status_label_${userId}`);
        
        fetch(`/user/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                label.innerText = data.status === 'active' ? 'Active' : 'Inactive';
            } else {
                checkbox.checked = !checkbox.checked;
                alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
            }
        })
        .catch(error => {
            checkbox.checked = !checkbox.checked;
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
    }
</script>

<style>
    .font-weight-600 { font-weight: 600; }
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .btn-outline-warning:hover { color: #fff; }
    .avatar-circle { border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>
@endsection
