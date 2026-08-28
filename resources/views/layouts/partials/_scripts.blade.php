<!--begin::Javascript-->
<script>
    var hostUrl = "/{{ asset('admin_assets') }}";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.js"></script>
<script src="{{ asset('admin_assets') }}/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ asset('admin_assets') }}/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
{{-- <script src="{{ asset('admin_assets') }}/plugins/custom/datatables/datatables.bundle.js"></script> --}}
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ asset('admin_assets') }}/js/widgets.bundle.js"></script>
<script src="{{ asset('admin_assets') }}/js/custom/widgets.js"></script>
@if(!request()->routeIs('terminal') && !request()->routeIs('pos.*'))
<script src="{{ asset('admin_assets') }}/js/custom/apps/chat/chat.js"></script>
<script src="{{ asset('admin_assets') }}/js/custom/utilities/modals/bidding.js"></script>
<script src="{{ asset('admin_assets') }}/js/custom/utilities/modals/users-search.js"></script>
@endif
<!--end::Custom Javascript-->

<!--CKEditor Build Bundles:: Only include the relevant bundles accordingly-->
<script src="{{ asset('admin_assets') }}/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>

<script type="text/javascript"
    src='https://maps.google.com/maps/api/js?libraries=places&key=AIzaSyAJDNGhvRiWXMvI7VjALT363E3QMOqp6j8'></script>
<script src="{{ asset('admin_assets') }}/js/locationpicker.jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>






<script>
    /* Get the element you want to display in fullscreen */
    var elem = document.body;

    function cancelFullScreen() {
        var el = document;
        var requestMethod =
            el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen || el
            .webkitExitFullscreen;
        if (requestMethod) { // cancel full screen.
            requestMethod.call(el);
        } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
            var wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
    }

    function requestFullScreen(el) {
        // Supports most browsers and their versions.
        var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen ||
            el.msRequestFullscreen;

        if (requestMethod) { // Native full screen.
            requestMethod.call(el);
        } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
            var wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
        return false
    }

    function toggleFullScreen(el) {
        if (!el) {
            el = document.body; // Make the body go full screen.
        }
        var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) || (document
            .mozFullScreen ||
            document.webkitIsFullScreen);

        if (isInFullScreen) {
            cancelFullScreen();
        } else {
            requestFullScreen(el);
        }
        return false;
    }
</script>
<script>
    var up_comming = @json(\Carbon\Carbon::now()->addDays(15)->format('Y-m-d'));
    $(document).ready(function() {
        var els = $('.upComingCheck');
        $.each(els, function(index, el) {
            var date = $(el).html();
            if (date <= up_comming) {
                $(el).addClass('text-danger');
            }
        })
    });
</script>
<!--end::Javascript-->
@livewireScripts
@livewireChartsScripts
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
<script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script>
<x-livewire-alert::flash />



@yield('scripts')
@stack('scripts')
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>



<script>
    document.addEventListener('DOMContentLoaded', function() {

        // نستهدف جميع النماذج في الصفحة
        document.querySelectorAll('form').forEach(form => {

            form.addEventListener('submit', function(e) {
                // نمنع تكرار التنفيذ لو النموذج تم إرساله بالفعل
                if (form.classList.contains('is-submitting')) {
                    e.preventDefault();
                    return false;
                }

                form.classList.add('is-submitting');

                const submitBtn = e.submitter ?? form.querySelector('[type="submit"]');

                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;

                    // Defer disable so the clicked submit button's name/value are still posted
                    setTimeout(() => {
                        submitBtn.innerHTML = 'الرجاء الانتظار...';
                        submitBtn.disabled = true;
                    }, 0);

                    setTimeout(() => {
                        form.classList.remove('is-submitting');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 10000);
                }
            });
        });
    });
</script>



