@extends('layouts.base')

@section('content')
<div class="mb-3 text-end">
    <a href="{{ route('roles.create') }}" class="btn btn-success">
        + เพิ่ม
    </a>
</div>
<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr class="text-center">
            <th width="25%">ชื่อบทบาท</th>
            <th>รายละเอียดบทบาท</th>
            <th width="20%">จัดการ</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($roles as $item)
            <tr>
                <td class="fw-semibold">{{ $item->role_name }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">
                    {{-- <a href="{{ route('roles.create') }}" class="btn btn-sm btn-success">เพิ่ม</a> --}}
                   <a href="{{ route('roles.edit', $item->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
