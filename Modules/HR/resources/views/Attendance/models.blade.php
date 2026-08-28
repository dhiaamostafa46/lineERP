<div class="modal fade" tabindex="-1" id="CreatePenalties">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"> @lang('hr::models/hr_penalties.plural') </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form for the modal -->
            {!! Form::open(['route' => 'hr.attendance.penalties', 'method' => 'POST']) !!}

            <div class="modal-body">

                <input type="hidden" id="employee_id" name="employee_id">
                <input type="hidden" id="date" name="date">
                <input type="hidden" id="timetrack" name="timetrack">
                <input type="hidden" id="status" name="status" value="2">

                <!-- Amount Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('amount', __('hr::models/hr_penalties.fields.amount') . ':') !!}
                    {!! Form::number('amount', null, [
                        'class' => 'form-control',
                        'maxlength' => 8,
                        'max' => 99999999,
                        'step' => '0.01',
                        'required' => true
                    ]) !!}
                </div>

                <!-- Due Date Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('due_date', __('hr::models/hr_penalties.fields.due_date') . ':') !!}
                    {!! Form::date('due_date', \Carbon\Carbon::now()->format('Y-m-d'), [
                        'class' => 'form-control',
                        'required' => true
                    ]) !!}
                </div>

                <!-- Description Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('description', __('hr::models/hr_penalties.fields.description') . ':') !!}
                    {!! Form::textarea('description', null, [
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'سبب الجزاء...'
                    ]) !!}
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                    إلغاء
                </button>
                {!! Form::submit('حفظ الجزاء', ['class' => 'btn btn-primary']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
