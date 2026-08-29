@extends('layouts.app')

@section('title', __('crud.create') . ' ' . __('basicdata::models/db_kitchens.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'title' => __('basicdata::models/db_kitchens.singular'),
        'pluralTitle' => __('basicdata::models/db_kitchens.plural'),
        'indexRoute' => 'basicdata.kitchens.index',
        'actionRoute' => 'basicdata.kitchens.store',
        'fieldsView' => 'basicdata::kitchens.fields'
    ])
@endsection
