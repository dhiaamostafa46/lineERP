@extends('layouts.app')

@section('title', __('hr::lang.my_requests'))

@section('content')
    @livewire('hr::my-requests-create')
@endsection
