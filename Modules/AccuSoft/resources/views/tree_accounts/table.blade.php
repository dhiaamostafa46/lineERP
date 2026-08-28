<div class="card border-0 shadow-lg">

    <div class="card-header py-4 px-4">
        <div class="d-flex align-items-center w-100">
            <!-- العنوان -->
            <div class="d-flex align-items-center">
                <i class="fas fa-sitemap me-3 fs-4"></i>
                <h5 class="mb-0 fw-semibold">
                    @lang('accusoft::models/as_tree_account.plural')
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
    #kt_docs_jstree_ajax .jstree-anchor {
        font-size: 15px !important;
        padding: 6px 10px !important;
        height: auto !important;
        display: inline-flex;
        align-items: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    #kt_docs_jstree_ajax .jstree-themeicon {
        font-size: 1.2rem !important;
        width: 20px !important;
        height: 20px !important;
        margin-right: 8px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tree-actions {
        opacity: 0;
        visibility: hidden;
        display: inline-flex;
        gap: 4px;
        margin-left: 15px;
        transition: all 0.2s ease;
    }

    .jstree-anchor:hover .tree-actions {
        opacity: 1;
        visibility: visible;
    }

    .tree-actions .btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #eee;
        border-radius: 6px;
        background: white;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .tree-actions .btn-view { color: #6366f1; }
    .tree-actions .btn-view:hover { background: #6366f1; color: white; border-color: #6366f1; }
    .tree-actions .btn-edit { color: #f59e0b; }
    .tree-actions .btn-edit:hover { background: #f59e0b; color: white; border-color: #f59e0b; }
    .tree-actions .btn-delete { color: #ef4444; }
    .tree-actions .btn-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }

    .jstree-anchor:hover {
        background: #f8fafc !important;
    }

    .tree-container.loading {
        position: relative;
    }
    
    .tree-container.loading::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.5);
        z-index: 10;
    }
</style>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script>
        $(document).ready(function() {
            const $tree = $("#kt_docs_jstree_ajax");

            $tree.jstree({
                    "core": {
                        "themes": {
                            "name": "default",
                            "variant": "large",
                            "responsive": true,
                            "dots": false,
                            "icons": true
                        },
                        "check_callback": true,
                        "data": {
                            "url": function(node) {
                                return "{{ route('accusoft.TreeAccounts.children') }}";
                            },
                            "data": function(node) {
                                return { "id": node.id };
                            }
                        },
                        "animation": 100,
                        "worker": true
                    },
                    "types": {
                        "default": { "icon": "fas fa-folder text-primary" },
                        "file": { "icon": "fas fa-file-invoice-dollar text-muted" }
                    },
                    "plugins": ["types", "search"] // Removed 'state' to avoid multiple recursive requests on load
                })
                .on('select_node.jstree', function(e, data) {
                    data.instance.toggle_node(data.node);
                })
                .on('open_node.jstree', function(e, data) {
                    var $icon = $('#' + data.node.id).find('> .jstree-anchor > .jstree-themeicon');
                    $icon.addClass('fa-folder-open').removeClass('fa-folder');
                })
                .on('close_node.jstree', function(e, data) {
                    var $icon = $('#' + data.node.id).find('> .jstree-anchor > .jstree-themeicon');
                    $icon.removeClass('fa-folder-open').addClass('fa-folder');
                })
                .on('loading.jstree', function() {
                    $('.tree-container').addClass('loading');
                })
                .on('loaded.jstree ready.jstree', function() {
                    $('.tree-container').removeClass('loading');
                });

            // Search with debounce
            let searchTimeout;
            $('#treeSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                const value = $(this).val();
                searchTimeout = setTimeout(function() {
                    $tree.jstree(true).search(value);
                }, 400);
            });

            // Toolbar actions
            $('#reloadTree').click(function() {
                const $icon = $(this).find('i');
                $icon.addClass('fa-spin');
                $tree.jstree('refresh');
                setTimeout(() => $icon.removeClass('fa-spin'), 1000);
            });

            $('#expandAll').click(function() {
                $tree.jstree('open_all');
            });

            $('#collapseAll').click(function() {
                $tree.jstree('close_all');
            });
        });

        function viewAccount(id) {
            window.location.href = "{{ url('accusoft/TreeAccounts') }}/" + id;
        }

        function editAccount(id) {
            window.location.href = "{{ url('accusoft/TreeAccounts') }}/" + id + "/edit";
        }

        function deleteAccount(id) {
            if (confirm("{{ __('accusoft::messages.confirm_delete_account') }}")) {
                $.ajax({
                    url: "{{ url('accusoft/TreeAccounts') }}/" + id,
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
