@extends('layouts.app')

@section('title', __('accusoft::models/as_journal_entries.statuses.pending'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <h1>@lang('accusoft::models/as_journal_entries.statuses.pending')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted  text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('accusoft.JournalEntry.index') }}" class="text-muted  text-hover-primary">
                                @lang('accusoft::models/as_journal_entries.plural')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('accusoft::models/as_journal_entries.statuses.pending')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->


                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('accusoft.JournalEntry.post')
                        <button type="button" class="btn btn-sm btn-success btn-bulk-post-trigger d-none" onclick="confirmBulkPost()">
                            <i class="fa-solid fa-check-double me-1"></i>
                            @lang('accusoft::lang.post_selected') (<span class="selected-count-badge">0</span>)
                        </button>
                    @endcan
                    <a class="btn btn-sm btn-primary float-right" href="{{ route('accusoft.JournalEntry.index') }}">
                        <i class="fa-solid fa-list"></i>
                        @lang('accusoft::models/as_journal_entries.plural')
                    </a>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="clearfix"></div>
                @if (true)
                    <div class="card shadow-sm my-3 " id="card-filter">
                        <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse"
                            data-bs-target="#kt_docs_card_collapsible" aria-expanded="false">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible" class="collapse">
                            {!! Form::open(['route' => 'accusoft.JournalEntry.pending', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <!-- Name Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('entry_number', __('accusoft::models/as_journal_entries.fields.entry_number') . ':') !!}
                                        {!! Form::text('entry_number', request('entry_number'), ['class' => 'form-control']) !!}
                                    </div>

                                     <div class="form-group col-md-4 mb-3">
                                        {!! Form::label('entry_type', __('accusoft::models/as_journal_entries.fields.entry_type') . ':') !!}
                                        <x-select2-input name="entry_type" :placeholder="__('accusoft::models/as_journal_entries.fields.entry_type')" :list="$types"
                                            :selected_id="request('entry_type')">
                                        </x-select2-input>
                                    </div>

                                    <div class="form-group col-md-4 mb-3">
                                        {!! Form::label('source', __('accusoft::models/as_journal_entries.fields.source') . ':') !!}
                                        <x-select2-input name="source" :placeholder="__('accusoft::models/as_journal_entries.fields.source')" :list="$sources"
                                            :selected_id="request('source')">
                                        </x-select2-input>
                                    </div>

                                    <!-- pagination Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-primary float-right"
                                    href="{{ route('accusoft.JournalEntry.pending') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif

                <form id="bulk-post-form" method="POST" action="{{ route('accusoft.JournalEntry.bulkPost') }}">
                    @csrf
                    <div class="card shadow-sm">
                        <div id="bulk-actions-toolbar" class="card-header border-0 py-3 px-6 d-none bg-light-success rounded-top d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge badge-success fs-6 fw-bold py-2 px-4">
                                    <i class="fa-solid fa-check-circle me-1 text-white"></i>
                                    تم تحديد <span class="selected-count-badge">0</span> قيد
                                </span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" onclick="confirmBulkPost()">
                                    <i class="fa-solid fa-check-double me-1"></i>
                                    @lang('accusoft::lang.post_selected')
                                </button>
                            </div>
                        </div>
                        @include('accusoft::journal_entries.table', ['isPendingView' => true])
                    </div>
                </form>

            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all-header');
        const checkboxes = document.querySelectorAll('.pending-entry-checkbox');
        const countBadges = document.querySelectorAll('.selected-count-badge');
        const bulkTriggers = document.querySelectorAll('.btn-bulk-post-trigger');
        const bulkToolbar = document.getElementById('bulk-actions-toolbar');

        function updateSelectedState() {
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) checkedCount++;
            });

            countBadges.forEach(b => b.textContent = checkedCount);

            if (checkedCount > 0) {
                bulkTriggers.forEach(btn => btn.classList.remove('d-none'));
                if (bulkToolbar) bulkToolbar.classList.remove('d-none');
            } else {
                bulkTriggers.forEach(btn => btn.classList.add('d-none'));
                if (bulkToolbar) bulkToolbar.classList.add('d-none');
            }

            if (checkAll && checkboxes.length > 0) {
                checkAll.checked = (checkedCount === checkboxes.length);
                checkAll.indeterminate = (checkedCount > 0 && checkedCount < checkboxes.length);
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateSelectedState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedState);
        });
    });

    function confirmBulkPost() {
        const checked = document.querySelectorAll('.pending-entry-checkbox:checked');
        const count = checked.length;

        if (count === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("accusoft::lang.no_entries_selected") }}',
                    confirmButtonText: '{{ __("crud.ok") }}'
                });
            } else {
                alert('{{ __("accusoft::lang.no_entries_selected") }}');
            }
            return;
        }

        const msg = '{{ __("accusoft::lang.confirm_bulk_post") }} (' + count + ')';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("accusoft::lang.post_selected") }}',
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#50cd89',
                cancelButtonColor: '#7e8299',
                confirmButtonText: 'نعم، ترحيل وتأكيد الكل',
                cancelButtonText: '{{ __("crud.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulk-post-form').submit();
                }
            });
        } else {
            if (confirm(msg)) {
                document.getElementById('bulk-post-form').submit();
            }
        }
    }
</script>
@endpush
