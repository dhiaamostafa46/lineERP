<h2 class="text-danger text-center mb-5">@lang('hr::models/hr_deducts.plural')</h2>

@forelse ($deducts??[] as $deduct)
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('amount', $deduct->name) !!}
    {!! Form::number('deducts['.$deduct->id.']', $salary_deducts[$deduct->id]??null, ['class' => 'form-control']) !!}
</div>
@empty

@endforelse
