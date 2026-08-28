<div class="custody-page">
    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-8">
        <div>
            <h1 class="page-title fw-bold text-gray-800 mb-2">@lang('hr::models/hr_custodies.plural')</h1>

        </div>
    </div>

    @if ($dataInf->isEmpty())
        <!-- Empty State -->
        <div class="card card-flush">
            <div class="card-body text-center p-lg-20">
               <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">

            </div>
        </div>
    @else
        <!-- Custody Table -->
        <div class="card card-flush">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-200px">@lang('hr::models/hr_custodies.fields.item_name')</th>
                                <th class="min-w-150px">@lang('hr::models/hr_custodies.fields.delivery_date')</th>
                                <th class="min-w-100px text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataInf as $item)
                                <tr>
                                    <td>
                                        <span class="text-gray-800 fw-bolder">{{ $item->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-normal text-muted">{{ $item->created_at->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="toggleOpenModal({{ $item->id }})" class="btn btn-sm btn-light-primary">
                                            @lang('crud.view')
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-8">
            {{ $dataInf->links() }}
        </div>
    @endif

    {{-- You might have a modal here that is toggled by $openModal --}}
    @if($openModal)
        {{-- Modal content for viewing a custody item ($model) --}}
    @endif
</div>
