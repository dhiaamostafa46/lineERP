
    <div class="card-body">
        <div class="row">
            {{-- User's Profile Image --}}
            <div class="col-lg-3 col-md-4 text-center mb-md-0">
                <label for="photo" class="form-label">{{ __('models/users.fields.photo') }}</label>
                <x-image-input name="photo" :value="$user->photo_original_path ?? ''" />
            </div>

            {{-- User's Main Information --}}
            <div class="col-lg-9 col-md-8">
                {{-- <h5 class="mb-4 border-bottom pb-2">المعلومات الشخصية</h5> --}}
                <div class="row">
                    <!-- Name Field -->
                    <div class="form-group col-md-6 mb-3">
                        {!! Form::label('name', __('models/users.fields.name') . ':', ['class' => 'form-label']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <!-- Email Field -->
                    <div class="form-group col-md-6 mb-3">
                        {!! Form::label('email', __('models/users.fields.email') . ':', ['class' => 'form-label']) !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group col-md-6 mb-3">
                        {!! Form::label('phone', __('models/users.fields.phone') . ':', ['class' => 'form-label']) !!}
                        {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- <h5 class="mb-4 border-bottom pb-2">الوصول والصلاحيات</h5> --}}

        <div class="row">
            <!-- Branch Field -->
            <div class="form-group col-md-6 mb-3">
                {!! Form::label('branch_id', __('models/users.fields.branch_id') . ':', ['class' => 'form-label']) !!}
                <x-select2-input name="branch_id" :placeholder="__('hr::lang.branch_id')" :list="$branchs" :selected_id="old('branch_id', $user->branch_id ?? null)"  required>
                </x-select2-input>
            </div>

            <!-- Role_id Field -->
            <div class="form-group col-md-6 mb-3">
                {!! Form::label('role_id', __('models/users.fields.role_id') . ':', ['class' => 'form-label']) !!}
                {!! Form::select('role_id', $roles, isset($user) ? $user->getRoleNames()->first() : null, [
                    'class' => 'form-control',
                    'placeholder' => __('lang.select_role'),
                    'required'
                ]) !!}
            </div>

            <!-- Status Field -->
            <div class="form-group col-md-6 mb-3">
                {!! Form::label('status', __('models/users.fields.status') . ':', ['class' => 'form-label']) !!}
                {!! Form::select('status', $statuses, null, ['class' => 'form-control', 'required']) !!}
            </div>

            {{-- Password Section --}}
            @if (Route::is('users.create') || Route::is('users.edit'))
                <div class="form-group col-md-6 mb-3">
                    {!! Form::label('user[password]', __('models/users.fields.password') . ':', ['class' => 'form-label required']) !!}
                    <div class="input-group">
                        {!! Form::text('defaulttext', 'كلمة المرور الإفتراضية Evix20 يتم تغيرها عند اول دخول', [
                            'class' => 'form-control',
                            'disabled' => true,
                        ]) !!}
                    </div>
                    {!! Form::hidden('user[password]', 'Evix20') !!}
                    {!! Form::hidden('user[password_confirmation]', 'Evix20') !!}
                </div>
            @endif
        </div>
    </div>

