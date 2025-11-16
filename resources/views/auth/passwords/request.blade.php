@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">{{ __('Quên mật khẩu cho giáo viên / sinh viên') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('matched_user'))
                        @php $user = session('matched_user'); @endphp
                        <div class="alert alert-info">
                            <p class="mb-1"><strong>{{ $user['role'] }}:</strong> {{ $user['name'] }}</p>
                            <p class="mb-1"><strong>{{ __('Mã định danh') }}:</strong> {{ $user['identifier'] }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $user['email'] }}</p>
                        </div>
                    @endif

                    <p class="text-muted">{{ __('Nhập địa chỉ email hoặc mã sinh viên / giáo viên của bạn để gửi yêu cầu đặt lại mật khẩu tới quản trị viên.') }}</p>

                    <form method="POST" action="{{ route('password.request.store') }}">
                        @csrf

                        <div class="mb-3 row">
                            <label for="identifier" class="col-md-4 col-form-label text-md-end">{{ __('Email hoặc mã') }}</label>

                            <div class="col-md-6">
                                <input id="identifier" type="text" class="form-control @error('identifier') is-invalid @enderror" name="identifier" value="{{ old('identifier') }}" required autofocus>

                                @error('identifier')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Gửi yêu cầu') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-link">{{ __('Quay lại đăng nhập') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
