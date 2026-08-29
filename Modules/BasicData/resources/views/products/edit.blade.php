@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('basicdata::models/db_products.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'isEdit' => true,
        'title' => __('basicdata::models/db_products.singular'),
        'pluralTitle' => __('basicdata::models/db_products.plural'),
        'indexRoute' => 'basicdata.products.index',
        'actionRoute' => ['basicdata.products.update', $product->id],
        'method' => 'PATCH',
        'fieldsView' => 'basicdata::products.fields'
    ])
@endsection
