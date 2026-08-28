<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('department_id', __('hr::models/hr_trackers.fields.department_id') . ':') !!}
        <select name="department_id" wire:model.live="department_id" id="department_id" class="form-select">
            <option value="" selected readonly>Select Department</option>
            @foreach ($departments as $item_id => $item_name)
            <option value="{{ $item_id }}">{{ $item_name }}</option>
            @endforeach
        </select>
    </div>

    @if (!empty($department_jobs))
    <div class="form-group col-sm-12 mb-5">
        <h4>@lang('hr::models/hr_trackers.fields.jobs')</h4>
        @forelse ($department_jobs as $job_id => $job_name)
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="{{ $job_id }}" id="flexCheckDefault{{ $job_id }}"
                wire:model="jobs[{{ $job_id }}]" name="jobs[{{ $job_id }}]" {{ in_array($job_id, $tracked_jobs)
                ? 'checked' : '' }} />
            <label class="form-check-label" for="flexCheckDefault{{ $job_id }}">
                {{ $job_name }}
            </label>
        </div>
        @empty

        @endforelse
    </div>
    @endif

</div>
