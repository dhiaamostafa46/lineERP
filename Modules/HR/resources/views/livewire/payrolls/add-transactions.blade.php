<div>
    @if ($canBeAdd && $showAddButton)
   
    <button class="btn btn-sm btn-light-primary" wire:click="addTransaction" type="button">
        <i class="fa-solid fa-plus"></i>
        @lang('hr::models/hr_payrolls.singular')
        <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="addTransaction">
            <span class="visually-hidden">Loading...</span>
        </div>
    </button>
    @endif
</div>
