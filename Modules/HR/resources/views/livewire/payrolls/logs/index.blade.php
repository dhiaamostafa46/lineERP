<div>
    <div class="accordion mb-5" id="accordionPanelsStayOpenExample">
        @forelse ($logs as $log)
        <div class="accordion-item my-3" style="-webkit-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
            -moz-box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);
            box-shadow: 0px 10px 25px -5px rgba(0,0,0,0.75);">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#panelsStayOpen-collapse{{ $log->id }}" aria-expanded="false"
                    aria-controls="panelsStayOpen-collapse{{ $log->id }}">
                    {{ $log->causer->name }} - {{ $log->description }} - {{ $log->created_at->diffForHumans() }}
                </button>
            </h2>
            <div id="panelsStayOpen-collapse{{ $log->id }}" class="accordion-collapse collapse">
                <div class="accordion-body">
                    @forelse ($log->properties as $key => $item)
                    <p><b>{{ $key }}:</b>
                        @if (is_array($item))
                        {{ implode(', ', $item) }}
                        @else
                        {!! $item !!}
                        @endif
                    </p>
                    @empty

                    @endforelse
                </div>
            </div>
        </div>

        @empty

        @endforelse
    </div>
    {{ $logs->onEachSide(2)->links('vendor/livewire/bootstrap') }}
</div>
