{!! Form::open(['route' => 'hr.advances.store', 'files' => true]) !!}

<div class="card-body">

    <div class="row">


        <!-- Due At Field -->

        <input type="hidden" name="employee_id" value="{{ $employee_id }}">
          <input type="hidden" name="type_page" value="emp">
        <!-- Amount Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label('amount', __('hr::models/hr_advances.fields.amount') . ':') !!}
            {!! Form::number('amount', old('amount', isset($advance) ? $advance->amount : null), [
                'class' => 'form-control',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
            ]) !!}
        </div>

        <!-- Due At Field (Hidden) -->
        <div class="form-group col-sm-6 mb-3" style="display: none;">
            {!! Form::label('due_at', __('hr::models/hr_advances.fields.due_at') . ':') !!}
            {!! Form::date('due_at', old('due_at', isset($advance) ? $advance->due_at : now()->format('Y-m-d')), [
                'class' => 'form-control',
            ]) !!}
        </div>

        <!-- Date Range Picker -->
        <div class="row">
            <div class="col-sm-6">
                <label for="kt_td_picker_linked_1_input" class="form-label">
                    {{ __('hr::models/hr_advances.fields.from_date') }}
                </label>
                <div class="input-group log-event" id="kt_td_picker_linked_1" data-td-target-input="nearest"
                    data-td-target-toggle="nearest">
                    <input id="kt_td_picker_linked_1_input" name="from_date" type="text" class="form-control"
                        data-td-target="#kt_td_picker_linked_1"
                        value="{{ old('from_date', isset($advance) ? $advance->from_date : '') }}" required />
                    <span class="input-group-text" data-td-target="#kt_td_picker_linked_1"
                        data-td-toggle="datetimepicker">
                        <i class="ki-duotone ki-calendar fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </div>
            </div>

            <div class="col-sm-6">
                <label for="kt_td_picker_linked_2_input" class="form-label">
                    {{ __('hr::models/hr_advances.fields.to_date') }}
                </label>
                <div class="input-group log-event" id="kt_td_picker_linked_2" data-td-target-input="nearest"
                    data-td-target-toggle="nearest">
                    <input id="kt_td_picker_linked_2_input" name="to_date" type="text" class="form-control"
                        data-td-target="#kt_td_picker_linked_2"
                        value="{{ old('to_date', isset($advance) ? $advance->to_date : '') }}" required />
                    <span class="input-group-text" data-td-target="#kt_td_picker_linked_2"
                        data-td-toggle="datetimepicker">
                        <i class="ki-duotone ki-calendar fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </div>
            </div>
        </div>


        <div class="form-group col-sm-12 mb-3">
            {!! Form::label('reason', __('hr::models/hr_advances.fields.reason') . ':') !!}
            {!! Form::text('reason', old('reason', isset($advance) ? $advance->reason : null), [
                'class' => 'form-control',
            ]) !!}
        </div>

        <!-- Description Field -->
        <div class="form-group col-sm-12 mb-3">
            {!! Form::label('description', __('hr::models/hr_advances.fields.description') . ':') !!}
            {!! Form::textarea('description', old('description', isset($advance) ? $advance->description : null), [
                'class' => 'form-control',
                'rows' => 3,
            ]) !!}
        </div>

        <!-- File Upload Field -->
        <div class="form-group col-sm-12 mb-3">
            <label class="form-label">{{ __('hr::models/hr_advances.fields.attachment') }}</label>
            <div class="fv-row">
                <input type="file" name="attachment" id="attachment_input" class="form-control"
                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" onchange="previewAttachment(this)">
                <div class="form-text">
                    size: 10MB |
                    type: Images, PDF, Word, Excel
                </div>
            </div>

            <!-- Preview Area -->
            <div id="attachment_preview" class="mt-3" style="display: none;">
                <div class="alert alert-light d-flex align-items-center">
                    <i class="ki-duotone ki-file fs-2x me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="flex-grow-1">
                        <strong id="preview_filename"></strong>
                        <br>
                        <small id="preview_filesize" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn btn-sm btn-light-danger" onclick="removeAttachment()">
                        <i class="ki-duotone ki-trash fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        {{ __('hr::crud.delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Display Existing Attachment -->
        @if (isset($advance) && $advance->attachment)
            <div class="form-group col-sm-12 mb-3">
                <label class="form-label">{{ __('hr::models/hr_advances.fields.current_attachment') }}</label>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="ki-duotone ki-file fs-2x me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="flex-grow-1">
                        <a href="{{ asset($advance->attachment) }}" target="_blank">
                            {{ __('hr::lang.view_attachment') }}
                        </a>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_attachment" id="remove_attachment"
                            value="1">
                        <label class="form-check-label" for="remove_attachment">
                            {{ __('hr::lang.remove_current_attachment') }}
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <!-- Existing Monthly Payments (Only on Edit) -->
        @if (isset($advance) && $advance->monthlyPayments && $advance->monthlyPayments->count() > 0)
            <div class="col-sm-12 mb-3">
                <h5>{{ __('hr::models/hr_monthly_payments.plural') }}</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('hr::models/hr_monthly_payments.fields.due_at') }}</th>
                                <th>{{ __('hr::models/hr_monthly_payments.fields.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advance->monthlyPayments as $payment)
                                <tr>
                                    <td>{{ $payment->due_at->format('Y-m') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Monthly Payments Calculation Table -->
        <div class="col-sm-12 mb-3">
            <div class="alert alert-warning" id="installment_warning" style="display: none;">
                <i class="ki-duotone ki-information-5 fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <span class="ms-2">{{ __('hr::lang.minimum_one_installment_required') }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="monthly_payments_table" style="display: none;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%">{{ __('hr::models/hr_monthly_payments.fields.due_at') }}</th>
                            <th style="width: 50%">{{ __('hr::models/hr_monthly_payments.fields.amount') }}</th>
                            <th style="width: 10%" class="text-center">{{ __('hr::crud.action') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th>Total</th>
                            <th colspan="2" id="monthly_total">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @push('scripts')
            <script>
                (function() {
                    'use strict';

                    // ==================== File Upload Functions ====================
                    window.previewAttachment = function(input) {
                        const file = input.files[0];
                        const previewDiv = document.getElementById('attachment_preview');
                        const filenameSpan = document.getElementById('preview_filename');
                        const filesizeSpan = document.getElementById('preview_filesize');

                        if (file) {
                            // Check file size (10MB)
                            const maxSize = 10 * 1024 * 1024;
                            if (file.size > maxSize) {
                                alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
                                input.value = '';
                                previewDiv.style.display = 'none';
                                return;
                            }

                            // Display preview
                            filenameSpan.textContent = file.name;
                            filesizeSpan.textContent = formatFileSize(file.size);
                            previewDiv.style.display = 'block';
                        } else {
                            previewDiv.style.display = 'none';
                        }
                    };

                    window.removeAttachment = function() {
                        const input = document.getElementById('attachment_input');
                        const previewDiv = document.getElementById('attachment_preview');

                        input.value = '';
                        previewDiv.style.display = 'none';
                    };

                    function formatFileSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                    }

                    // ==================== Date Pickers ====================
                    const linkedPicker1Element = document.getElementById("kt_td_picker_linked_1");
                    const linkedPicker2Element = document.getElementById("kt_td_picker_linked_2");

                    if (linkedPicker1Element && linkedPicker2Element && typeof tempusDominus !== 'undefined') {
                        try {
                            const linked1 = new tempusDominus.TempusDominus(linkedPicker1Element, {
                                display: {
                                    components: {
                                        calendar: true,
                                        date: true,
                                        month: true,
                                        year: true,
                                        decades: true,
                                        clock: false
                                    }
                                },
                                localization: {
                                    format: 'yyyy-MM-dd'
                                }
                            });

                            const linked2 = new tempusDominus.TempusDominus(linkedPicker2Element, {
                                useCurrent: false,
                                display: {
                                    components: {
                                        calendar: true,
                                        date: true,
                                        month: true,
                                        year: true,
                                        decades: true,
                                        clock: false
                                    }
                                },
                                localization: {
                                    format: 'yyyy-MM-dd'
                                }
                            });

                            linkedPicker1Element.addEventListener(tempusDominus.Namespace.events.change, (e) => {
                                if (e.detail && e.detail.date) {
                                    linked2.updateOptions({
                                        restrictions: {
                                            minDate: e.detail.date
                                        }
                                    });
                                }
                                calculateAndDisplayMonthlyPayments();
                            });

                            linked2.subscribe(tempusDominus.Namespace.events.change, (e) => {
                                if (e.date) {
                                    linked1.updateOptions({
                                        restrictions: {
                                            maxDate: e.date
                                        }
                                    });
                                }
                                calculateAndDisplayMonthlyPayments();
                            });
                        } catch (error) {
                            console.error("Error initializing date pickers:", error);
                        }
                    }

                    // ==================== Monthly Payments Calculation ====================
                    const amountInput = document.querySelector('input[name="amount"]');
                    const fromDateInput = document.querySelector('input[name="from_date"]');
                    const toDateInput = document.querySelector('input[name="to_date"]');

                    if (amountInput && fromDateInput && toDateInput) {
                        amountInput.addEventListener('input', debounce(calculateAndDisplayMonthlyPayments, 300));
                        fromDateInput.addEventListener('change', calculateAndDisplayMonthlyPayments);
                        toDateInput.addEventListener('change', calculateAndDisplayMonthlyPayments);

                        // Initial calculation on page load
                        if (amountInput.value && fromDateInput.value && toDateInput.value) {
                            setTimeout(calculateAndDisplayMonthlyPayments, 500);
                        }
                    }

                    function debounce(func, wait) {
                        let timeout;
                        return function executedFunction(...args) {
                            const later = () => {
                                clearTimeout(timeout);
                                func(...args);
                            };
                            clearTimeout(timeout);
                            timeout = setTimeout(later, wait);
                        };
                    }

                    function calculateAndDisplayMonthlyPayments() {
                        const amount = parseFloat(amountInput.value);
                        const fromDateValue = fromDateInput.value;
                        const toDateValue = toDateInput.value;

                        const tableBody = document.querySelector('#monthly_payments_table tbody');
                        const table = document.querySelector('#monthly_payments_table');
                        const warningAlert = document.getElementById('installment_warning');

                        if (!tableBody || !table) {
                            console.warn("Table elements not found");
                            return;
                        }

                        tableBody.innerHTML = '';

                        // Validation
                        if (!fromDateValue || !toDateValue || isNaN(amount) || amount <= 0) {
                            table.style.display = 'none';
                            if (warningAlert) warningAlert.style.display = 'none';
                            return;
                        }

                        const fromDate = new Date(fromDateValue);
                        const toDate = new Date(toDateValue);

                        if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime()) || fromDate > toDate) {
                            table.style.display = 'none';
                            if (warningAlert) warningAlert.style.display = 'none';
                            return;
                        }

                        table.style.display = 'table';
                        if (warningAlert) warningAlert.style.display = 'none';

                        // Calculate months
                        let currentDate = new Date(fromDate.getFullYear(), fromDate.getMonth(), 1);
                        const endDate = new Date(toDate.getFullYear(), toDate.getMonth(), 1);
                        const months = [];

                        while (currentDate <= endDate) {
                            months.push(new Date(currentDate));
                            currentDate.setMonth(currentDate.getMonth() + 1);
                        }

                        if (months.length === 0) {
                            table.style.display = 'none';
                            return;
                        }

                        // Calculate monthly amounts
                        const monthlyAmount = Math.floor((amount / months.length) * 100) / 100;
                        let totalAmount = 0;

                        months.forEach((month, index) => {
                            const year = month.getFullYear();
                            const monthStr = String(month.getMonth() + 1).padStart(2, '0');
                            const monthYear = `${year}-${monthStr}`;

                            const row = tableBody.insertRow();
                            const cell1 = row.insertCell(0);
                            const cell2 = row.insertCell(1);
                            const cell3 = row.insertCell(2);

                            cell1.innerHTML = `<strong>${monthYear}</strong>`;
                            cell1.style.width = '40%';
                            cell3.style.textAlign = 'center';

                            const input = document.createElement('input');
                            input.type = 'number';
                            input.name = `monthly_payments[${monthYear}]`;
                            input.className = 'form-control monthly-amount';
                            input.step = '0.01';
                            input.min = '0';
                            input.dataset.monthYear = monthYear;

                            // Calculate amount for this month
                            let currentMonthAmount = monthlyAmount;
                            if (index === months.length - 1) {
                                currentMonthAmount = (amount - totalAmount).toFixed(2);
                            } else {
                                totalAmount += monthlyAmount;
                            }

                            input.value = currentMonthAmount;
                            input.addEventListener('input', function(e) {
                                redistributeAmounts(e.target);
                            });

                            cell2.appendChild(input);

                            // Delete button
                            const deleteBtn = document.createElement('button');
                            deleteBtn.type = 'button';
                            deleteBtn.className = 'btn btn-sm btn-danger btn-delete-installment';
                            deleteBtn.innerHTML =
                                '<i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>';
                            deleteBtn.title = 'حذف القسط';
                            deleteBtn.addEventListener('click', function() {
                                deleteInstallment(row, monthYear);
                            });

                            cell3.appendChild(deleteBtn);
                        });

                        updateTotal();
                    }

                    function deleteInstallment(row, monthYear) {
                        const allRows = document.querySelectorAll('#monthly_payments_table tbody tr');
                        const warningAlert = document.getElementById('installment_warning');

                        // يجب أن يكون هناك قسط واحد على الأقل
                        if (allRows.length <= 1) {
                            if (warningAlert) {
                                warningAlert.style.display = 'block';
                                setTimeout(() => {
                                    warningAlert.style.display = 'none';
                                }, 3000);
                            }
                            alert('يجب أن يكون هناك قسط شهري واحد على الأقل');
                            return;
                        }

                        // حذف الصف
                        row.remove();

                        // إعادة ترتيب التواريخ والمبالغ
                        redistributeAfterDelete();
                        updateTotal();
                    }

                    function redistributeAfterDelete() {
                        const totalAmount = parseFloat(amountInput.value);
                        const tableBody = document.querySelector('#monthly_payments_table tbody');
                        const remainingRows = tableBody.querySelectorAll('tr');
                        const remainingMonths = remainingRows.length;

                        if (remainingMonths === 0) return;

                        // الحصول على تاريخ البداية والنهاية من الحقول الأصلية
                        const fromDateValue = fromDateInput.value;
                        const toDateValue = toDateInput.value;

                        if (!fromDateValue || !toDateValue) return;

                        const fromDate = new Date(fromDateValue);
                        const toDate = new Date(toDateValue);

                        // إنشاء قائمة بالأشهر المتبقية بناءً على عدد الصفوف
                        const months = [];
                        let currentDate = new Date(fromDate.getFullYear(), fromDate.getMonth(), 1);
                        const endDate = new Date(toDate.getFullYear(), toDate.getMonth(), 1);

                        while (currentDate <= endDate && months.length < remainingMonths) {
                            months.push(new Date(currentDate));
                            currentDate.setMonth(currentDate.getMonth() + 1);
                        }

                        // توزيع المبلغ بالتساوي على الأشهر المتبقية
                        const amountPerMonth = Math.floor((totalAmount / remainingMonths) * 100) / 100;
                        let distributedAmount = 0;

                        remainingRows.forEach((row, index) => {
                            const cells = row.cells;
                            const dateCell = cells[0];
                            const amountCell = cells[1];
                            const input = amountCell.querySelector('.monthly-amount');

                            if (months[index]) {
                                // تحديث التاريخ
                                const year = months[index].getFullYear();
                                const monthStr = String(months[index].getMonth() + 1).padStart(2, '0');
                                const monthYear = `${year}-${monthStr}`;

                                dateCell.innerHTML = `<strong>${monthYear}</strong>`;

                                // تحديث اسم الحقل
                                if (input) {
                                    input.name = `monthly_payments[${monthYear}]`;
                                    input.dataset.monthYear = monthYear;

                                    // تحديث المبلغ
                                    if (index === remainingMonths - 1) {
                                        // الشهر الأخير يحصل على المبلغ المتبقي
                                        input.value = (totalAmount - distributedAmount).toFixed(2);
                                    } else {
                                        input.value = amountPerMonth.toFixed(2);
                                        distributedAmount += amountPerMonth;
                                    }
                                }
                            }
                        });
                    }

                    function redistributeAmounts(changedInput) {
                        const totalAmount = parseFloat(amountInput.value);
                        const monthlyInputs = Array.from(document.querySelectorAll('.monthly-amount'));
                        const changedIndex = monthlyInputs.indexOf(changedInput);

                        if (changedIndex === -1) return;

                        let amountBeforeChanged = 0;
                        for (let i = 0; i < changedIndex; i++) {
                            amountBeforeChanged += parseFloat(monthlyInputs[i].value) || 0;
                        }

                        const changedAmount = parseFloat(changedInput.value) || 0;
                        const remainingAmount = totalAmount - amountBeforeChanged - changedAmount;
                        const remainingInputs = monthlyInputs.slice(changedIndex + 1);

                        if (remainingInputs.length > 0 && remainingAmount >= 0) {
                            const amountPerRemainingInput = Math.floor((remainingAmount / remainingInputs.length) * 100) / 100;
                            let distributedAmount = 0;

                            remainingInputs.forEach((input, index) => {
                                if (index === remainingInputs.length - 1) {
                                    input.value = (remainingAmount - distributedAmount).toFixed(2);
                                } else {
                                    input.value = amountPerRemainingInput.toFixed(2);
                                    distributedAmount += amountPerRemainingInput;
                                }
                            });
                        }

                        updateTotal();
                    }

                    function updateTotal() {
                        let total = 0;
                        const monthlyInputs = document.querySelectorAll('.monthly-amount');

                        monthlyInputs.forEach(input => {
                            const value = parseFloat(input.value) || 0;
                            total += value;
                        });

                        const totalCell = document.getElementById('monthly_total');
                        if (totalCell) {
                            totalCell.textContent = total.toFixed(2);

                            const totalAdvanceAmount = parseFloat(amountInput.value) || 0;
                            const difference = Math.abs(total - totalAdvanceAmount);

                            if (difference > 0.01) {
                                totalCell.style.color = '#dc3545';
                                totalCell.style.fontWeight = 'bold';
                                totalCell.title = `الفرق: ${difference.toFixed(2)}`;
                            } else {
                                totalCell.style.color = '#198754';
                                totalCell.style.fontWeight = 'bold';
                                totalCell.title = 'المجموع يطابق مبلغ السلفة';
                            }
                        }
                    }
                })();
            </script>
        @endpush


    </div>

</div>



<div class="card-footer py-4 text-end">
    <a href="{{ route('hr.advances.index') }}" class="btn btn-sm btn-secondary">
        @lang('crud.cancel')
    </a>
    {!! Form::submit(__('crud.send'), ['class' => 'btn btn-sm btn-primary']) !!}
</div>
@push('scripts')
    <script>
        var dtToday = new Date();
        $("#due_at").flatpickr({
            minDate: dtToday,
        });
    </script>
@endpush

{!! Form::close() !!}
