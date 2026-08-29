@extends('layouts.app')

@section('title', __('crud.create') . ' ' . __('basicdata::models/db_service_points.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'title' => __('basicdata::models/db_service_points.singular'),
        'pluralTitle' => __('basicdata::models/db_service_points.plural'),
        'indexRoute' => 'basicdata.service_points.index',
        'actionRoute' => 'basicdata.service_points.store',
        'fieldsView' => 'basicdata::service_points.fields'
    ])
@endsection
