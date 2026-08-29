@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('basicdata::models/db_service_points.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'isEdit' => true,
        'title' => __('basicdata::models/db_service_points.singular'),
        'pluralTitle' => __('basicdata::models/db_service_points.plural'),
        'indexRoute' => 'basicdata.service_points.index',
        'actionRoute' => ['basicdata.service_points.update', $servicePoint->id],
        'method' => 'PATCH',
        'fieldsView' => 'basicdata::service_points.fields'
    ])
@endsection
