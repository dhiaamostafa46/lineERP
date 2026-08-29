@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('basicdata::models/db_units.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'isEdit' => true,
        'title' => __('basicdata::models/db_units.singular'),
        'pluralTitle' => __('basicdata::models/db_units.plural'),
        'indexRoute' => 'basicdata.units.index',
        'actionRoute' => ['basicdata.units.update', $unit->id],
        'method' => 'PATCH',
        'fieldsView' => 'basicdata::units.fields'
    ])
@endsection
