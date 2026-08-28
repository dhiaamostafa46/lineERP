@extends('errors.layout')

@section('title')
    @lang('errors.error') {{ $statusCode ?? '' }}
@endsection

@section('code', $statusCode ?? 'Error')

@section('message')
    {{ $exception->getMessage() ?: __('errors.generic_message') }}
@endsection
