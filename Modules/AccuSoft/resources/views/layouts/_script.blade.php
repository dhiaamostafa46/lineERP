@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('AS-Accusoft-table');
            if (!table) return;

            const parentRows = table.querySelectorAll('.parent-account');

            parentRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('a')) return;
                    if (e.target.closest('td:last-child')) return;
                    toggleChildren(this);
                });
            });

            function toggleChildren(parentRow) {
                const accountId = parentRow.dataset.accountId;
                const toggleIcon = parentRow.querySelector('.toggle-icon');
                if (!toggleIcon) return;

                const isCollapsed = toggleIcon.classList.contains('collapsed');

                if (isCollapsed) {
                    showDirectChildren(accountId);
                    toggleIcon.classList.remove('collapsed');
                } else {
                    hideAllChildren(accountId);
                    toggleIcon.classList.add('collapsed');
                }
            }

            function showDirectChildren(parentId) {
                const allRows = table.querySelectorAll('.account-row');
                allRows.forEach(row => {
                    if (row.dataset.parentId === parentId) {
                        row.classList.remove('hidden');
                    }
                });
            }

            function hideAllChildren(parentId) {
                const allRows = table.querySelectorAll('.account-row');
                allRows.forEach(row => {
                    if (row.dataset.parentId === parentId) {
                        row.classList.add('hidden');
                        const toggleIcon = row.querySelector('.toggle-icon');
                        if (toggleIcon && !toggleIcon.classList.contains('collapsed')) {
                            toggleIcon.classList.add('collapsed');
                        }
                        hideAllChildren(row.dataset.accountId);
                    }
                });
            }

            function collapseAll() {
                const allRows = table.querySelectorAll('.account-row');
                allRows.forEach(row => {
                    const expandLevel = window.expandLevel || 1;
                    const level = parseInt(row.dataset.level);
                    const isLeaf = row.dataset.isLeaf === 'true';

                    if (level > expandLevel) {
                        row.classList.add('hidden');
                    }

                    if (!isLeaf) {
                        const toggleIcon = row.querySelector('.toggle-icon');
                        if (toggleIcon) {
                            if (level >= expandLevel) {
                                toggleIcon.classList.add('collapsed');
                            } else {
                                toggleIcon.classList.remove('collapsed');
                            }
                        }
                    }
                });
            }

            collapseAll();
        });
    </script>

 
        <script>
            new tempusDominus.TempusDominus(document.getElementById('kt_td_picker_from_input'), {
                display: {
                    components: {
                        calendar: true,
                        clock: false
                    },
                    buttons: {
                        today: true,
                        clear: true,
                        close: true
                    }
                },
                localization: {
                    format: 'yyyy-MM-dd'
                } // لتوافق قاعدة البيانات
            });

            new tempusDominus.TempusDominus(document.getElementById('kt_td_picker_to_input'), {
                display: {
                    components: {
                        calendar: true,
                        clock: false
                    },
                    buttons: {
                        today: true,
                        clear: true,
                        close: true
                    }
                },
                localization: {
                    format: 'yyyy-MM-dd'
                }
            });
        </script>

@endpush
