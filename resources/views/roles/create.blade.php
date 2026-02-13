@extends('layouts.base')

@section('content')
<form action="{{ route('roles.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">ชื่อบทบาท</label>
        <input type="text" name="role_name" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">รายละเอียดบทบาท</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">บันทึก</button>
</form>
@endsection
