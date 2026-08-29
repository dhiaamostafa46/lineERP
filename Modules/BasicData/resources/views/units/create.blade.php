@extends('layouts.app')

@section('title', __('crud.create') . ' ' . __('basicdata::models/db_units.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'title' => __('basicdata::models/db_units.singular'),
        'pluralTitle' => __('basicdata::models/db_units.plural'),
        'indexRoute' => 'basicdata.units.index',
        'actionRoute' => 'basicdata.units.store',
        'fieldsView' => 'basicdata::units.fields'
    ])
@endsection
