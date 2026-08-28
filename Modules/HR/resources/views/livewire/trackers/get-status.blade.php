<div>




    @switch($step_status)
        @case('do_not_have_track_pending')
            <span class="{{ $model->status_badge }}">
                <!--If there is no trake for employee requests and request pending-->
                {{ $model->status_text }}
            </span>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-danger" wire:click="rejectModal()">
                    @lang('hr::models/hr_trackers.fields.reject')
                </button>
                <button type="button" class="btn btn-sm btn-primary" wire:click="approveModal()">
                    @lang('hr::models/hr_trackers.fields.approve')
                </button>
            </div>
        @break

        @case('do_not_have_track_finished')
            <span class="{{ $model->status_badge }}">
                <!--If there is no trake for employee requests and request finished-->
                {{ $model->status_text }}
            </span>
        @break

        @case('has_track_finished')
            <button type="button" class="{{ $model->status_badge }} my-2 me-5 border-0" data-bs-toggle="popover"
                data-bs-placement="top" title="{{ $current_step }} - {{ $steps }} @lang('hr::models/hr_trackers.fields.steps')"
                data-bs-content="{{ $current_name }}">
                {{ $model->status_text }}
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" wire:click="toggleOpenModal()">
                <i class="fa-solid fa-eye"></i>
            </button>
            @if ($openModal)
                <div class="modal fade show" tabindex="-1" id="kt_modal_1" style="display: block; background: #18181b6b"
                    aria-modal="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">
                                    {{ $model->employee->username }}
                                </h3>
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" wire:click="toggleOpenModal()">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <!--end::Close-->
                            </div>

                            <div class="modal-body">

                                @foreach ($approvals ?? [] as $approval)
                                    <div class="py-5 my-3 bg-white" wire:key="approval_{{ $loop->index }}">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center mb-5">
                                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                        <div class="symbol-label">
                                                            <img src="{{ $approval->user->photo_original_path }}"
                                                                alt="Emma Smith" class="w-100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <h4 class="text-gray-800 text-hover-primary mb-1">
                                                            {{ $approval->user->name }}
                                                        </h4>
                                                        <span
                                                            class="text-muted">{{ $approval->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <span class="{{ $approval->status_badge }} text-end">
                                                    {{ $approval->status_text }}
                                                </span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted">{{ $approval->note }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <hr class="my-5">
                                    @endif
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" wire:click="toggleOpenModal()">
                                    @lang('crud.close')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @break

        @case('has_track_pending')
            <button type="button" class="btn btn-sm btn-primary " wire:click="toggleOpenModal()" wire:poll.15s>
                @lang('hr::models/hr_trackers.fields.apptovals')
            </button>
            @if ($openModal)

                <div class="modal fade show" tabindex="-1" id="kt_modal_1" style="display: block; background: #18181b6b"
                    aria-modal="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">
                                    {{ $model->employee->username }} {{ $current_user_id }}
                                </h3>
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" wire:click="toggleOpenModal()">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <!--end::Close-->
                            </div>

                            <div class="modal-body">
                                @foreach ($approvals ?? [] as $approval)
                                    <div class="py-5 my-3 bg-white" wire:key="approval_{{ $loop->index }}">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center mb-5">
                                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                        <div class="symbol-label">
                                                            {{-- @dd($approval->user->photo_original_path) --}}
                                                            <img src="{{ $approval->user->photo_original_path }}"
                                                                alt="Emma Smith" class="w-100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <h4 class="text-gray-800 text-hover-primary mb-1">
                                                            {{ $approval->user->name }}
                                                        </h4>
                                                        <span
                                                            class="text-muted">{{ $approval->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <span class="{{ $approval->status_badge }} text-end">
                                                    {{ $approval->status_text }}
                                                </span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted">{{ $approval->note }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <hr class="my-5">
                                    @endif
                                @endforeach


                                @if ($current_user_id == $user_id)
                                    <hr class="my-5">
                                    <textarea wire:model="note" id="" cols="30" rows="3" class="form-control"></textarea>
                                    @error('note')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" wire:click="toggleOpenModal()">
                                    @lang('hr::models/hr_payroll_approvals.fields.close')</button>
                                @if ($current_user_id == $user_id)
                                    @if ($current_step > 2)
                                        <button class="btn btn-sm btn-bg-light btn-active-color-danger"
                                            wire:click="restart({{ $current_id }})">
                                            @lang('hr::models/hr_payroll_approvals.fields.restart')
                                            <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                                wire:target="restart">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </button>
                                    @endif
                                    @if ($current_step > 1)
                                        <button class="btn btn-sm btn-bg-light btn-active-color-danger"
                                            wire:click="backStep({{ $current_id }})">
                                            @lang('hr::models/hr_payroll_approvals.fields.back_step')
                                            <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                                wire:target="back_step">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-bg-light btn-active-color-danger"
                                        wire:click="createRejected({{ $current_id }})">
                                        <i class="fa-solid fa-x"></i>
                                        @lang('hr::models/hr_payroll_approvals.fields.reject')
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="createRejected">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                    <button class="btn btn-sm btn-bg-light btn-active-color-primary"
                                        wire:click="createApproved({{ $current_id }})">
                                        <i class="fa-solid fa-check"></i>
                                        @lang('hr::models/hr_payroll_approvals.fields.approve')
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="createApproved">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @break

        @default
            {{-- <button type="button" class="badge bg-info text-white my-2 me-5" data-bs-toggle="popover"
        data-bs-placement="top" title="{{ $current_step }} - {{ $steps }} @lang('hr::models/hr_trackers.fields.steps')"
        data-bs-content="{{ $current_name }}">
        @lang('hr::models/hr_trackers.fields.in_progress')
    </button> --}}
    @endswitch
</div>
