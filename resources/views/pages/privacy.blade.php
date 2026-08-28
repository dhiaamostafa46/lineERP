@extends('layouts.public')

@section('title', __('Privacy.main_title'))

@push('styles')
<style>
    .privacy-wrapper {
        padding: 3rem 0 4rem;
    }

    .privacy-wrapper .headtext {
        font-size: 1.35rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: #181c32;
    }

    .privacy-wrapper .head2textone {
        font-size: 1rem;
        font-weight: 600;
        color: #5e6278;
        margin-bottom: 0.75rem;
    }

    .privacy-wrapper .textprograf {
        color: #3f4254;
        margin-bottom: 0.5rem;
    }

    .privacy-wrapper ul {
        padding-inline-start: 1.25rem;
        margin-bottom: 1rem;
    }

    .privacy-wrapper ul li {
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<section class="wrapper privacy-wrapper">
    <div class="container">
        <h1 class="text-center">{{ trans('Privacy.main_title') }}</h1>

        <br><br><br><br>

        <h4 class="head2textone">{{ trans('Privacy.last_updated') }}</h4>
        <p class="lh-lg textprograf">{{ trans('Privacy.intro_paragraph') }}</p>

        <h3 class="headtext">{{ trans('Privacy.data_collection_title') }}</h3>
        <p class="lh-lg textprograf">{{ trans('Privacy.data_collection_intro') }}</p>
        <ul>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.data_auth_label') }}</strong> {{ trans('Privacy.data_auth_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.data_profile_label') }}</strong> {{ trans('Privacy.data_profile_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.data_requests_label') }}</strong> {{ trans('Privacy.data_requests_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.data_location_label') }}</strong> {{ trans('Privacy.data_location_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.data_permissions_label') }}</strong> {{ trans('Privacy.data_permissions_content') }}</p></li>
        </ul>

        <h3 class="headtext">{{ trans('Privacy.data_usage_title') }}</h3>
        <p class="lh-lg textprograf">{{ trans('Privacy.data_usage_intro') }}</p>
        <ul>
            <li><p class="lh-lg textprograf">{{ trans('Privacy.data_usage_point1') }}</p></li>
            <li><p class="lh-lg textprograf">{{ trans('Privacy.data_usage_point2') }}</p></li>
            <li><p class="lh-lg textprograf">{{ trans('Privacy.data_usage_point3') }}</p></li>
            <li><p class="lh-lg textprograf">{{ trans('Privacy.data_usage_point4') }}</p></li>
            <li><p class="lh-lg textprograf">{{ trans('Privacy.data_usage_point5') }}</p></li>
        </ul>

        <h3 class="headtext">{{ trans('Privacy.data_sharing_title') }}</h3>
        <ul>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.sharing_internal_label') }}</strong> {{ trans('Privacy.sharing_internal_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.sharing_external_label') }}</strong> {{ trans('Privacy.sharing_external_content') }}</p></li>
        </ul>

        <h3 class="headtext">{{ trans('Privacy.data_security_title') }}</h3>
        <p class="lh-lg textprograf">{{ trans('Privacy.data_security_content') }}</p>

        <h3 class="headtext">{{ trans('Privacy.app_permissions_title') }}</h3>
        <p class="lh-lg textprograf">{{ trans('Privacy.app_permissions_intro') }}</p>
        <ul>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.perm_location_label') }}</strong> {{ trans('Privacy.perm_location_content') }}</p></li>
            <li><p class="lh-lg textprograf"><strong>{{ trans('Privacy.perm_notify_label') }}</strong> {{ trans('Privacy.perm_notify_content') }}</p></li>
        </ul>

        <h3 class="headtext">{{ trans('Privacy.user_rights_title') }}</h3>
        <p class="lh-lg textprograf">{{ trans('Privacy.user_rights_content') }}</p>
    </div>
</section>
@endsection
