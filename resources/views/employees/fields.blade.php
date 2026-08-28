<div class="form-step active">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Full Name Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('full_name', __('models/employees.fields.full_name') . ':', ['class' => 'required']) !!}
                    {!! Form::text('full_name', @optional($employee)->full_name ?? null, ['class' => 'form-control']) !!}
                </div>




                <!-- Username Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('username', __('models/employees.fields.nikname') . ':',['class' => 'required']) !!}
                    {!! Form::text('username', @optional($employee)->username ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- Phone Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('phone', __('models/employees.fields.phone') . ':',['class' => 'required']) !!}
                    {!! Form::text('phone', @optional($employee)->phone ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- Email Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('email', __('models/employees.fields.email') . ':',['class' => 'required']) !!}
                    {!! Form::text('email', @optional($employee)->email ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- Dob Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('dob', __('models/employees.fields.dob') . ':',['class' => 'required']) !!}
                    {!! Form::date('dob', @optional($employee)->dob ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- Address Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('address', __('models/employees.fields.address') . ':',['class' => 'required']) !!}
                    {!! Form::text('address', @optional($employee)->address ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- National Address Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('national_address', __('models/employees.fields.national_address') . ':') !!}
                    {!! Form::text('national_address', @optional($employee)->national_address ?? null, ['class' => 'form-control']) !!}
                </div>

                <!-- Religion Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('religion', __('models/employees.fields.religion') . ':') !!}
                    {!! Form::text('religion', @optional($employee)->religion ?? null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('branch_id', __('models/employees.fields.branches') . ':') !!}
                    <x-select2-input name="branch_id" :placeholder="__('lang.select_branch')" :list="$branches" :selected_id="old('branch_id', @optional($employee)->branch_id ?? 0)">
                    </x-select2-input>
                </div>

                <!-- Gender Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('gender', __('models/employees.fields.gender') . ':',['class' => 'required']) !!}
                    <x-select2-input name="gender" :placeholder="__('hr::lang.select_gender')" :list="$genders" :selected_id="old('gender', @optional($employee)->gender ?? 0)">
                    </x-select2-input>
                </div>

                <!-- Marital Status Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('marital_status', __('models/employees.fields.marital_status') . ':',['class' => 'required']) !!}
                    <x-select2-input name="marital_status" :placeholder="__('hr::lang.select_marital_status')" :list="$maritalStatuses" :selected_id="old('marital_status', @optional($employee)->marital_status ?? 0)">
                    </x-select2-input>
                </div>

                <!-- Number Of Children Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('number_of_children', __('models/employees.fields.number_of_children') . ':') !!}
                    {!! Form::number('number_of_children', @optional($employee)->number_of_children ?? null, [
                        'class' => 'form-control',
                    ]) !!}
                </div>

                <!-- Nationality Field -->
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('nationality', __('models/employees.fields.nationality') . ':') !!}
                    {!! Form::text('nationality', @optional($employee)->nationality ?? null, ['class' => 'form-control']) !!}
                </div>

            </div>
        </div>
        <div class="card-footer py-4 text-end">
            <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-secondary">
                @lang('crud.cancel')
            </a>
            <button type="button" class="btn btn-sm btn-primary next-step">@lang('crud.Next')</button>
        </div>
    </div>
</div>




  <div class="form-step ">

        <div class="card">
            <div class="card-body">
                <div class="row">

                    @include('employees.bank_fields', ['bank' => @optional($employee)->bank])
                    <hr class="my-10">
                    @include('employees.identity_fields', ['identity' => @optional($employee)->identity])
                </div>
            </div>
            <div class="card-footer py-4 text-end">
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>

                <button type="button" class="prev-step btn btn-sm btn-primary">@lang('crud.Previous')</button>
                <button type="button" class="next-step btn btn-sm btn-primary">@lang('crud.Next')</button>
            </div>
        </div>
    </div>


{{-- @include('employees.bank_fields', ['bank' => @optional($employee)->bank])
<hr class="my-10">
@include('employees.identity_fields', ['identity' => @optional($employee)->identity]) --}}