<script>
    $(document).on('click', '.copy-table', function() {
        const tableSelector = $(this).data('target');
        const $table = $(tableSelector);

        if ($table.length === 0) {
            alert('الجدول غير موجود!');
            return;
        }

        let tableText = '';

        // إضافة رؤوس الأعمدة بدون آخر عمود
        $table.find('thead tr').each(function() {
            const rowText = $(this).find('th:not(:last-child)').map(function() {
                return $(this).text().trim();
            }).get().join('\t');
            tableText += rowText + '\n';
        });

        // إضافة الصفوف بدون آخر عمود
        $table.find('tbody tr').each(function() {
            const rowText = $(this).find('td:not(:last-child)').map(function() {
                return $(this).text().trim();
            }).get().join('\t');
            tableText += rowText + '\n';
        });

        // نسخ النص للحافظة
        navigator.clipboard.writeText(tableText).then(() => {
            const icon = $(this).find('i');
            icon.css('color', '#16a34a'); // لون أخضر عند النجاح
            setTimeout(() => icon.css('color', '#4a5568'), 1000);
            // toastr.success('تم نسخ الجدول بنجاح');
        }).catch(err => {
            console.error('خطأ في النسخ:', err);
        });
    });
</script>
























{{-- <script>
    (function() {

        function initSelect($select) {
            if ($select.hasClass('select2-hidden-accessible')) return;

            let placeholder = $select.data('placeholder') || 'اختر حساب';
            let selectedId = $select.data('selected') || $select.val() || null;

            $select.select2({
                ajax: {
                    url: '/api/TreeAccounts/ajex',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            lang: '{{ app()->getLocale() }}',
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination?.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: placeholder,
                allowClear: true,
                minimumInputLength: 0,
                dir: 'rtl'
            });

            // ✅ تحميل القيمة المحددة مسبقاً
            if (selectedId) {
                $.ajax({
                    url: '/api/TreeAccounts/ajex',
                    data: {
                        id: selectedId,
                        lang: '{{ app()->getLocale() }}',
                    },
                    dataType: 'json'
                }).done(function(data) {
                    if (data.results && data.results.length) {
                        let acc = data.results[0];
                        let option = new Option(acc.text, acc.id, true, true);
                        $select.empty().append(option).trigger('change');
                    }
                });
            }
        }

        // تهيئة العناصر الموجودة
        $(document).ready(function() {
            $('.select2-account').each(function() {
                initSelect($(this));
            });
        });

        // مراقبة العناصر الجديدة
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType !== 1) return;

                    if (node.matches?.('.select2-account')) {
                        initSelect($(node));
                    }

                    $(node).find?.('.select2-account').each(function() {
                        initSelect($(this));
                    });
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    })();
</script> --}}


{{-- <script>
    (function() {

        function initSelect($select) {
            if ($select.hasClass('select2-hidden-accessible')) return;

            let placeholder = $select.data('placeholder') || 'اختر حساب';
            let selectedId = $select.data('selected') || null;

            // ✅ إذا كان هناك قيمة محددة، نحملها أولاً
            if (selectedId) {
                $.ajax({
                    url: '/api/TreeAccounts/ajex',
                    data: {
                        id: selectedId,
                        lang: '{{ app()->getLocale() }}',
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        // ✅ إضافة مؤشر التحميل
                        $select.prop('disabled', true);
                    }
                }).done(function(data) {
                    if (data.results && data.results.length) {
                        let acc = data.results[0];
                        let option = new Option(acc.text, acc.id, true, true);
                        $select.append(option);
                    }
                }).fail(function(xhr, status, error) {
                    console.error('خطأ في تحميل القيمة المحددة:', error);
                }).always(function() {
                    $select.prop('disabled', false);
                    // ✅ تهيئة select2 بعد تحميل القيمة
                    initializeSelect2($select, placeholder);
                });
            } else {
                // ✅ تهيئة مباشرة إذا لم يكن هناك قيمة
                initializeSelect2($select, placeholder);
            }
        }

        function initializeSelect2($select, placeholder) {
            $select.select2({
                ajax: {
                    url: '/api/TreeAccounts/ajex',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            lang: '{{ app()->getLocale() }}',
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination?.more || false
                            }
                        };
                    },
                    cache: true
                },
                placeholder: placeholder,
                allowClear: false,
                minimumInputLength: 0,
                dir: 'rtl',
                language: {
                    searching: function() {
                        return 'جاري البحث...';
                    },
                    noResults: function() {
                        return 'لا توجد نتائج';
                    },
                    loadingMore: function() {
                        return 'جاري تحميل المزيد...';
                    }
                }
            });
        }

        // تهيئة العناصر الموجودة
        $(document).ready(function() {
            $('.select2-account').each(function() {
                initSelect($(this));
            });
        });

        // مراقبة العناصر الجديدة
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType !== 1) return;

                    if (node.matches?.('.select2-account')) {
                        initSelect($(node));
                    }

                    $(node).find?.('.select2-account').each(function() {
                        initSelect($(this));
                    });
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    })();
</script> --}}

