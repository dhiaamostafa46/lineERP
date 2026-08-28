
<!-- Photo Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('user[photo]', __('models/users.fields.photo') . ':') !!}
    <br class="mb-1">
    <x-image-input name="user[photo]" :value="$user->photo_original_path ?? ''" />
</div>

<!-- Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[name]',  __('models/users.fields.name') . ':',['class' => 'required']) !!}
    {!! Form::text('user[name]',  @optional($User_data)->name ?? null , ['class' => 'form-control']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[email]',  __('models/users.fields.email')  . ':',['class' => 'required']) !!}
    {!! Form::text('user[email]',  @optional($User_data)->email ?? null , ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[phone]', __('models/users.fields.phone') . ':',['class' => 'required']) !!}
    {!! Form::text('user[phone]',  @optional($User_data)->phone ?? null , ['class' => 'form-control']) !!}
</div>

{{-- <!-- Password Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[password]', __('models/users.fields.password') . ':',['class' => 'required']) !!}
    {!! Form::password('user[password]', ['class' => 'form-control']) !!}
</div>

<!-- Password_confirmation Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[password_confirmation]', __('models/users.fields.password_confirmation')  . ':',['class' => 'required']) !!}
    {!! Form::password('user[password_confirmation]', ['class' => 'form-control']) !!}

</div> --}}
<!-- Password Field -->

@if(Route::is('hr.employees.create'))
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[password]', __('models/users.fields.password') . ':',['class' => 'required']) !!}
    {!! Form::text('defaulttext',  'كلمة المرور الإفتراضية Evix20 يتم تغيرها عند اول دخول'  , ['class' => 'form-control','disabled' => true]) !!}
    {!! Form::hidden('user[password]', "Evix20" , ['class' => 'form-control']) !!}
</div>

<!-- Password_confirmation Field -->
{{-- <div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[password_confirmation]', __('models/users.fields.password_confirmation')  . ':',['class' => 'required']) !!}
    {!! Form::password('user[password_confirmation]', ['class' => 'form-control']) !!} --}}
    {!! Form::hidden('user[password_confirmation]',"Evix20" , ['class' => 'form-control']) !!}
{{-- </div> --}}

@endif

<!-- Role_id Field -->

{{-- <div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[role_id]',  __('models/users.fields.role_id'). ':') !!}
    {!! Form::select('user[role_id]', $user_roles,   isset($User_data) ? $User_data->getRoleNames()->first() : null , ['class' => 'form-control','placeholder' =>
    __('lang.select_role')]) !!}


</div> --}}
  {!! Form::hidden('user[role_id]',  isset($User_data) ? $User_data->getRoleNames()->first(): $user_roles->name ?? '', ['class' => 'form-control']) !!}
  {!! Form::hidden('user[emp_flage]', "2", ['class' => 'form-control']) !!}

  

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('user[status]', __('models/users.fields.status') . ':') !!}
    {!! Form::select('user[status]', $user_statuses,  @optional($User_data)->status ?? null , ['class' => 'form-control']) !!}
</div>


