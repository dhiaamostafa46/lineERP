<div wire:key="create-modal-{{ $department_id }}">

    
    <button type="button" class="btn btn-sm btn-primary" wire:click="toggleOpenModal()">
        @lang('hr::models/hr_trackers.plural')
    </button>
    @if ($openModal)
    <div class="modal bg-body fade show" tabindex="-1" id="kt_modal_1" aria-modal="true" role="dialog"
        style="display: block;">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content shadow-none">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @lang('hr::models/hr_trackers.plural') : {{ $department->name }}
                    </h5>
                </div>

                <div class="modal-body">
                    <div class="container">
                        <div class="accordion mb-5" id="accordionPanelsStayOpenExample">
                            <div class="accordion-item my-3" style="-webkit-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
                                                                -moz-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
                                                                box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#panelsStayOpen-collapse0" aria-expanded="false"
                                        aria-controls="panelsStayOpen-collapse0">
                                        <i class="fa-solid fa-plus me-3"></i>
                                        <b>@lang('crud.add_new')</b>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapse0" class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                        <div class="form-group row mb-3">
                                            <div class="col-lg-4 col-md-6 col-sm-12 my-auto">
                                                <label for="name">
                                                    @lang('hr::models/hr_trackers.fields.name')
                                                </label>
                                            </div>
                                            <div class="col-lg-8 col-md-6 col-sm-12">
                                                <input type="text" class="form-control" wire:model='name'>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-lg-4 col-md-6 col-sm-12 my-auto">
                                                <label for="status">
                                                    @lang('hr::models/hr_trackers.fields.status')
                                                </label>
                                            </div>
                                            <div class="col-lg-8 col-md-6 col-sm-12">
                                                <select wire:model="status" id="status" class="form-control">
                                                    <option value="" selected readonly>
                                                        @lang('hr::lang.select_status')
                                                    </option>
                                                    @foreach ($statuses as $key => $value)
                                                    <option value="{{ $key }}" @if($status==$key) selected @endif>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-lg-4 col-md-6 col-sm-12 my-auto">
                                                <label for="type">
                                                    @lang('hr::models/hr_trackers.fields.type')
                                                </label>
                                            </div>
                                            <div class="col-lg-8 col-md-6 col-sm-12">
                                                <select wire:model="type" id="type" class="form-control">
                                                    <option value="" selected readonly>
                                                        @lang('hr::lang.select_type')
                                                    </option>
                                                    @foreach ($types as $key => $value)
                                                    <option value="{{ $key }}" @if($type==$key) selected @endif>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div>
                                                    @forelse ($tracker_approvals??[] as $index => $item)
                                                    <div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">
                                                                <label for="user_id">
                                                                    @lang('hr::lang.select_user')
                                                                </label>
                                                                <select
                                                                    wire:model.live="tracker_approvals.{{$index}}.user_id"
                                                                    id="user_id" class="form-control">
                                                                    <option value="" selected readonly>
                                                                        @lang('hr::lang.select_user')
                                                                    </option>
                                                                    @foreach ($users as $key => $value)
                                                                    <option value="{{ $key }}"
                                                                        @if($item['user_id']==$key) selected @endif>
                                                                        {{ $value }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('tracker_approvals.'.$index.'.user_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="user_id">
                                                                    @lang('hr::models/hr_settings.fields.sort')
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    wire:model.live="tracker_approvals[{{$index}}][sort]"
                                                                    value="{{$item['sort']??$loop->iteration}}">
                                                                @error('tracker_approvals.'.$index.'.sort')
                                                                <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-4">
                                                                <a href="javascript:;"
                                                                    wire:click="removeApproval({{$index}})"
                                                                    class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                                                    <i class="ki-duotone ki-trash fs-5">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                        <span class="path4"></span>
                                                                        <span class="path5"></span>
                                                                    </i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @empty
                                                    @endforelse
                                                </div>
                                                <!--end::Form group-->

                                                <!--begin::Form group-->
                                                <div class="form-group mt-5">
                                                    <a href="javascript:;" wire:click="addApproval"
                                                        class="btn btn-light-primary">
                                                        <i class="ki-duotone ki-plus fs-3"></i>
                                                    </a>
                                                </div>
                                                <!--end::Form group-->
                                            </div>
                                            <!--end::Repeater-->

                                        </div>
                                    </div>
                                </div>
                            </div>

                            @forelse ($trackers as $tracker)
                            <div class="accordion-item my-3" style="-webkit-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
                                        -moz-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
                                        box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#panelsStayOpen-collapse{{ $tracker->id }}"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapse{{ $tracker->id }}">
                                        {{ $tracker->name }} - {{ $tracker->created_at->diffForHumans() }}
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapse{{ $tracker->id }}" class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                    </div>
                                </div>
                            </div>
                            @empty

                            @endforelse
                        </div>
                        {{ $trackers->onEachSide(2)->links('vendor/livewire/bootstrap') }}
                    </div>
                </div>

                <div class="modal-footer">
                    {{-- @dump($errors->all()) --}}
                    <button type="button" class="btn btn-primary" wire:click="create()">@lang('crud.save')</button>
                    <button type="button" class="btn btn-light" wire:click="toggleOpenModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
