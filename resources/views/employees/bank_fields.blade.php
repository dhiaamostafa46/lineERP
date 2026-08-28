<h2 class="text-primary text-center mb-5">@lang('models/employees.fields.bank_details')</h2>
<!-- Bank Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('bank_name', __('models/employees.fields.bank_name') . ':',['readonly' => true,'class' => 'required']) !!}
    {!! Form::text('bank_name', isset($bank) ? $bank->bank_name : null, ['class' => 'form-control','readonly' => true]) !!}
</div>

<!-- IBAN Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('iban', __('models/employees.fields.iban') . ':',['class' => 'required']) !!}
    {!! Form::text('iban', isset($bank) ? $bank->iban : null, ['class' => 'form-control']) !!}
</div>

@push('scripts')
<script>
    var banks = @json(config('banks')??"");
    $(document).ready(function () {
        $('#iban').on('keyup',function () {
            checkIBAN($(this).val());
        });
    });

    function checkIBAN(value) {
        let res = value.replace(/\s/g, "");
        var result = Number(res.slice(4, 6));
        if (res.length == 24) {
            $('#iban').removeClass("is-invalid");
        } else {
            $('#iban').addClass("is-invalid");
        }
        $('#bank_name').val(banks[result]);
    }
</script>
@endpush