<script>
    (function() {
        function initAjaxSelect($select, url, placeholder) {
            if ($select.hasClass('select2-hidden-accessible')) return;

            let selectedId = $select.data('selected') || $select.val() || null;

            function initialize() {
                $select.select2({
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                search: params.term || '',
                                page: params.page || 1,
                                lang: '{{ app()->getLocale() }}',
                            };
                        },
                        processResults: function(data, params) {
                            return {
                                results: data.results || [],
                                pagination: {
                                    more: data.pagination?.more || false
                                }
                            };
                        },
                        cache: true
                    },
                    placeholder: placeholder,
                    allowClear: true,
                    minimumInputLength: 0,
                    dir: 'rtl',
                    language: {
                        searching: function() {
                            return 'جاري البحث...';
                        },
                        noResults: function() {
                            return 'لا توجد نتائج';
                        },
                        loadingMore: function() {
                            return 'جاري تحميل المزيد...';
                        }
                    }
                });
            }

            if (selectedId) {
                $.ajax({
                    url: url,
                    data: {
                        id: selectedId,
                        lang: '{{ app()->getLocale() }}',
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $select.prop('disabled', true);
                    }
                }).done(function(data) {
                    if (data.results && data.results.length) {
                        let item = data.results[0];
                        let option = new Option(item.text, item.id, true, true);
                        if (!$select.find("option[value='" + item.id + "']").length) {
                            $select.append(option);
                        }
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Error loading selected item:', error);
                }).always(function() {
                    $select.prop('disabled', false);
                    initialize();
                });
            } else {
                initialize();
            }
        }

        $(document).ready(function() {
            $('.select2-ajax-customers').each(function() {
                initAjaxSelect($(this), '{{ route("Lookup.getCustomers") }}', 'اختر العميل');
            });
            $('.select2-ajax-suppliers').each(function() {
                initAjaxSelect($(this), '{{ route("Lookup.getSuppliers") }}', 'اختر المورد');
            });
            $('.select2-ajax-stores').each(function() {
                initAjaxSelect($(this), '{{ route("Lookup.getStores") }}', 'اختر المستودع');
            });
            $('.select2-ajax-users').each(function() {
                initAjaxSelect($(this), '{{ route("Lookup.getUsers") }}', 'اختر المستخدم');
            });
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType !== 1) return;

                    if (node.matches?.('.select2-ajax-customers')) {
                        initAjaxSelect($(node), '{{ route("Lookup.getCustomers") }}', 'اختر العميل');
                    }
                    if (node.matches?.('.select2-ajax-suppliers')) {
                        initAjaxSelect($(node), '{{ route("Lookup.getSuppliers") }}', 'اختر المورد');
                    }
                    if (node.matches?.('.select2-ajax-stores')) {
                        initAjaxSelect($(node), '{{ route("Lookup.getStores") }}', 'اختر المستودع');
                    }
                    if (node.matches?.('.select2-ajax-users')) {
                        initAjaxSelect($(node), '{{ route("Lookup.getUsers") }}', 'اختر المستخدم');
                    }

                    $(node).find('.select2-ajax-customers').each(function() {
                        initAjaxSelect($(this), '{{ route("Lookup.getCustomers") }}', 'اختر العميل');
                    });
                    $(node).find('.select2-ajax-suppliers').each(function() {
                        initAjaxSelect($(this), '{{ route("Lookup.getSuppliers") }}', 'اختر المورد');
                    });
                    $(node).find('.select2-ajax-stores').each(function() {
                        initAjaxSelect($(this), '{{ route("Lookup.getStores") }}', 'اختر المستودع');
                    });
                    $(node).find('.select2-ajax-users').each(function() {
                        initAjaxSelect($(this), '{{ route("Lookup.getUsers") }}', 'اختر المستخدم');
                    });
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    })();
</script>



