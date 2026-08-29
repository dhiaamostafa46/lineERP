@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('basicdata::models/db_categories.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'isEdit' => true,
        'title' => __('basicdata::models/db_categories.singular'),
        'pluralTitle' => __('basicdata::models/db_categories.plural'),
        'indexRoute' => 'basicdata.categories.index',
        'actionRoute' => ['basicdata.categories.update', $category->id],
        'method' => 'PATCH',
        'fieldsView' => 'basicdata::categories.fields'
    ])
@endsection
