<div class="card border-0 shadow-lg">

    <div class="card-header py-4 px-4">
        <div class="d-flex align-items-center w-100">
            <!-- العنوان -->
            <div class="d-flex align-items-center">
                <i class="fas fa-sitemap me-3 fs-4"></i>
                <h5 class="mb-0 fw-semibold">
                    @lang('accusoft::models/as_cost_centers.plural')
                </h5>
            </div>

            <!-- الأزرار في النهاية -->
            <div class="d-flex align-items-center gap-1 ms-auto">
                <button type="button" class="btn btn-sm btn-light" id="expandAll" title="فتح الكل">
                    <i class="fas fa-expand-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light" id="collapseAll" title="إغلاق الكل">
                    <i class="fas fa-compress-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light" id="reloadTree" title="تحديث">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>



    <div class="card-body p-0">
        <!-- Search Bar -->
        <div class="p-4 pb-3 bg-light d-none">
            <div class="input-group shadow-sm"
                style="border-radius: 12px; overflow: hidden; border: 2px solid #e0e0e0;">
                <span class="input-group-text bg-white border-0" style="padding: 16px 20px;">
                    <i class="fas fa-search" style="color: #667eea; font-size: 18px;"></i>
                </span>
                <input type="text" class="form-control border-0" id="treeSearch" placeholder="@lang('ابحث عن حساب...')"
                    style="padding: 16px; font-size: 16px; font-weight: 500;">
            </div>
        </div>

        <!-- Tree Container -->
        <div class="tree-container p-4">
            <div id="kt_docs_jstree_ajax"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<style>
#jstree .jstree-anchor {
    font-size: 30px !important;        /* حجم النص */
    padding: 12px 20px !important;  ;     /* padding عمودي 12px، أفقي 20px */
}

#jstree .jstree-themeicon {
    width: 30px !important;  ;            /* حجم أيقونة الشجرة */
    height: 30px !important;  ;
    margin-right: 8px !important;  ;      /* مسافة بين الأيقونة والنص */
}

/* إخفاء أيقونات الإجراءات بشكل افتراضي */
.tree-actions {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    display: inline-flex;
    gap: 5px;
    margin-right: 10px;
}

/* إظهار الأيقونات عند hover على السطر */
.jstree-anchor:hover .tree-actions {
    opacity: 1;
    visibility: visible;
}

/* تنسيق أزرار الإجراءات */
.tree-actions .btn {
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.tree-actions .btn-view {
    color: #667eea;
}

.tree-actions .btn-view:hover {
    background: #667eea;
    color: white;
}

.tree-actions .btn-edit {
    color: #f59e0b;
}

.tree-actions .btn-edit:hover {
    background: #f59e0b;
    color: white;
}

.tree-actions .btn-delete {
    color: #ef4444;
}

.tree-actions .btn-delete:hover {
    background: #ef4444;
    color: white;
}

/* تحسين تأثير hover على السطر بالكامل */
.jstree-anchor:hover {
    background: #f3f4f6 !important;
    border-radius: 8px;
}
</style>





@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize jsTree
            $("#kt_docs_jstree_ajax").jstree({
                    "core": {
                        "themes": {
                            "name": "default",
                            "variant" : "large",
                            "responsive": true,
                            "dots": true, // إيقاف النقاط والخطوط الافتراضية
                            "icons": true,
                            "stripes": false // إيقاف الخلفية الرمادية
                        },
                        "check_callback": true,
                        "data": {
                            "url": function(node) {
                                return "{{ route('accusoft.CostCenter.children') }}";
                            },
                            "data": function(node) {
                                return {
                                    "id": node.id
                                };
                            }
                        },
                        "animation": 300,
                        "worker": true
                    },
                    "types": {
                        "default": {
                            "icon": "fas fa-folder"
                        },
                        "file": {
                            "icon": "fas fa-file-invoice-dollar"
                        }
                    },
                    "plugins": ["types", "state", "search"]
                })
                .on('select_node.jstree', function(e, data) {
                    // فتح/إغلاق العقدة
                    data.instance.toggle_node(data.node);
                })
                .on('open_node.jstree', function(e, data) {
                    var $anchor = $('#' + data.node.id).children('.jstree-anchor');
                    $anchor.find('i.fa-folder').removeClass('fa-folder').addClass('fa-folder-open');
                })
                .on('close_node.jstree', function(e, data) {
                    $('#' + data.node.id).children('.jstree-anchor')
                        .find('i.fa-folder-open').removeClass('fa-folder-open').addClass('fa-folder');
                })
                .on('loading.jstree', function() {
                    $('.tree-container').addClass('loading');
                })
                .on('loaded.jstree ready.jstree', function() {
                    $('.tree-container').removeClass('loading');
                });

            // Search with debounce
            var searchTimeout = false;
            $('#treeSearch').on('keyup', function() {
                if (searchTimeout) clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    var value = $('#treeSearch').val();
                    $('#kt_docs_jstree_ajax').jstree(true).search(value);
                }, 300);
            });

            // Toolbar actions
            $('#reloadTree').click(function() {
                var $icon = $(this).find('i');
                $icon.addClass('fa-spin');
                $('#kt_docs_jstree_ajax').jstree('refresh');
                setTimeout(() => $icon.removeClass('fa-spin'), 1000);
            });

            $('#expandAll').click(function() {
                $('#kt_docs_jstree_ajax').jstree('open_all', null, 400);
            });

            $('#collapseAll').click(function() {
                $('#kt_docs_jstree_ajax').jstree('close_all', null, 400);
            });
        });

        // دوال العرض والتعديل
        function viewAccount(id) {
            window.location.href = "{{ url('accusoft/CostCenter') }}/" + id;
        }

        function editAccount(id) {
            window.location.href = "{{ url('accusoft/CostCenter') }}/" + id + "/edit";
        }

        function deleteAccount(id) {
            if (confirm("{{ __('accusoft::messages.confirm_delete_costcenter') }}")) {
                $.ajax({
                    url: "{{ url('accusoft/CostCenter') }}/" + id,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    data: {
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: "{{ __('accusoft::messages.success') }}",
                                text: response.message,
                                confirmButtonText: "{{ __('accusoft::messages.ok') }}"
                            });
                            $('#kt_docs_jstree_ajax').jstree('refresh');
                        }
                    },
                    error: function(xhr) {
                        let msg = "{{ __('accusoft::messages.delete_error_occurred') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: "{{ __('accusoft::messages.cannot_delete') }}",
                            text: msg,
                            confirmButtonText: "{{ __('accusoft::messages.ok') }}"
                        });
                    }
                });
            }
        }
    </script>
@endsection
