<div class="documents-page">

    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-8">
        <div>
            <h1 class="page-title fw-bold text-gray-800 mb-2">@lang('hr::models/hr_documents.plural')</h1>

        </div>
        {{-- Add Upload button if needed --}}
        {{-- <a href="#" class="btn btn-primary btn-lg d-flex align-items-center gap-2 shadow-sm"> --}}
        {{-- <i class="ki-duotone ki-plus fs-2"></i> --}}
        {{-- @lang('crud.create') --}}
        {{-- </a> --}}
    </div>

    @if ($dataInf->isEmpty())
        <!-- Empty State -->
        <div class="card card-flush">
            <div class="card-body text-center p-lg-20">
                 <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">
            </div>
        </div>
    @else
        <!-- Documents Grid -->
        <div class="row g-6 g-xl-9">
            @foreach ($dataInf as $item)
                @php
                    $ext = strtolower(pathinfo($item->file, PATHINFO_EXTENSION));
                    $icon_map = [
                        'pdf' => 'pdf', 'doc' => 'doc', 'docx' => 'doc',
                        'jpg' => 'blank-image', 'jpeg' => 'blank-image', 'png' => 'blank-image', 'gif' => 'blank-image',
                        'ai' => 'ai',
                        'css' => 'css',
                        'sql' => 'sql',
                        'tif' => 'tif',
                        'xml' => 'xml',
                    ];
                    $icon = $icon_map[$ext] ?? 'doc'; // default icon
                @endphp
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 card-flush document-card">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <a href="{{ $item->file_original_path }}" target="_blank" class="text-gray-800 text-hover-primary d-flex flex-column">
                                <div class="symbol symbol-60px mb-5 mx-auto">
                                    <img src="{{ asset('admin_assets/media/svg/files/' . $icon . '.svg') }}" class="theme-light-show" alt="">
                                    <img src="{{ asset('admin_assets/media/svg/files/' . $icon . '-dark.svg') }}" class="theme-dark-show" alt="">
                                </div>
                                <div class="fs-5 fw-bold mb-2 text-truncate">{{ $item->type->name ?? 'Document' }}</div>
                            </a>
                            <div class="fs-7 fw-semibold text-gray-500">{{ $item->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-8">
            {{ $dataInf->links() }}
        </div>
    @endif

    <style>
        .document-card {
            transition: all .3s ease;
            border: 1px solid var(--bs-card-border-color);
        }

        .document-card:hover {
            transform: translateY(-5px);
            border-color: var(--bs-primary);
            box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb), 0.15);
        }
    </style>
</div>
