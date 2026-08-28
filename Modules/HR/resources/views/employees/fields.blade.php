<style>
    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }
</style>





{{-- <div class="card">



    <div class="card-body">

        <div class="row">
            @include('hr::employees.fields')
        </div>

    </div>

    <div class="card-footer py-4 text-end">
        <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-secondary">

            @lang('crud.cancel')
        </a>
        {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary']) !!}
    </div>


</div> --}}






<div id="multiStepForm">
    @include('employees.fields', ['employee' => @optional($employee)->main_employee])
    <div class="form-step ">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('hr::employees.model')
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
    <div class="form-step ">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('hr::employees.salary')
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

    <!-- Step 3 -->
    <div class="form-step">


        <div class="card">
            <div class="card-body">
                <h2 class="text-primary text-center mb-5">@lang('hr::models/hr_employees.system_login_data')</h2>
                <div class="row">
                    @include('hr::employees.user_fields', ['user' => @optional($employee)->user])
                </div>
            </div>
            <div class="card-footer py-4 text-end">
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>

                <button type="button" class="prev-step btn btn-sm btn-secondary"> @lang('crud.Previous')</button>
                {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
            </div>
        </div>
    </div>
</div>






























@section('scripts')
    <script>
        $(document).ready(function() {
            // عندما يتغير حقل username
            $('#username').on('input', function() {
                // جلب القيمة المدخلة
                var usernameValue = $(this).val();

                // تعيين القيمة إلى حقل user[name]
                $('input[name="user[name]"]').val(usernameValue);
            });

            $('#email').on('input', function() {
                // جلب القيمة المدخلة
                var usernameValue = $(this).val();

                // تعيين القيمة إلى حقل user[name]
                $('input[name="user[email]"]').val(usernameValue);
            });


        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const steps = document.querySelectorAll(".form-step");
            let currentStep = 0;

            function showStep(step) {
                steps.forEach((stepElement, index) => {
                    stepElement.classList.toggle("active", index === step);
                });
            }

            document.querySelectorAll(".next-step").forEach((button) => {
                button.addEventListener("click", () => {
                    currentStep = Math.min(currentStep + 1, steps.length - 1);
                    showStep(currentStep);
                });
            });

            document.querySelectorAll(".prev-step").forEach((button) => {
                button.addEventListener("click", () => {
                    currentStep = Math.max(currentStep - 1, 0);
                    showStep(currentStep);
                });
            });

            showStep(currentStep);
        });
    </script>
@endsection
