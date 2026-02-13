@extends('layouts.app')

@section('content')
<div class="auth-card">
    <div class="auth-card-header">
        <h3><i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ</h3>
    </div>

    <div class="auth-card-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required autofocus placeholder="example@mail.com">
                @error('email')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       required placeholder="••••••••">
                @error('password')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label" for="remember" style="font-size: 0.85rem;">จดจำฉัน</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: #6366f1; text-decoration: none;">ลืมรหัสผ่าน?</a>
                @endif
            </div>

            <button type="submit" class="btn-primary-custom">
                เข้าสู่ระบบ
            </button>
        </form>
    </div>
</div>
@endsection