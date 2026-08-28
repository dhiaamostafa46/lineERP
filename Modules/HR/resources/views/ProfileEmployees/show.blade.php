@extends('layouts.app')

@section('title', __('hr::models/hr_payrolls.singular'))

@section('content')
    @livewire('hr::profile.show')
@endsection
