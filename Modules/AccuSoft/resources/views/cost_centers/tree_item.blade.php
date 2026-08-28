@once
<style>
    .account-tree-toggle {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.15s ease-in-out;
    }
    .account-tree-toggle:hover {
        color: #212529;
    }
    .account-tree-toggle .toggle-icon {
        transition: transform 0.2s ease-in-out;
    }
    .account-tree-toggle[aria-expanded="false"] .toggle-icon {
        transform: rotate(-90deg);
    }
    .account-tree-item .actions {
        visibility: hidden;
        transition: visibility 0.1s ease-in-out;
    }
    .account-tree-item:hover .actions {
        visibility: visible;
    }
</style>
@endonce

<li class="py-1 account-tree-item">
    <div class="d-flex align-items-center" style="padding-left: {{ $level * 1.5 }}rem;">
        <div class="flex-grow-1 d-flex align-items-center">
            @if ($account->children && $account->children->count() > 0)
                <a href="#node-{{ $account->id }}" data-bs-toggle="collapse" class="me-2 account-tree-toggle" aria-expanded="false">
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </a>
            @else
                <span class="me-2" style="display: inline-block; width: 14px;"></span>
            @endif

            <i class="far fa-folder me-2 text-primary"></i>
            <span class="fw-bold">{{ $account->name }}</span>
            <span class="text-muted small mx-2">({{ $account->code }})</span>
            <span class="badge {{ $account->status_badge }}">{{ $account->status_text }}</span>
        </div>

        <div class="actions">
            <div class='btn-group btn-group-sm'>
                <a href="{{ route('accusoft.CostCenter.show', [$account->id]) }}" class='btn btn-light border-0' title="@lang('crud.view')">
                    <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('accusoft.CostCenter.edit', [$account->id]) }}" class='btn btn-light border-0' title="@lang('crud.edit')">
                    <i class="fa-solid fa-edit"></i>
                </a>
                {!! Form::open(['route' => ['accusoft.CostCenter.destroy', $account->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                {!! Form::button('<i class="fa-solid fa-trash text-danger"></i>', [
                    'type' => 'submit',
                    'class' => 'btn btn-light border-0',
                    'title' => __('crud.delete'),
                    'onclick' => "return confirm('Are you sure?')",
                ]) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    @if ($account->children && $account->children->count() > 0)
        <ul class="list-unstyled collapse" id="node-{{ $account->id }}">
            @foreach ($account->children as $child)
                @include('accusoft::tree_accounts.tree_item', ['account' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
