@extends('layouts.admin')
@section('title','Kích thước')
@section('page_title','Kích thước')
@section('content')
<div class="admin-page"><section class="admin-panel form-narrow"><form method="POST" action="{{ $size->exists ? route('admin.sizes.update',$size) : route('admin.sizes.store') }}">@csrf @if(${singular}->exists) @method('PUT') @endif<div class="form-field"><label>Tên kích thước</label><input name="name" value="{{ old('name',$size->name) }}" placeholder="60 cm" required></div><div class="form-field"><label>Trạng thái</label><select name="status"><option value="active" @selected(old('status',$size->status ?: 'active')==='active')>Hoạt động</option><option value="inactive" @selected(old('status',$size->status)==='inactive')>Tạm ẩn</option></select></div><div class="form-actions"><button class="admin-primary">Lưu</button><a class="admin-secondary" href="{{ route('admin.sizes.index') }}">Hủy</a></div></form></section></div>
@endsection
