<div class="col-12">
    <hr class="my-5">
    <h4>
        @lang('hr::models/hr_GroupTask.fields.employee_id')
    </h4>
    <!--begin::Repeater-->
    <div id="group_details">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="group_details">
                @forelse ($GroupTask->details ??[] as $item)
                <div data-repeater-item>
                    <div class="form-group row">
                        <div class="col-md-3">
                            {!! Form::label('group_details[employee_id]', __('hr::models/hr_GroupTask.fields.employee_id') .
                            ':') !!}

                               <x-select2-input name="group_details[employee_id]" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                    :selected_id="old('group_details.employee_id', $item['employee_id'] ?? null)">
                                </x-select2-input>
                            {{-- {!! Form::select('group_details[employee_id]', $employees, $item['employee_id'] ?? null, [
                            'class' => 'form-control',
                            'placeholder' => __('hr::models/hr_GroupTask.fields.employee_id'),
                            ]) !!} --}}
                        </div>


                        <div class="col-md-4">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-light-danger mt-3 mt-md-8">
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
                            {!! Form::label('group_details[employee_id]', __('hr::models/hr_GroupTask.fields.employee_id') .
                            ':') !!}
                            {{-- {!! Form::select('group_details[employee_id]', $employees, null, ['class' => 'form-control'])
                            !!} --}}

                                 <x-select2-input name="group_details[employee_id]" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                    :selected_id="old('group_details.employee_id', $item['employee_id'] ?? null)">
                                </x-select2-input>
                        </div>


                        <div class="col-md-4">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-light-danger mt-3 mt-md-8">
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
    $('#group_details').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
</script>
@endpush















