@if (isset($posts) && $posts->isNotEmpty())
    <div class="card shadow-sm mb-7">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title">
                <span class="card-label fw-bold text-gray-800 fs-3">@lang('hr::models/hr_posts.feed_title')</span>
            </h3>
        </div>
        <div class="card-body pt-2">
            <div class="d-flex flex-column gap-5">
                @foreach ($posts as $post)
                    <div class="border border-gray-200 border-dashed rounded p-5 {{ $post->is_pinned ? 'bg-light-warning' : '' }}"
                        wire:key="hr-post-{{ $post->id }}">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="{{ $post->type_badge }}">{{ $post->type_text }}</span>
                                @if ($post->is_pinned)
                                    <span class="badge badge-light-warning">@lang('hr::models/hr_posts.pinned')</span>
                                @endif
                                <h4 class="fs-5 fw-bold text-gray-900 mb-0">{{ $post->title }}</h4>
                            </div>
                            <span class="text-muted fs-7">
                                {{ $post->published_at?->format('Y-m-d H:i') ?? $post->created_at?->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        @if ($post->image_url)
                            <div class="mb-4">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="rounded w-100" style="max-height: 280px; object-fit: cover;">
                            </div>
                        @endif

                        @if (strlen(strip_tags($post->body ?? '')) > 220)
                            <div class="post-body text-gray-700 fs-6">
                                {!! \Illuminate\Support\Str::limit(strip_tags($post->body), 220) !!}
                            </div>
                            <details class="mt-3">
                                <summary class="text-primary fw-semibold cursor-pointer">@lang('hr::models/hr_posts.read_more')</summary>
                                <div class="mt-3 border-top pt-3 post-body-full">
                                    {!! $post->body !!}
                                </div>
                            </details>
                        @else
                            <div class="post-body text-gray-700 fs-6">
                                {!! $post->body !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
