<!-- Employee Id Field -->
<input type="hidden" value="{{$employee_id}}" name="employee_id">

<!-- Date -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('request_date', __('hr::models/hr_absentrequest.fields.requestdate') . ':') !!}
    {!! Form::date('request_date', isset($holiday) ? $holiday->end_at : null, ['class' => 'form-control']) !!}
</div>
<!-- From At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('from_at', __('hr::models/hr_absentrequest.fields.from_at') . ':') !!}
    {!! Form::time('from_at', isset($holiday) ? $holiday->from_at : null, ['class' => 'form-control']) !!}
</div>


<!-- End At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('end_at', __('hr::models/hr_absentrequest.fields.end_at') . ':') !!}
    {!! Form::time('end_at', isset($holiday) ? $holiday->end_at : null, ['class' => 'form-control']) !!}
</div>
<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('details', __('hr::models/hr_absentrequest.fields.details') . ':') !!}
    {!! Form::textarea('details', null, ['class' => 'form-control']) !!}
</div>
{!! Form::submit(__('hr::lang.submit'), ['class' => 'btn btn-sm btn-primary']) !!}
@push('scripts')
<script>
     var dtToday = new Date();
    $("#request_date").flatpickr({
        minDate:dtToday,
    });
   
</script>
@endpush
