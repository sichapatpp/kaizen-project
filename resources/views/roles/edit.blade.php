@extends('layouts.base')

@section('content')
<form action="{{ route('roles.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">ชื่อบทบาท</label>
        <input type="text" name="role_name"
               class="form-control"
               value="{{ $role->role_name }}">
    </div>

    <div class="mb-3">
        <label class="form-label">รายละเอียดบทบาท</label>
        <textarea name="description"
                  class="form-control">{{ $role->description }}</textarea>
    </div>

    <button class="btn btn-success">บันทึก</button>
</form>
@endsection

