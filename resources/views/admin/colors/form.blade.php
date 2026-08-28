@extends('layouts.admin')
@section('title','Màu sắc')
@section('page_title','Màu sắc')
@section('content')
<div class="admin-page"><section class="admin-panel form-narrow"><form method="POST" action="{{ $color->exists ? route('admin.colors.update',$color) : route('admin.colors.store') }}">@csrf @if(${singular}->exists) @method('PUT') @endif<div class="form-field"><label>Tên màu</label><input name="name" value="{{ old('name',$color->name) }}" placeholder="Đen" required></div><div class="form-field"><label>Mã màu</label><input name="hex_code" value="{{ old('hex_code',$color->hex_code ?: '#111827') }}" placeholder="#111827" required></div><div class="form-field"><label>Trạng thái</label><select name="status"><option value="active" @selected(old('status',$color->status ?: 'active')==='active')>Hoạt động</option><option value="inactive" @selected(old('status',$color->status)==='inactive')>Tạm ẩn</option></select></div><div class="form-actions"><button class="admin-primary">Lưu</button><a class="admin-secondary" href="{{ route('admin.colors.index') }}">Hủy</a></div></form></section></div>
@endsection
