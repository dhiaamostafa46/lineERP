<div class="row">
    <!-- Code Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('accusoft::models/as_tree_account.fields.code') . ':') !!}
        {!! Form::text('code', isset($treeAccount) ? $treeAccount->code : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>

    <!-- Parent Id Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('parent_id', __('accusoft::models/as_tree_account.fields.parent_id') . ':') !!}
        <x-select2-input name="parent_id" :placeholder="__('accusoft::models/as_tree_account.fields.parent_id')" :list="$TreeAccounts ?? []" :selected_id="old('parent_id', isset($treeAccount) ? $treeAccount->parent_id : null)">
        </x-select2-input>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#parent_id').on('change', function() {
            var parentId = $(this).val();
            if (parentId) {
                $.get("{{ url('accusoft/tree-accounts/get-next-code') }}", {parent_id: parentId}, function(data) {
                    $('#code').val(data);
                });
            }
        });
    });
</script>
@endpush

<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('accusoft::models/as_tree_account.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($treeAccount) ? $treeAccount->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Description Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[description]', $language . ' ' . __('accusoft::models/as_tree_account.fields.description') . ':') !!}
            {!! Form::textarea($locale . '[description]', isset($treeAccount) ? $treeAccount->translate($locale)->description : null, [
                'class' => 'form-control', 'rows' => 2
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <!-- Account Type Field -->

    <!-- Type Field (Nature) -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('accusoft::models/as_tree_account.fields.type') . ':') !!}
        <div class="mt-2">
            @foreach($types as $key => $value)
                <div class="form-check form-check-inline">
                    {!! Form::radio('type', $key, old('type', @optional($treeAccount)->type) == $key, ['class' => 'form-check-input', 'id' => 'type_'.$key]) !!}
                    {!! Form::label('type_'.$key, $value, ['class' => 'form-check-label']) !!}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('accusoft::models/as_tree_account.fields.status') . ':') !!}
        <div class="mt-2">
            @foreach($statuses as $key => $value)
                <div class="form-check form-check-inline">
                    {!! Form::radio('status', $key, old('status', @optional($treeAccount)->status ?? 1) == $key, ['class' => 'form-check-input', 'id' => 'status_'.$key]) !!}
                    {!! Form::label('status_'.$key, $value, ['class' => 'form-check-label']) !!}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Is Leaf Field -->
    <div class="form-group col-sm-6 mb-3 d-flex align-items-center">
        <div class="form-check mt-4">
            {!! Form::hidden('is_leaf', 0) !!}
            {!! Form::checkbox('is_leaf', 1, isset($treeAccount) ? $treeAccount->is_leaf : true, ['class' => 'form-check-input', 'id' => 'is_leaf']) !!}
            {!! Form::label('is_leaf', __('accusoft::models/as_tree_account.fields.is_leaf'), ['class' => 'form-check-label']) !!}
        </div>
    </div>


        <!-- Is Leaf Field -->
    <div class="form-group col-sm-6 mb-3 d-flex align-items-center">
        <div class="form-check mt-4">
            {!! Form::hidden('use_cost_center', 0) !!}
            {!! Form::checkbox('use_cost_center', 1, isset($treeAccount) ? $treeAccount->use_cost_center : false, ['class' => 'form-check-input', 'id' => 'use_cost_center']) !!}
            {!! Form::label('use_cost_center', __('accusoft::models/as_tree_account.fields.use_cost_center'), ['class' => 'form-check-label']) !!}
        </div>
    </div>
</div>
