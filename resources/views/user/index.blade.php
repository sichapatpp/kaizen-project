@extends('layouts.base')

@section('content')
<div class="mb-3 text-end">
    <a href="{{ route('user.create') }}" class="btn btn-success">
        + เพิ่มผู้ใช้
    </a>
</div>
<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr class="text-center">
            <th width="20%">ชื่อ-นามสกุล</th>
            <th width="25%">อีเมล</th>
            <th width="20%">หน่วยงาน</th>
            <th width="15%">สถานะ</th>
            <th width="20%">จัดการ</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($user as $item)
            <tr>
                <td class="fw-semibold">{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->department ?? '-' }}</td>
                <td class="text-center">{{ $item->status }}</td>
                <td class="text-center">
                    <a href="{{ route('user.edit', $item->id) }}"class="btn btn-sm btn-warning">แก้ไข</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
