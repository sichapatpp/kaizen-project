@extends('layouts.base')

@section('content')
<form action="{{ route('user.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">ชื่อ-นามสกุล</label>
        <input type="text" name="name" class="form-control"
               value="{{ $user->name }}">
    </div>

    <div class="mb-3">
        <label class="form-label">อีเมล</label>
        <input type="email" name="email" class="form-control"
               value="{{ $user->email }}">
    </div>

    <div class="mb-3">
        <label class="form-label">หน่วยงาน</label>
        <input type="text" name="department" class="form-control"
               value="{{ $user->department }}">
    </div>

        <div   div class="mb-3">
            <label class="form-label">บทบาท</label>
            <select name="role_id" class="form-select">
                @foreach ($roles as $role)
                    <option {{ old('role_id') == $role->id ? 'selected' : '' }} value="{{ $role->id }}">
                        {{ $role->role_name }}</option>
                @endforeach
            </select>
        </div>

    <div class="mb-3">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select">
            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">บันทึก</button>
</form>
@endsection
