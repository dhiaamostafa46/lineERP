@extends('pos::layouts.master')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 text-center" style="width: 350px; border-radius: 15px;">
        <h3 class="mb-2">تسجيل الدخول</h3>
        <p class="text-muted mb-4">{{ $device->name }}</p>
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('pos.login.submit', ['device_id' => $device->id]) }}" method="POST" x-data="{ pin: '' }">
            @csrf
            
            @if($device->auth_mode === 'pin')
                <!-- PIN Numpad -->
                <input type="password" name="pin" class="form-control text-center fs-2 mb-3 tracking-widest" readonly x-model="pin" required>
                
                <div class="row g-2 mb-3">
                    <template x-for="n in [1,2,3,4,5,6,7,8,9]">
                        <div class="col-4">
                            <button type="button" class="btn btn-light btn-lg w-100 py-3 shadow-sm" @click="if(pin.length < 6) pin += n" x-text="n"></button>
                        </div>
                    </template>
                    <div class="col-4">
                        <button type="button" class="btn btn-danger btn-lg w-100 py-3 shadow-sm" @click="pin = pin.slice(0, -1)"><i class="fas fa-backspace"></i></button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-light btn-lg w-100 py-3 shadow-sm" @click="if(pin.length < 6) pin += '0'">0</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-warning btn-lg w-100 py-3 shadow-sm" @click="pin = ''">C</button>
                    </div>
                </div>
            @elseif($device->auth_mode === 'password')
                <div class="mb-4">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100 py-2">دخول</button>
        </form>

        <a href="{{ route('pos.select_device') }}" class="text-muted mt-4 d-block text-decoration-none">
            <i class="fas fa-arrow-right"></i> تغيير الجهاز
        </a>
    </div>
</div>
@endsection
