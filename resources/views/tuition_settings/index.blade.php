@extends('layouts.app')

@section('title', 'Cấu hình học phí')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Cấu hình học phí môn học</h1>
            <p class="text-muted mb-0">Học phí = Số tiền một tín * Hệ số môn học * Số tín</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Thiết lập mức thu học phí</div>
                <div class="card-body">
                    <form action="{{ route('tuition-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="per_credit_amount" class="form-label">Số tiền một tín (VNĐ)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                <input
                                    type="number"
                                    name="per_credit_amount"
                                    id="per_credit_amount"
                                    class="form-control @error('per_credit_amount') is-invalid @enderror"
                                    value="{{ old('per_credit_amount', $setting->per_credit_amount) }}"
                                    step="0.01"
                                    min="0"
                                    required
                                >
                                <span class="input-group-text">VNĐ</span>
                                @error('per_credit_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Gợi ý tính toán</div>
                <div class="card-body">
                    <p class="mb-3">
                        Công thức học phí được áp dụng tự động dựa trên hệ số của từng môn học và số tín chỉ của môn đó.
                        Bạn chỉ cần điều chỉnh mức thu cho một tín chỉ.
                    </p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Mức thu hiện tại:</strong>
                            <span class="float-end">{{ number_format($setting->per_credit_amount, 2, ',', '.') }} VNĐ</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Ví dụ:</strong>
                            <span class="d-block small text-muted">Môn 3 tín, hệ số 1.2 ⇒ Học phí = {{ number_format($setting->per_credit_amount, 2, ',', '.') }} × 1.2 × 3</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
