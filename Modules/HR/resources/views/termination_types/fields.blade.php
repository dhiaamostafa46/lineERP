@foreach (config('langs') as $locale => $language)
<!-- Name Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('name', $language .' '. __('hr::models/hr_termination_types.fields.name') . ':') !!}
    {!! Form::text($locale . '[name]', isset($termination_type) ? $termination_type->translate($locale)->name : null, [
    'class' => 'form-control',
    ]) !!}
</div>
@endforeach

<!-- Status Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('status', __('hr::models/hr_termination_types.fields.status').':') !!}
    {!! Form::select('status', $statuses,null, ['class' => 'form-control','placeholder' => __('lang.select_status')])
    !!}
</div>

<div class="col-12">
    <h4>
        @lang('hr::models/hr_termination_types.fields.rewards')
    </h4>
    <!--begin::Repeater-->
    <div id="rewards">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="rewards">
                @forelse ($termination_type->rewards??[] as $reward)
                <div data-repeater-item>
                    <div class="form-group row">
                        <div class="col-md-3">
                            {!! Form::label('rewards[worked_days]',
                            __('hr::models/hr_termination_type_rewards.fields.worked_days') .
                            ':') !!}
                            {!! Form::number('rewards[worked_days]', $reward->worked_days??null, ['class' =>
                            'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('rewards[fixed_amount]',
                            __('hr::models/hr_termination_type_rewards.fields.fixed_amount') .
                            ':') !!}
                            {!! Form::number('rewards[fixed_amount]', $reward->fixed_amount??0, ['class' =>
                            'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('rewards[percentage]',
                            __('hr::models/hr_termination_type_rewards.fields.percentage') .
                            ':') !!}
                            {!! Form::number('rewards[percentage]', $reward->percentage??0, ['class' => 'form-control'])
                            !!}
                        </div>
                        <div class="col-md-3 ">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-light-danger mt-3 mt-md-8 ">
                                <i class="ki-duotone ki-trash fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div data-repeater-item>
                    <div class="form-group row">
                        <div class="col-md-3">
                            {!! Form::label('rewards[worked_days]',
                            __('hr::models/hr_termination_type_rewards.fields.worked_days') .
                            ':') !!}
                            {!! Form::number('rewards[worked_days]', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('rewards[fixed_amount]',
                            __('hr::models/hr_termination_type_rewards.fields.fixed_amount') .
                            ':') !!}
                            {!! Form::number('rewards[fixed_amount]', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('rewards[percentage]',
                            __('hr::models/hr_termination_type_rewards.fields.percentage') .
                            ':') !!}
                            {!! Form::number('rewards[percentage]', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3 ">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-light-danger mt-3 mt-md-8 ">
                                <i class="ki-duotone ki-trash fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <!--end::Form group-->

            <!--begin::Form group-->
            <div class="form-group mt-5">
                <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                    <i class="ki-duotone ki-plus fs-3"></i>
                </a>
            </div>
            <!--end::Form group-->
        </div>
        <!--end::Repeater-->
    </div>
</div>
@push('scripts')
<script src="{{ asset('admin_assets') }}/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script>
    $('#rewards').repeater({
        initEmpty: false,

        defaultValues: {
            'text-input': 'foo'
        },

        show: function () {
            $(this).slideDown();
        },

        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });
</script>
@endpush