{{-- <script>
    // livewire sortable
    let root = document.querySelector('[drag-root]');
   root.querySelectorAll('[drag-item]').forEach((item) => {
       el.addEventListener('dragstart', (e) => {
           e.target.setAttribute('dragging', true);
       });

       el.addEventListener('drop', (e) => {
           e.target.classList.remove('bg-gray-300');
           let draggingEl = root.querySelector('[dragging=true]');
           root.insertBefore(draggingEl, e.target);
       });

       el.addEventListener('dragenter', (e) => {
           e.target.classList.add('bg-gray-300');
       });

       el.addEventListener('dragover', (e) => e.preventDefault());

       el.addEventListener('dragleave', (e) => {
           e.target.classList.remove('bg-gray-300');
       });

       el.addEventListener('dragend', (e) => {
           e.dataTransfer.clearData();
       });
   })
</script> --}}

<script>
    /* ==========================================================================
       Evix ERP Global Theme Manager (Reactive Dark / Light Engine)
       ========================================================================== */
    (function() {
        function getSystemTheme() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applyTheme(mode) {
            var resolved = mode === 'system' ? getSystemTheme() : mode;
            document.documentElement.setAttribute('data-bs-theme', resolved);
            document.documentElement.setAttribute('data-bs-theme-mode', mode);
            localStorage.setItem('data-bs-theme', mode);
            localStorage.setItem('data-bs-theme-mode', mode);
            localStorage.setItem('kt_theme_mode_value', mode);

            // Update theme toggle icons / active state in header
            document.querySelectorAll('[data-kt-element="mode"]').forEach(function(el) {
                var val = el.getAttribute('data-kt-value');
                if (val === mode) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });

            // Update ApexCharts if present
            if (window.ApexCharts && window.dashboardCharts) {
                Object.keys(window.dashboardCharts).forEach(function(key) {
                    var chart = window.dashboardCharts[key];
                    if (chart && typeof chart.updateOptions === 'function') {
                        try {
                            chart.updateOptions({
                                theme: { mode: resolved },
                                chart: { background: 'transparent' }
                            }, false, false);
                        } catch(e) {}
                    }
                });
            }

            // Dispatch global event for any Livewire / custom listeners
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: resolved, mode: mode } }));
        }

        // Attach click listeners to theme switcher items
        document.addEventListener('click', function(e) {
            var target = e.target.closest('[data-kt-element="mode"]');
            if (target) {
                e.preventDefault();
                var mode = target.getAttribute('data-kt-value') || 'light';
                applyTheme(mode);
            }
        });

        // Watch for OS theme changes when in 'system' mode
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                var currentMode = localStorage.getItem('data-bs-theme-mode') || localStorage.getItem('data-bs-theme') || 'light';
                if (currentMode === 'system') {
                    applyTheme('system');
                }
            });
        }

        // Sync initial state
        document.addEventListener('DOMContentLoaded', function() {
            var currentMode = localStorage.getItem('data-bs-theme-mode') || localStorage.getItem('data-bs-theme') || 'light';
            applyTheme(currentMode);
        });
    })();
</script>
