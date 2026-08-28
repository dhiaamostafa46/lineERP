<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_report_types.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($report_type) ? $report_type->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
    @foreach (config('langs') as $locale => $language)
    <!-- Description Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('description', $language .' '. __('hr::models/hr_report_types.fields.description') . ':') !!}
        {!! Form::textarea($locale . '[description]', isset($report_type) ?
        $report_type->translate($locale)->description : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>