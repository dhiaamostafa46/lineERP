<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_posts.fields.type') . ':') !!}
    <x-select2-input name="type" :placeholder="__('hr::lang.select_type')" :list="$types"
        :selected_id="old('type', @optional($post)->type ?? \Modules\HR\App\Models\HrPost::TYPE_NEWS)" />
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_posts.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
        :selected_id="old('status', @optional($post)->status ?? \Modules\HR\App\Models\HrPost::STATUS_DRAFT)" />
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('flage', __('hr::models/hr_posts.fields.flage') . ':') !!}
    <x-select2-input id="flage" name="flage" :placeholder="__('hr::lang.select_status')" :list="$flages"
        :selected_id="old('flage', @optional($post)->flage ?? \Modules\HR\App\Models\HrPost::FLAG_ALL)" />
</div>

<div id="employee_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_posts.fields.employee_id') . ':') !!}
    <x-select2multi-input id="employee_id" name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($post)->employee_id ?? [])" />
</div>

<div id="department_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('department_id', __('hr::models/hr_posts.fields.department_id') . ':') !!}
    <x-select2multi-input id="department_id" name="department_id" :placeholder="__('hr::lang.select_department')" :list="$departments"
        :selected_id="old('department_id', @optional($post)->department_id ?? [])" />
</div>

<div id="branches_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('branch_id', __('hr::models/hr_posts.fields.branch_id') . ':') !!}
    <x-select2multi-input id="branch_id" name="branch_id" :placeholder="__('hr::lang.branches')" :list="$branches"
        :selected_id="old('branch_id', @optional($post)->branch_id ?? [])" />
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('published_at', __('hr::models/hr_posts.fields.published_at') . ':') !!}
    {!! Form::datetimeLocal('published_at', old('published_at', optional(@optional($post)->published_at)->format('Y-m-d\TH:i')), ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('expires_at', __('hr::models/hr_posts.fields.expires_at') . ':') !!}
    {!! Form::datetimeLocal('expires_at', old('expires_at', optional(@optional($post)->expires_at)->format('Y-m-d\TH:i')), ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    <div class="form-check mt-8">
        <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned" value="1"
            {{ old('is_pinned', @optional($post)->is_pinned) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_pinned">@lang('hr::models/hr_posts.fields.is_pinned')</label>
    </div>
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('image', __('hr::models/hr_posts.fields.image') . ':') !!}
    {!! Form::file('image', ['class' => 'form-control', 'accept' => 'image/*']) !!}
    @if (!empty($post?->image_url))
        <div class="mt-3">
            <img src="{{ $post->image_url }}" alt="" class="rounded border" style="max-height: 120px;">
        </div>
    @endif
</div>

@foreach (config('langs') as $locale => $language)
    <div class="col-12"><hr><h5 class="mb-3">{{ $language }}</h5></div>

    <div class="form-group col-sm-12 mb-3">
        {!! Form::label($locale . '[title]', $language . ' ' . __('hr::models/hr_posts.fields.title') . ':') !!}
        {!! Form::text($locale . '[title]', isset($post) ? $post->translate($locale)?->title : null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-12 mb-3">
        {!! Form::label($locale . '[body]', $language . ' ' . __('hr::models/hr_posts.fields.body') . ':') !!}
        {!! Form::textarea($locale . '[body]', isset($post) ? $post->translate($locale)?->body : null, [
            'class' => 'form-control summernote-body',
            'id' => 'summernote-' . $locale,
            'rows' => 5,
        ]) !!}
    </div>
@endforeach

@section('scripts')
<script>
    $(document).ready(function () {
        $('.summernote-body').each(function () {
            $(this).summernote({ height: 220 });
        });

        function toggleFields(flageValue) {
            $('#employee_field, #department_field, #branches_field').hide();

            if (flageValue == 2) {
                $('#employee_field').show();
            } else if (flageValue == 3) {
                $('#department_field').show();
            } else if (flageValue == 4) {
                $('#branches_field').show();
            }
        }

        $('#flage').on('change', function () {
            toggleFields($(this).val());
        }).trigger('change');

        $('form').on('submit', function () {
            $('.summernote-body').each(function () {
                $(this).val($(this).summernote('code'));
            });
        });
    });
</script>
@endsection
