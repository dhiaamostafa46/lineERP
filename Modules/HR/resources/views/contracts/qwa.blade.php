<section class="wrapper" id="create_pdf_contract">
    <div class="row">



         <style>
            .Arabic_section {
                direction: rtl;
                float: right;

            }

            .English_section {
                direction: ltr;
                float: left;

            }
        </style>








 @include('hr::contracts.templete')
        ----------------------------------------------------------


        <h1 class="text-center" style="color: #478f8c">

            {{ trans('hr::models/hr_contracts.employment_contract', [], 'ar') }}
            <br>
            {{ trans('hr::models/hr_contracts.employment_contract', [], 'en') }}
        </h1>
        <br>
        <!-- العمود الأول بالعربية -->
        {{-- <div class="col-6" dir="rtl">
            @include('hr::contracts.sectionAR')
        </div>

        <!-- العمود الثاني بالإنجليزية -->
        <div class="col-6" dir="ltr">
            @include('hr::contracts.sectionEn')
        </div> --}}


        @include('hr::contracts.Qwa.date')
        @include('hr::contracts.Qwa.first_party')
        @include('hr::contracts.Qwa.second_party')
        @include('hr::contracts.Qwa.contract')

        @include('hr::contracts.Qwa.work')
        @include('hr::contracts.Qwa.salary')

        @include('hr::contracts.Qwa.commitments')
        @include('hr::contracts.Qwa.termination')

        @include('hr::contracts.Qwa.items')
        @include('hr::contracts.Qwa.end_of_service')
        @include('hr::contracts.Qwa.law')
        @include('hr::contracts.Qwa.signature')
    </div>


    @section('scripts')
        <script>
            // استخدام html2pdf.js لتحويل الصفحة إلى PDF
            document.getElementById('download-pdf').addEventListener('click', () => {
                const element = document.getElementById('create_pdf_contract');
                const opt = {
                    margin: 1,
                    filename: 'page.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'letter',
                        orientation: 'portrait'
                    }
                };

                // تحويل المحتوى إلى PDF
                html2pdf().from(element).set(opt).save();
            });
        </script>
    @endsection
</section>


<div class="modal fade" tabindex="-1" id="kt_modal_1">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'hr.ContractItem.store', 'files' => true]) !!}
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('hr::models/hr_contract_items.singular') }} </h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <input type="hidden" name="employee_id" value="{{ $contract->employee_id }}">
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <!-- Arabic Description Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('Desc_ar', __('hr::models/hr_contract_items.fields.description_ar') . ':') !!}
                    {!! Form::textarea('Desc_ar', null, ['class' => 'form-control']) !!}
                </div>

                <!-- English Description Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('Desc_En', __('hr::models/hr_contract_items.fields.description_en') . ':') !!}
                    {!! Form::textarea('Desc_En', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="modal-footer">
                <a data-bs-dismiss="modal" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
                {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
