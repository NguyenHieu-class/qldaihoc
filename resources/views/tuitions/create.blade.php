@extends('layouts.app')

@section('title', 'Thêm khoản học phí')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('tuitions.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thêm khoản học phí mới</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tuitions.store') }}" method="POST" class="vstack gap-4">
                        @csrf
                        @include('tuitions._form')
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tuitions.index') }}" class="btn btn-outline-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu khoản học phí
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
