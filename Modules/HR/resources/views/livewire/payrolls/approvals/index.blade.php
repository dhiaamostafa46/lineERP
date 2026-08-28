<div class="col-12">
    @if ($approvals_is_ready)
        {{-- @dd($approvals) --}}
        @foreach ($approvals as $approval)
            <div class="col-12 p-5 my-3 bg-white drag-item" wire:key="approval_{{ $loop->index }}">
                <div class="row">

                    <div class="col-1">
                        <div class="symbol symbol-65px symbol-circle">
                            <img src="{{ $approval->user->photo_original_path }}" alt="image">
                        </div>
                    </div>
                    <div class="col-3 my-auto">
                        <h4>{{ $approval->user->name }}</h4>
                    </div>
                    <div class="col-5 my-auto">
                        <h4>{{ $approval->note }}</h4>
                    </div>
                    <div class="col-2 my-auto">
                        <span class="{{ $approval->status_badge }}">
                            {{ $approval->status_text }}
                        </span>
                    </div>
                    {{-- <div class="col-1 my-auto text-end">
                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger p-2"
                    wire:click="delete({{ $approval->id }})">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div> --}}
                </div>
            </div>
        @endforeach
    @else
        <p class="alert alert-danger">يرجى ضبط الموافقات من خلال إعدادات الموارد البشرية لتتمكن من الموافقة والإعتماد
        </p>
        {{-- @if ($users)
sas
    <div class="card">
        <div class="card-body row">
            <div class="col-8 d-flex">
                <select class="form-select" data-control="select2" wire:model.live="user_id">
                    <option value="" selected readonly>
                        {{ __('hr::lang.select_user') }}
                    </option>
                    @forelse ($users as $id => $user_name)
                    <option value="{{ $id }}" @if ($id == $user_id) selected @endif wire:key="user-approvals-{{ $id }}">
                        {{ $user_name }}
                    </option>
                    @empty
                    @endforelse
                </select>
                <button class="btn btn-primary ml-3" wire:click="create">
                    @lang('crud.add')
                </button>
            </div>
            <div class="col-4 text-end">
                <button class="btn btn-primary float-right" wire:click="approvalsReady">
                    @lang('hr::models/hr_payrolls.approvals_is_ready')
                </button>
            </div>
        </div>
    </div>
    @endif --}}
        <div class="row my-5">
            <div wire:sortable="updateApprovalOrder">
                @foreach ($approvals as $approval)
                    <div class="col-12 p-5 my-3 bg-white drag-item" wire:sortable.item="{{ $approval->id }}"
                        wire:key="approval_{{ $loop->index }}">
                        <div class="row">
                            <div class="col-2">
                                <i class="fa-solid fa-arrows-up-down me-5 fs-2" wire:sortable.handle></i>
                                <div class="symbol symbol-65px symbol-circle">
                                    <img src="{{ $approval->user->photo_original_path }}" alt="image">
                                </div>
                            </div>
                            <div class="col-2 my-auto">
                                <h4>{{ $approval->user->name }}</h4>
                            </div>
                            <div class="col-5 my-auto">
                                <h4>{{ $approval->note }}</h4>
                            </div>
                            <div class="col-2 my-auto">
                                <span class="{{ $approval->status_badge }}">
                                    {{ $approval->status_text }}
                                </span>
                            </div>
                            <div class="col-1 my-auto text-end">
                                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger p-2"
                                    wire:click="delete({{ $approval->id }})">
                                    <i class="fa-solid fa-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
