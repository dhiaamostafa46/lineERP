<div>
   
    @if ($available)
        @if ($approved)
            <span class="{{ $approval->status_badge }}">{{ $approval->status_text }}</span>
        @else
            @error('note')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <textarea wire:model="note" id="" cols="30" rows="3" class="form-control" placeholder="@lang('hr::models/hr_payroll_approvals.fields.note')"></textarea>
            @if ($approval->sort > 2)
                <button class="btn btn-sm btn-bg-light btn-active-color-danger" wire:click="restart({{ $approval->id }})">
                    @lang('hr::models/hr_payroll_approvals.fields.restart')
                    <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="restart">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            @endif
            @if ($approval->sort > 1)
                <button class="btn btn-sm btn-bg-light btn-active-color-danger"
                    wire:click="back_step({{ $approval->id }},{{ $approval->sort - 1 }})">
                    @lang('hr::models/hr_payroll_approvals.fields.back_step')
                    <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="back_step">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            @endif
            {{-- <button class="btn btn-sm btn-bg-light btn-active-color-danger" wire:click="create_rejected({{ $approval->id }})">
        <i class="fa-solid fa-x"></i>
        @lang('hr::models/hr_payroll_approvals.fields.reject')
        <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="create_rejected">
            <span class="visually-hidden">Loading...</span>
        </div>
    </button> --}}
            <button class="btn btn-sm btn-bg-light btn-active-color-primary"
                wire:click="create_approved({{ $approval->id }})">
                <i class="fa-solid fa-check"></i>
                @lang('hr::models/hr_payroll_approvals.fields.approve')
                <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="create_approved">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        @endif
    @endif
</div>
