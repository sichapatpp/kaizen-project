@extends('layouts.base')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">ชื่อ-นามสกุล</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">อีเมล</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">หน่วยงาน</label>
            <input type="text" name="department" class="form-control" value="{{ old('department') }}">
        </div>

        <div class="mb-3">
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
            <select name="status" class="form-select" value="{{ old('status') }}">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="text-end">
            <a href="{{ route('user.index') }}" class="btn btn-secondary">
                ยกเลิก
            </a>
            <button class="btn btn-success">
                บันทึก
            </button>
        </div>
    </form>
@endsection
