<h2 class="text-primary text-center mb-5">@lang('hr::models/hr_allowances.plural')</h2>

@forelse ($allowances??[] as $allowance)
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('amount', $allowance->name) !!}
    {!! Form::number('allowances['.$allowance->id.']', $salary_allowances[$allowance->id] ?? null, ['class' => 'form-control']) !!}
</div>
@empty

@endforelse
