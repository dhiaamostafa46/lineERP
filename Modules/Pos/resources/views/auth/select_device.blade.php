@extends('pos::layouts.master')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 text-center" style="width: 400px; border-radius: 15px;">
        <h3 class="mb-4">اختيار نقطة البيع</h3>
        
        <div class="d-grid gap-3">
            @foreach($devices as $device)
                <a href="{{ route('pos.terminal', ['device' => $device->uuid]) }}" class="btn btn-outline-primary btn-lg p-3">
                    <i class="fas fa-desktop fs-3 mb-2 d-block"></i>
                    {{ $device->name }}
                </a>
            @endforeach
        </div>
        
        <a href="{{ url('/') }}" class="text-muted mt-4 text-decoration-none">
            <i class="fas fa-arrow-right"></i> العودة للنظام الرئيسي
        </a>
    </div>
</div>
@endsection
