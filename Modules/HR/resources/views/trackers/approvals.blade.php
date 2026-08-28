<div class="col-12">
    <hr class="my-5">
    <h4>
        @lang('hr::models/hr_tracking_approvals.plural')
    </h4>
    <!--begin::Repeater-->
    <div id="tracker_approvals">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="tracker_approvals">
                @forelse ($approvals??[] as $item)
                <div data-repeater-item>
                    <div class="form-group row">
                        <div class="col-md-3">
                            {!! Form::label('tracker_approvals[user_id]', __('hr::models/hr_settings.fields.user_id') .
                            ':') !!}
                            {!! Form::select('tracker_approvals[user_id]', $users, $item['user_id'] ?? null, [
                            'class' => 'form-control',
                            'placeholder' => __('hr::lang.select_user'),
                            ]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('tracker_approvals[sort]', __('hr::models/hr_settings.fields.sort') . ':')
                            !!}
                            {!! Form::number('tracker_approvals[sort]', $item['sort']?? null, ['class' =>
                            'form-control']) !!}
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
                            {!! Form::label('tracker_approvals[user_id]', __('hr::models/hr_settings.fields.user_id') .
                            ':') !!}
                            {!! Form::select('tracker_approvals[user_id]', $users, null, ['class' => 'form-control'])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('tracker_approvals[sort]', __('hr::models/hr_settings.fields.sort') . ':')
                            !!}
                            {!! Form::number('tracker_approvals[sort]', null, ['class' => 'form-control']) !!}
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
    $('#tracker_approvals').repeater({
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
