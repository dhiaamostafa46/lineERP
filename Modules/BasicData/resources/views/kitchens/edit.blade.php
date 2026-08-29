@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('basicdata::models/db_kitchens.singular'))

@section('content')
    @include('basicdata::layouts.partials._form_page', [
        'isEdit' => true,
        'title' => __('basicdata::models/db_kitchens.singular'),
        'pluralTitle' => __('basicdata::models/db_kitchens.plural'),
        'indexRoute' => 'basicdata.kitchens.index',
        'actionRoute' => ['basicdata.kitchens.update', $kitchen->id],
        'method' => 'PATCH',
        'fieldsView' => 'basicdata::kitchens.fields'
    ])
@endsection
