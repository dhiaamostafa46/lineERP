<!-- Name Field -->
<div class="form-group col-6">
    {!! Form::label('name', __('models.themes.fields.name') . ':', ['class' => 'm-auto']) !!}
    {!! Form::text('name', isset($theme) ? $theme->name ?? '' : '', ['class' => 'form-control']) !!}
</div>
<div class="row">


    <div class="col-6">
        <div class="form-group">
            <h2 class="text-center">@lang('models.settings.fields.panel')</h2>
        </div>

        <!-- Panel Body Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_body_background', __('models.themes.fields.panel_body_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('panel_body_background', isset($theme) ? $theme->panel_body_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Aside Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_aside_background', __('models.themes.fields.panel_aside_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('panel_aside_background', isset($theme) ? $theme->panel_aside_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Aside Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_aside_color', __('models.themes.fields.panel_aside_color') . ':', ['class' =>
            'm-auto']) !!}
            {!! Form::color('panel_aside_color', isset($theme) ? $theme->panel_aside_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Btn Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_btn_background', __('models.themes.fields.panel_btn_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('panel_btn_background', isset($theme) ? $theme->panel_btn_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Header Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_header_color', __('models.themes.fields.panel_header_color') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('panel_header_color', isset($theme) ? $theme->panel_header_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Content Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_content_color', __('models.themes.fields.panel_content_color') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('panel_content_color', isset($theme) ? $theme->panel_content_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Panel Btn Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('panel_btn_color', __('models.themes.fields.panel_btn_color') . ':', ['class' => 'm-auto'])
            !!}
            {!! Form::color('panel_btn_color', isset($theme) ? $theme->panel_btn_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

    </div>
    <div class="col-6">
        <div class="form-group">
            <h2 class="text-center">@lang('models.settings.fields.mobile')</h2>
        </div>

        <!-- Mobile Body Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_body_background', __('models.themes.fields.mobile_body_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_body_background', isset($theme) ? $theme->mobile_body_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Aside Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_aside_background', __('models.themes.fields.mobile_aside_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_aside_background', isset($theme) ? $theme->mobile_aside_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Aside Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_aside_color', __('models.themes.fields.mobile_aside_color') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_aside_color', isset($theme) ? $theme->mobile_aside_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Btn Background Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_btn_background', __('models.themes.fields.mobile_btn_background') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_btn_background', isset($theme) ? $theme->mobile_btn_background ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Header Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_header_color', __('models.themes.fields.mobile_header_color') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_header_color', isset($theme) ? $theme->mobile_header_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Content Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_content_color', __('models.themes.fields.mobile_content_color') . ':', [
            'class' => 'm-auto',
            ]) !!}
            {!! Form::color('mobile_content_color', isset($theme) ? $theme->mobile_content_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

        <!-- Mobile Btn Color Field -->
        <div class="form-group border border-2 rounded d-flex p-2">
            {!! Form::label('mobile_btn_color', __('models.themes.fields.mobile_btn_color') . ':', ['class' =>
            'm-auto']) !!}
            {!! Form::color('mobile_btn_color', isset($theme) ? $theme->mobile_btn_color ?? '' : '', [
            'class' => 'form-control',
            'style' => 'width:50px',
            ]) !!}
        </div>

    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('themes.index') }}" class="btn btn-default">@lang('crud.cancel')</a>
</div>
