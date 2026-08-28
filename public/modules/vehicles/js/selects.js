window.initAjaxSelect = function (selector, url = null, placeholder = null, dropdownParent = null) {
    const el = $(selector);

    if (!el.length) {
        return;
    }

    url = url || el.data('ajax-url');
    placeholder = placeholder || el.data('placeholder') || 'Select option';

    if (!dropdownParent || !dropdownParent.length) {
        const modal = el.closest('.modal');
        dropdownParent = modal.length ? modal.find('.modal-content').first() : el.parent();
    } else if (dropdownParent.hasClass('modal')) {
        dropdownParent = dropdownParent.find('.modal-content').first();
    }

    if (!dropdownParent.length) {
        dropdownParent = el.parent();
    }

    if (el.hasClass('select2-hidden-accessible')) {
        el.select2('destroy');
    }

    el.select2({
        placeholder: placeholder,
        width: '100%',
        allowClear: true,
        minimumInputLength: 0,
        dropdownParent: dropdownParent,
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term || '' }),
            processResults: data => ({ results: Array.isArray(data) ? data : (data.results || []) }),
        },
    });
};

// Required for Select2 inside Metronic / Bootstrap modals (prevents click from being swallowed).
if (!window.__vehiclesSelect2MousedownBound) {
    window.__vehiclesSelect2MousedownBound = true;

    $(document).on('mousedown', '.select2-selection', function (e) {
        e.stopPropagation();

        const select = $(this).closest('.select2-container').prev('select');

        if (select.length) {
            select.select2('open');
        }
    });
}
