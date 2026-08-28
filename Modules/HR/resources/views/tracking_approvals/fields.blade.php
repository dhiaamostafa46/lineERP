<!-- Trackable Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('trackable', __('hr::models/hr_tracking_approvals.fields.trackable').':') !!}
    {!! Form::text('trackable', null, ['class' => 'form-control']) !!}
</div>


<!-- User Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user_id', __('hr::models/hr_tracking_approvals.fields.user_id').':') !!}
    {!! Form::text('user_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Sort Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('sort', __('hr::models/hr_tracking_approvals.fields.sort').':') !!}
    {!! Form::text('sort', null, ['class' => 'form-control']) !!}
</div>


<!-- Is Current Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('is_current', __('hr::models/hr_tracking_approvals.fields.is_current').':') !!}
    {!! Form::text('is_current', null, ['class' => 'form-control']) !!}
</div>