@extends('layouts.app')

@section('content')

<div class="container d-flex flex-column align-items-center">

    {{-- ================= LOGIN CARD ================= --}}
    <div class="auth-card w-100" style="max-width: 500px;">
        <div class="auth-card-header text-center">
            <h3><i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ</h3>
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="example@mail.com">
                    @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required
                           placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox"
                               name="remember"
                               id="remember"
                               class="form-check-input">
                        <label class="form-check-label" for="remember" style="font-size: 0.85rem;">
                            จดจำฉัน
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="font-size: 0.85rem; color: #6366f1; text-decoration: none;">
                            ลืมรหัสผ่าน?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>
    </div>

    {{-- ================= DEMO LOGIN SECTION (อยู่ล่าง) ================= --}}
    <div class="auth-card w-100 mt-4" style="max-width: 500px;">
        <div class="auth-card-header text-center">
            <h5 class="mb-0">ทดลองเข้าสู่ระบบ (รหัสผ่าน: 12345678)</h5>
        </div>

        <div class="auth-card-body">

            <div class="p-3 border rounded mb-3"
                 style="cursor:pointer;"
                 onclick="fillLogin('kaizenuser@gmail.com')">
                <strong>ผู้ดูแลระบบ</strong><br>
                <small class="text-muted">kaizenuser@gmail.com</small>
            </div>

             <div class="p-3 border rounded mb-3"
                 style="cursor:pointer;"
                 onclick="fillLogin('sichapatsuckvichai@gmail.com')">
                <strong>ผู้ใช้งานทั่วไป</strong><br>
                <small class="text-muted">sichapatsuckvichai@gmail.com</small>
            </div>

            <div class="p-3 border rounded mb-3"
                 style="cursor:pointer;"
                 onclick="fillLogin('napat.work@gmail.com')">
                <strong>หัวหน้า</strong><br>
                <small class="text-muted">napat.work@gmail.com</small>
            </div>

            <div class="p-3 border rounded"
                 style="cursor:pointer;"
                 onclick="fillLogin('realuser.np@gmail.com')">
                <strong>ประธาน</strong><br>
                <small class="text-muted">realuser.np@gmail.com</small>
            </div>

        </div>
    </div>

</div>

{{-- ================= AUTO FILL SCRIPT ================= --}}
<script>
    function fillLogin(email) {
        document.querySelector('input[name="email"]').value = email;
        document.querySelector('input[name="password"]').value = '12345678';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

@endsection