
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('basic', __('hr::models/hr_salaries.fields.basic') . ':',['class' => 'required']) !!}
    {!! Form::number('basic',  @optional($salary)->basic ?? null, ['class' => 'form-control']) !!}
</div>

<hr class="my-10">
@include('hr::salaries.allowance_fields')
<hr class="my-10">
@include('hr::salaries.deduct_fields')
