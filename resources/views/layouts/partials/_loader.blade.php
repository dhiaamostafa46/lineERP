<div class="h-100 bg-active-opacity-50 bg-dark active position-fixed top-0 start-0 w-100 no-print" style="z-index: 9999"
    id="loader">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-grow" role="status" style="color: #ffffff00">
            <img src="{{ asset('admin_assets') }}/media/logos/evix.ico" width="50">
        </div>
    </div>
</div>

<style>
@media print {
    #loader, .spinner-grow, .spinner-border {
        display: none !important;
    }
}
</style>

@push('scripts')
<script>
    $(document).ready(function () {
        $("#loader").delay(500).fadeOut(500);
    });
</script>
@endpush
