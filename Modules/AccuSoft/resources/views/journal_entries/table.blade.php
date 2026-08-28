<div class="card-body p-0">
    <div class="table-responsive">

        @php
            $sortBy = request('sort_by');
            $sortOrder = request('sort_order', 'desc');
            $buildSortUrl = function($column) use ($sortBy, $sortOrder) {
                $order = ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_order' => $order]);
            };
            $getSortIcon = function($column) use ($sortBy, $sortOrder) {
                if ($sortBy !== $column) return '<i class="fas fa-sort text-muted ms-1" style="font-size: 10px;"></i>';
                return $sortOrder === 'asc' 
                    ? '<i class="fas fa-sort-up text-primary ms-1"></i>' 
                    : '<i class="fas fa-sort-down text-primary ms-1"></i>';
            };
            $isPendingView = $isPendingView ?? request()->routeIs('accusoft.JournalEntry.pending');
        @endphp
        <table class="table table-striped text-center gy-7 gs-7" id="AS-journalEntries-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    @if ($isPendingView)
                        <th class="w-10px pe-2 text-center">
                            <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                <input class="form-check-input" type="checkbox" id="check-all-header" title="@lang('accusoft::lang.select_all')" />
                            </div>
                        </th>
                    @endif
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('entry_number') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.entry_number') {!! $getSortIcon('entry_number') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('entry_date') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.entry_date') {!! $getSortIcon('entry_date') !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ $buildSortUrl('description') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.description') {!! $getSortIcon('description') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('created_by') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.created_by') {!! $getSortIcon('created_by') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('entry_type') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.entry_type') {!! $getSortIcon('entry_type') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('source') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.source') {!! $getSortIcon('source') !!}
                        </a>
                    </th>
                    <th class="text-end">
                        <a href="{{ $buildSortUrl('total_debit') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.total_debit') {!! $getSortIcon('total_debit') !!}
                        </a>
                    </th>
                    <th class="text-end">
                        <a href="{{ $buildSortUrl('total_credit') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.total_credit') {!! $getSortIcon('total_credit') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('status') }}" class="text-gray-800 text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.fields.status') {!! $getSortIcon('status') !!}
                        </a>
                    </th>
                    <th class="text-center table-action">@lang('crud.action')</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // جلب الإعدادات مرة واحدة خارج الحلقة لتحسين الأداء
                    $acc_settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();
                    $pwdEnabled = $acc_settings->lock_period_pwd_enabled ?? false;
                @endphp
                @foreach ($journalEntries as $entry)
                    <tr>
                        @if ($isPendingView)
                            <td class="text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                    <input class="form-check-input pending-entry-checkbox" type="checkbox" name="entry_ids[]" value="{{ $entry->id }}" />
                                </div>
                            </td>
                        @endif
                        <td class="text-center">
                            <a href="{{ route('accusoft.JournalEntry.show', $entry->id) }}"
                                class="text-primary fw-bold">{{ $entry->entry_number }}</a>
                        </td>
                        <td class="text-center">
                            {{ $entry->entry_date ? \Carbon\Carbon::parse($entry->entry_date)->format('Y/m/d') : '—' }}
                        </td>
                        <td>{{ Str::limit($entry->description ?? $entry->notes, 40) }}</td>
                        <td class="text-center">
                            <span class="badge badge-light-primary fw-semibold">{{ $entry->creator->name ?? '—' }}</span>
                        </td>
                        <td class="text-center">{{ $entry->type_text }}</td>
                        <td class="text-center">
                            <span class="badge {{ $entry->source_badge_class }} fw-semibold fs-7">{{ $entry->source_text }}</span>
                        </td>
                        <td class="text-end text-success fw-bold"> {{ number_format($entry->total_debit ?? 0, 2) }}
                        </td>
                        <td class="text-end text-danger fw-bold">{{ number_format($entry->total_credit ?? 0, 2) }} </td>
                        <td class="text-center">
                            @if ($entry->is_locked)
                                <span class="badge badge-light-warning fs-base">@lang('lang.locked')</span>
                            @else
                                {{ $entry->status_text }}
                            @endif
                        </td>






                        <td style="width: 120px" class="table-action">

                            <div class='btn-group'>
                                <a href="{{ route('accusoft.JournalEntry.show', [$entry->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>




                                @php
                                    $isPending = $entry->status == \App\Models\AccuSoft\JournalEntry::STATUS_PENDING;
                                    $isAuto = $entry->entry_type == \App\Models\AccuSoft\JournalEntry::ENTRY_TYPE_AUTO;
                                    $isOpenYear = \App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($entry->entry_date);
                                    $isLocked = $entry->is_locked || !$isOpenYear;
                                    // Pending entries are always editable (regardless of auto/manual)
                                    $canEdit = $isPending || !$isAuto;
                                @endphp

                                @if ($canEdit)
                                    @if (!$isLocked)
                                        <a href="{{ route('accusoft.JournalEntry.edit', [$entry->id]) }}"
                                            class='btn btn-sm btn-primary mx-1' title="@lang('lang.edit')">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                    @elseif ($isLocked && $pwdEnabled)
                                        <a href="javascript:void(0)"
                                            onclick="promptLockPassword('{{ route('accusoft.JournalEntry.edit', [$entry->id]) }}', '{{ route('accusoft.JournalEntry.verifyLockPassword', [$entry->id]) }}')"
                                            class='btn btn-sm btn-primary mx-1'
                                            title="@lang('accusoft::models/as_accounting_settings.fields.lock_period_pwd')">
                                            <i class="fa-solid fa-lock"></i>
                                        </a>
                                    @endif
                                @endif

                                @if ($isPending)
                                    @can('accusoft.JournalEntry.post')
                                        <form method="POST" action="{{ route('accusoft.JournalEntry.post', $entry->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary mx-1"
                                                    title="@lang('accusoft::lang.post_entry')">
                                                <i class="fa-solid fa-check-double"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif



                                {{-- {!! Form::open(['route' => ['accusoft.JournalEntry.destroy', $entry->id], 'method' => 'delete']) !!}
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('Are you sure?')",
                                ]) !!}
                                   {!! Form::close() !!} --}}

                            </div>

                        </td>





                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $journalEntries])
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (typeof window.promptLockPassword !== 'function') {
        window.promptLockPassword = function(editUrl, verifyUrl) {
            Swal.fire({
                title: "@lang('accusoft::models/as_accounting_settings.fields.lock_period_pwd')",
                input: 'password',
                inputAttributes: { autocapitalize: 'off' },
                showCancelButton: true,
                confirmButtonText: "@lang('lang.submit')",
                cancelButtonText: "@lang('lang.cancel')",
                showLoaderOnConfirm: true,
                preConfirm: (pwd) => {
                    return $.ajax({
                        url: verifyUrl,
                        type: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            password: pwd
                        }
                    }).then(response => {
                        if (!response.success) throw new Error(response.message || "@lang('lang.wrongOldPassword')");
                        return pwd;
                    }).catch(error => {
                        Swal.showValidationMessage(error.message || "@lang('lang.wrongOldPassword')");
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = editUrl + '?lock_password=' + encodeURIComponent(result.value);
                }
            });
        };
    }
</script>
@endpush
