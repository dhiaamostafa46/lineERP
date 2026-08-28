<div class="row">
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.title'):</strong>
        <div>{{ $post->title }}</div>
    </div>
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.type'):</strong>
        <div><span class="{{ $post->type_badge }}">{{ $post->type_text }}</span></div>
    </div>
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.status'):</strong>
        <div><span class="{{ $post->status_badge }}">{{ $post->status_text }}</span></div>
    </div>
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.flage'):</strong>
        <div>{{ $post->flag_text }}</div>
    </div>
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.published_at'):</strong>
        <div>{{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '-' }}</div>
    </div>
    <div class="col-sm-6 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.expires_at'):</strong>
        <div>{{ $post->expires_at ? $post->expires_at->format('Y-m-d H:i') : '-' }}</div>
    </div>
    @if ($post->image_url)
        <div class="col-sm-12 mb-4">
            <strong>@lang('hr::models/hr_posts.fields.image'):</strong>
            <div class="mt-2">
                <img src="{{ $post->image_url }}" alt="" class="rounded border" style="max-height: 240px;">
            </div>
        </div>
    @endif
    <div class="col-sm-12 mb-4">
        <strong>@lang('hr::models/hr_posts.fields.body'):</strong>
        <div class="mt-2 border rounded p-4 bg-light">{!! $post->body !!}</div>
    </div>
</div>
