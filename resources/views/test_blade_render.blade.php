@extends('layouts.app')
@section('content')
    <div>
        {{ \Illuminate\Support\Facades\Blade::render('<h1>Hello {{ $name }}</h1>', ['name' => 'World']) }}
    </div>
@endsection
