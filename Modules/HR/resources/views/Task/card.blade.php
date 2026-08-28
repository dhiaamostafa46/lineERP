<div class="timeline timeline-border-dashed">
    <!--begin::Timeline item-->
    <div class="timeline-item">
        <!--begin::Timeline line-->
        <div class="timeline-line"></div>
        <!--end::Timeline line-->
        <!--begin::Timeline icon-->
        <div class="timeline-icon">
            <i class="ki-duotone ki-pencil fs-2 text-gray-500">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Timeline icon-->
        <!--begin::Timeline content-->
        <div class="timeline-content mb-10 mt-n1">
            <!--begin::Timeline heading-->
            <div class="pe-3 mb-5">
                <!--begin::Title-->
                <div class="fs-5 fw-semibold mb-2">     @lang('lang.system') :</div>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="d-flex align-items-center mt-1 fs-6">
                    <!--begin::Info-->
                    <div class="text-muted me-2 fs-7"> {{ $Tasts->created_at }} </div>
                    <!--end::Info-->
                </div>
                <!--end::Description-->
            </div>
            <!--end::Timeline heading-->
            <!--begin::Timeline details-->
            <div class="overflow-auto pb-5">
                <!--begin::Notice-->
                <div
                    class="notice d-flex bg-light-success rounded border-primary border border-dashed min-w-lg-600px flex-shrink-0 p-6">

                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                        <!--begin::Content-->
                        <div class="mb-3 mb-md-0 fw-semibold">
                            <h4 class="text-gray-900 fw-bold"> {{ $Tasts->title }} </h4>
                            <div class="fs-6 text-gray-700 pe-7"> @php echo $Tasts->description @endphp </div>
                        </div>
                        <!--end::Content-->

                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Notice-->
            </div>
            <!--end::Timeline details-->
        </div>
        <!--end::Timeline content-->
    </div>
    <!--end::Timeline item-->


    @foreach ($TastDetails as $item)
        <!--begin::Timeline item-->
        <div class="timeline-item">
            <!--begin::Timeline line-->
            <div class="timeline-line"></div>
            <!--end::Timeline line-->
            <!--begin::Timeline icon-->
            <div class="timeline-icon">
                <i class="ki-duotone ki-pencil fs-2 text-gray-500">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <!--end::Timeline icon-->
            <!--begin::Timeline content-->
            <div class="timeline-content mb-10 mt-n1">
                <!--begin::Timeline heading-->
                <div class="pe-3 mb-5">
                    <!--begin::Title-->
                    <div class="fs-5 fw-semibold mb-2">{{ $item->User->name ??'' }} :</div>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="d-flex align-items-center mt-1 fs-6">
                        <!--begin::Info-->
                        <div class="text-muted me-2 fs-7"> {{ $item->created_at }} </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Description-->
                </div>
                <!--end::Timeline heading-->
                <!--begin::Timeline details-->
                <div class="overflow-auto pb-5">
                    <!--begin::Notice-->
                    <div
                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed min-w-lg-600px flex-shrink-0 p-6">

                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <!--begin::Content-->
                            <div class="mb-3 mb-md-0 fw-semibold">
                                <h4 class="text-gray-900 fw-bold"> {{ $Tasts->title }} </h4>
                                <div class="fs-6 text-gray-700 pe-7">   @php echo $item->description @endphp </div>
                            </div>
                            <!--end::Content-->

                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Notice-->
                </div>
                <!--end::Timeline details-->
            </div>
            <!--end::Timeline content-->
        </div>
        <!--end::Timeline item-->
    @endforeach




</div>






















<div class="modal fade" tabindex="-1" id="Create_task_details">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"> </h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                {!! Form::open(['route' => 'hr.TaskDetails.store', 'files' => true]) !!}


                @include('hr::Task.fields_teskdetails')





                <div class="card-footer py-4 text-end">
                    <a data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-secondary">

                        @lang('crud.cancel')
                    </a>
                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary']) !!}
                </div>

                {!! Form::close() !!}

            </div>


        </div>
    </div>
</div>








@section('scripts')
<script>
    $(document).ready(function() {

        $('#summernote').summernote({
                height: 200
            });
    });
</script>
@endsection
