<form wire:submit.prevent="save">
    <div class="row">
        @foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $language)
            <div class="form-group col-sm-6 mb-3">
                <label for="name_{{ $locale }}" class="form-label">
                    {{ $language }} @lang('invoices::models/inv_customers.fields.name'):
                </label>
                <input type="text" id="name_{{ $locale }}" wire:model.defer="name.{{ $locale }}" 
                    class="form-control @error('name.'.$locale) is-invalid @enderror">
                @error('name.'.$locale)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endforeach

        <div class="form-group col-sm-4 mb-3">
            <label for="phone" class="form-label">@lang('invoices::models/inv_customers.fields.phone'):</label>
            <input type="text" id="phone" wire:model.defer="phone" 
                class="form-control @error('phone') is-invalid @enderror">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-4 mb-3">
            <label for="email" class="form-label">@lang('invoices::models/inv_customers.fields.email'):</label>
            <input type="email" id="email" wire:model.defer="email" 
                class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-4 mb-3">
            <label for="vat_number" class="form-label">@lang('invoices::models/inv_customers.fields.vat_number'):</label>
            <input type="text" id="vat_number" wire:model.defer="vat_number" 
                class="form-control @error('vat_number') is-invalid @enderror">
            @error('vat_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-4 mb-3">
            <label for="cr_number" class="form-label">@lang('invoices::models/inv_customers.fields.cr_number'):</label>
            <input type="text" id="cr_number" wire:model.defer="cr_number" 
                class="form-control @error('cr_number') is-invalid @enderror">
            @error('cr_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-4 mb-3" wire:ignore>
            <label for="tree_account_id" class="form-label">@lang('invoices::models/inv_customers.fields.tree_account_id'):</label>
            <select class="form-select form-control select2-modal" id="tree_account_id" wire:model="tree_account_id">
                <option value="">@lang('lang.select')</option>
                @foreach($accounts as $id => $nm)
                    <option value="{{ $id }}">{{ $nm }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-4 mb-3" wire:ignore>
            <label for="branch_id" class="form-label">@lang('invoices::models/inv_customers.fields.branch_id'):</label>
            <select class="form-select form-control select2-modal" id="branch_id" wire:model="branch_id">
                <option value="">@lang('lang.select')</option>
                @foreach($branches as $id => $nm)
                    <option value="{{ $id }}">{{ $nm }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-4 mb-3">
            <label for="credit_limit" class="form-label">@lang('invoices::models/inv_customers.fields.credit_limit'):</label>
            <input type="number" step="0.01" id="credit_limit" wire:model.defer="credit_limit" 
                class="form-control @error('credit_limit') is-invalid @enderror">
            @error('credit_limit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-sm-4 mb-3">
            <label for="status" class="form-label">@lang('invoices::models/inv_customers.fields.status'):</label>
            <select class="form-select" id="status" wire:model.defer="status">
                <option value="1">@lang('lang.active')</option>
                <option value="0">@lang('lang.inactive')</option>
            </select>
        </div>

        <div class="form-group col-sm-12 mb-3">
            <label for="address" class="form-label">@lang('invoices::models/inv_customers.fields.address'):</label>
            <textarea id="address" wire:model.defer="address" rows="2" 
                class="form-control @error('address') is-invalid @enderror"></textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="modal-footer py-4 px-0">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">@lang('crud.cancel')</button>
        <button type="submit" class="btn btn-sm btn-primary">
            @lang('crud.save')
            <div wire:loading wire:target="save">
                <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
            </div>
        </button>
    </div>
</form>

@script
<script>
    $(document).ready(function() {
        $('.select2-modal').select2({
            dropdownParent: $('#createCustomerModal'),
            width: '100%'
        }).on('change', function(e) {
            $wire.set($(this).attr('wire:model'), $(this).val());
        });
    });

    $wire.on('customer-data-loaded', (data) => {
        setTimeout(() => {
            $('#tree_account_id').val(data[0].tree_account_id).trigger('change');
            $('#branch_id').val(data[0].branch_id).trigger('change');
            $('#status').val(data[0].status).trigger('change');
        }, 100);
    });
</script>
@endscript
