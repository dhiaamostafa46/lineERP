<select class="form-select" id="select_{{$attributes['input_name']}}" data-control="select2"
    data-placeholder="{{ $placeholder }}">
    <option value="" selected disabled readonly>{{$attributes['input_name']}}</option>
    @forelse ($attributes['list'] as $item_id => $item_name)
    <option value="{{ $item_id }}" @if ($item_id==$attributes['selected_id']??0) selected @endif>
        {{ $item_name }}
    </option>
    @empty
    @endforelse
</select>

@push('scripts')
<script>
    var id = @json('#'.$attributes['input_name']);
    var name = @json($attributes['input_name']);
    var placeholder = @json($attributes['placeholder']);
    $(document).ready(function() {
        $("{{ '#select_'.$attributes['input_name'] }}").on('change', function (e) {
            @this.set("{{$attributes['input_name'] }}",$(this).select2("val"));
        });
    });
</script>
@endpush