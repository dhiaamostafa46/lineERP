<div class="card">

    <div class="card-body">

        <div class="row">
            <!-- Employee Id Field -->
            <div class="form-group col-md-6 col-sm-12 mb-3">
                <label for="employee_id">
                    @lang('hr::models/hr_rewards.fields.employee_id')
                </label>
                <select wire:model="employee_id" id="employee_id" class="form-control">
                    <option value="" selected readonly>
                        @lang('hr::lang.select_employee')
                    </option>
                    @foreach ($employees as $key => $value)
                        <option value="{{ $key }}" @if ($status == $key) selected @endif>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Type Field -->
            <div class="form-group col-md-6 col-sm-12 mb-3">
                <label for="type">
                    @lang('hr::models/hr_rewards.fields.type')
                </label>
                <select wire:model.live="type" id="type" class="form-control">
                    <option value="" selected readonly>
                        @lang('hr::lang.select_type')
                    </option>
                    @foreach ($types as $key => $value)
                        <option value="{{ $key }}" @if ($status == $key) selected @endif>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            @if ($type == 2)
                <!-- Amount Field -->
                <div class="form-group col-md-12 col-sm-12 mb-3">
                    <label for="amount">
                        @lang('hr::models/hr_rewards.fields.amount')
                    </label>
                    <input type="number" class="form-control" id="amount" wire:model="amount">
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


              
                <div class="form-group col-md-4 col-sm-12 mb-3">
                    <label for="start_at">
                        @lang('hr::models/hr_penalties.fields.due_date')
                    </label>
                    <input type="date" class="form-control" id="due_date" wire:model="due_date">
                    @error('due_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
            @if ($type == 1)
                <!-- Over Time Field -->
                <div class="form-group col-md-12 col-sm-12 mb-3">
                    <label for="over_time">
                        @lang('hr::models/hr_rewards.fields.over_time')
                    </label>
                    <input type="number" class="form-control" id="over_time" wire:model="over_time">
                    @error('over_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            @if ($type == 3)
                <div class="form-group col-md-4 col-sm-12 mb-3">
                    <label for="days_off">
                        @lang('hr::models/hr_rewards.fields.days_off')
                    </label>
                    <input type="number" class="form-control" id="days_off" wire:model="days_off">
                    @error('days_off')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-4 col-sm-12 mb-3">
                    <label for="start_at">
                        @lang('hr::models/hr_rewards.fields.start_at')
                    </label>
                    <input type="date" class="form-control" id="start_at" wire:model="start_at">
                    @error('start_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-4 col-sm-12 mb-3">
                    <label for="end_at">
                        @lang('hr::models/hr_rewards.fields.end_at')
                    </label>
                    <input type="date" class="form-control" id="end_at" wire:model="end_at">
                    @error('end_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
            @if ($type == 4)
                <div class="form-group col-md-12 col-sm-12 mb-3">
                    <label for="note">
                        @lang('hr::models/hr_rewards.fields.note')
                    </label>
                    <textarea wire:model="note" id="" cols="30" rows="3" class="form-control"></textarea>
                    @error('note')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

    </div>

    <div class="card-footer py-4 text-end">
        <a href="{{ route('hr.rewards.index') }}" class="btn btn-sm btn-secondary">
            @lang('crud.cancel')
        </a>
        @if ($reward)
            <button type="button" class="btn btn-sm btn-primary" wire:click="updateChanges()">
                @lang('crud.save')
                <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="updateChanges">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        @else
            <button type="button" class="btn btn-sm btn-primary" wire:click="saveChanges()">
                @lang('crud.save')
                <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="saveChanges">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        @endif
    </div>
</div>
