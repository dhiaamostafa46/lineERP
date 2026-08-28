<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-posts-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_posts.fields.id')</th>
                    <th>@lang('hr::models/hr_posts.fields.title')</th>
                    <th>@lang('hr::models/hr_posts.fields.type')</th>
                    <th>@lang('hr::models/hr_posts.fields.flage')</th>
                    <th>@lang('hr::models/hr_posts.fields.published_at')</th>
                    <th>@lang('hr::models/hr_posts.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        @if ($post->is_pinned)
                            <span class="badge badge-light-warning me-1">@lang('hr::models/hr_posts.pinned')</span>
                        @endif
                        {{ $post->title }}
                    </td>
                    <td><span class="{{ $post->type_badge }}">{{ $post->type_text }}</span></td>
                    <td>{{ $post->flag_text }}</td>
                    <td>{{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '' }}</td>
                    <td><span class="{{ $post->status_badge }}">{{ $post->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.posts.destroy', $post->id], 'method' => 'delete']) !!}
                        <div class="btn-group">
                            @can('hr.posts.show')
                            <a href="{{ route('hr.posts.show', [$post->id]) }}" class="btn btn-icon btn-sm btn-light-success btn-xs">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('hr.posts.edit')
                            <a href="{{ route('hr.posts.edit', [$post->id]) }}" class="btn btn-icon btn-sm btn-light-primary btn-xs">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.posts.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                'type' => 'submit',
                                'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endcan
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $posts->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $posts])
        </div>
    </div>
</div>
