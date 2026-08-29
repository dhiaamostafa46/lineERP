@extends('layouts.app')

@section('title', __('crud.create') . ' ' . __('basicdata::models/db_categories.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'title' => __('basicdata::models/db_categories.singular'),
        'pluralTitle' => __('basicdata::models/db_categories.plural'),
        'indexRoute' => 'basicdata.categories.index',
        'actionRoute' => 'basicdata.categories.store',
        'fieldsView' => 'basicdata::categories.fields'
    ])
@endsection
