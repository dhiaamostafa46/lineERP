@extends('layouts.app')

@section('title', __('crud.create') . ' ' . __('basicdata::models/db_products.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'title' => __('basicdata::models/db_products.singular'),
        'pluralTitle' => __('basicdata::models/db_products.plural'),
        'indexRoute' => 'basicdata.products.index',
        'actionRoute' => 'basicdata.products.store',
        'fieldsView' => 'basicdata::products.fields'
    ])
@endsection
