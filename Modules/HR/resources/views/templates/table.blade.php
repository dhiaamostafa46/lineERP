    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

    <style>

        .header {
            background-color: #1976d2;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .icon-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
        }

        .container-custom {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            text-align: right;
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-title {
            text-align: right;
            font-size: 18px;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
            text-align: right;
        }

        .required {
            color: #d32f2f;
            margin-right: 3px;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            width: 100%;
        }

        .form-group select {
            cursor: pointer;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-custom {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary-custom {
            background-color: #00bcd4;
            color: white;
        }

        .btn-secondary-custom {
            background-color: #26a69a;
            color: white;
        }

        .btn-success-custom {
            background-color: #4caf50;
            color: white;
        }

        .btn-warning-custom {
            background-color: #ff9800;
            color: white;
        }

        .note-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-right: 4px solid #1976d2;
        }

        .note-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1565c0;
            text-align: right;
        }

        .note-text {
            font-size: 13px;
            color: #555;
            line-height: 1.6;
            text-align: right;
        }

        .fields-section {
            background-color: #f1f8e9;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .fields-title {
            text-align: right;
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .tag {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tag:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .tag-green {
            background-color: #4caf50;
        }

        .tag-pink {
            background-color: #e91e63;
        }

        .tag-blue {
            background-color: #2196f3;
        }

        .tag-purple {
            background-color: #9c27b0;
        }

        .editor-container {
            margin-top: 20px;
        }

        .variable-highlight {
            background-color: #ffeb3b;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            margin: 0 3px;
            display: inline-block;
        }

        .note-editor {
            direction: rtl;
        }

        .note-editable {
            text-align: right;
            direction: rtl;
            min-height: 300px;
        }

        .variables-group {
            margin-bottom: 20px;
        }

        .group-header {
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
            padding: 5px 10px;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .templates-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .template-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .template-card h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .template-card p {
            font-size: 13px;
            color: #666;
        }

        .template-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .template-actions button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .preview-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .preview-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            direction: rtl;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close-preview {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #viewMode {
            display: none;
        }
        </style>






<div class="container-custom">
        <!-- وضع إنشاء/تعديل القالب -->
        <div id="editMode">
            <h1 class="page-title">إدارة القوالب</h1>

            <div class="form-box">
                <h2 class="form-title">إنشاء قالب جديد</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label><span class="required">*</span>اسم القالب</label>
                        <input type="text" id="templateName" placeholder="مثال: قالب سلفة">
                    </div>

                    <div class="form-group">
                        <label><span class="required">*</span>نوع القالب</label>
                        <input type="text" id="templateType" placeholder="مثال: مالية">
                    </div>

                    <div class="form-group">
                        <label>الوصف</label>
                        <input type="text" id="templateDesc" placeholder="وصف القالب">
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-custom btn-secondary-custom" id="loadArabicVars">المتغيرات - العربية</button>
                    <button class="btn-custom btn-primary-custom" id="loadEnglishVars">المتغيرات - الإنجليزية</button>
                </div>

                <div class="note-box">
                    <div class="note-title">تنبيه هام:</div>
                    <div class="note-text">
                        المتغيرات التي تحتوي على 🔒 هي متغيرات محسوبة يتم احتسابها عند إصدار النموذج. انقر على أي متغير
                        لإضافته إلى القالب.
                    </div>
                </div>

                <div class="fields-section">
                    <div class="fields-title">المتغيرات المتاحة</div>
                    <div id="variablesContainer"></div>
                </div>

                <div class="editor-container">
                    <div id="summernote"></div>
                </div>

                <div class="action-buttons">
                    <button class="btn-custom btn-success-custom" id="saveTemplate">حفظ القالب</button>
                    <button class="btn-custom btn-warning-custom" id="viewTemplates">عرض القوالب المحفوظة</button>
                </div>
            </div>
        </div>

        <!-- وضع عرض القوالب -->
        <div id="viewMode">
            <h1 class="page-title">القوالب المحفوظة</h1>

            <div class="action-buttons" style="margin-bottom: 20px;">
                <button class="btn-custom btn-primary-custom" id="backToEdit">إنشاء قالب جديد</button>
            </div>

            <div class="templates-list" id="templatesList"></div>
        </div>
    </div>

    <!-- مودال المعاينة -->
    <div class="preview-modal" id="previewModal">
        <div class="preview-content">
            <div class="preview-header">
                <h2 id="previewTitle"></h2>
                <button class="close-preview" id="closePreview">إغلاق</button>
            </div>
            <div id="previewBody"></div>
            <div style="margin-top: 20px;">
                <h4>أدخل بيانات المتغيرات:</h4>
                <div id="variableInputs"></div>
                <button class="btn-custom btn-success-custom" id="fillVariables" style="margin-top: 15px;">ملء
                    المتغيرات</button>
            </div>
        </div>
    </div>


    <script>
        // المتغيرات المتاحة
        const arabicVariables = {
            "معلومات الموظف": [{
                    name: "اسم_الموظف",
                    label: "اسم الموظف",
                    color: "green"
                },
                {
                    name: "رقم_الموظف",
                    label: "رقم الموظف",
                    color: "pink"
                },
                {
                    name: "القسم",
                    label: "القسم",
                    color: "blue"
                },
                {
                    name: "المسمى_الوظيفي",
                    label: "المسمى الوظيفي",
                    color: "purple"
                },
                {
                    name: "رقم_الهاتف",
                    label: "رقم الهاتف",
                    color: "green"
                },
                {
                    name: "البريد_الالكتروني",
                    label: "البريد الإلكتروني",
                    color: "pink"
                }
            ],
            "معلومات السلفة": [{
                    name: "مبلغ_السلفة",
                    label: "مبلغ السلفة",
                    color: "blue"
                },
                {
                    name: "تاريخ_السلفة",
                    label: "تاريخ السلفة",
                    color: "purple"
                },
                {
                    name: "سبب_السلفة",
                    label: "سبب السلفة",
                    color: "green"
                },
                {
                    name: "عدد_الاقساط",
                    label: "عدد الأقساط",
                    color: "pink"
                }
            ],
            "معلومات محسوبة": [{
                    name: "🔒 قيمة_القسط",
                    label: "قيمة القسط الشهري",
                    color: "blue",
                    locked: true
                },
                {
                    name: "🔒 تاريخ_اليوم",
                    label: "تاريخ اليوم",
                    color: "purple",
                    locked: true
                },
                {
                    name: "🔒 اجمالي_المبلغ",
                    label: "إجمالي المبلغ",
                    color: "green",
                    locked: true
                }
            ]
        };

        const englishVariables = {
            "Employee Info": [{
                    name: "employee_name",
                    label: "Employee Name",
                    color: "green"
                },
                {
                    name: "employee_id",
                    label: "Employee ID",
                    color: "pink"
                },
                {
                    name: "department",
                    label: "Department",
                    color: "blue"
                },
                {
                    name: "position",
                    label: "Position",
                    color: "purple"
                }
            ],
            "Loan Info": [{
                    name: "loan_amount",
                    label: "Loan Amount",
                    color: "blue"
                },
                {
                    name: "loan_date",
                    label: "Loan Date",
                    color: "purple"
                },
                {
                    name: "loan_reason",
                    label: "Loan Reason",
                    color: "green"
                }
            ]
        };

        // تخزين القوالب
        let templates = JSON.parse(localStorage.getItem('templates') || '[]');
        let currentTemplate = null;

        // عرض المتغيرات
        function renderVariables(variablesData) {
            const container = $('#variablesContainer');
            container.empty();

            $.each(variablesData, function(groupName, variables) {
                const group = $('<div class="variables-group"></div>');
                group.append('<div class="group-header">' + groupName + '</div>');

                const tagsContainer = $('<div class="tags-container"></div>');

                $.each(variables, function(index, variable) {
                    const tag = $('<span class="tag tag-' + variable.color + '"></span>')
                        .text(variable.label)
                        .data('variable', variable);

                    tagsContainer.append(tag);
                });

                group.append(tagsContainer);
                container.append(group);
            });

            attachVariableClickEvent();
        }

        function attachVariableClickEvent() {
            $('.tag').off('click').on('click', function() {
                const variable = $(this).data('variable');
                const variableHtml = '<span class="variable-highlight">{{ ' + variable.name + ' }}</span>&nbsp;';
                $('#summernote').summernote('pasteHTML', variableHtml);
            });
        }

        // حفظ القالب
        function saveTemplate() {
            const name = $('#templateName').val().trim();
            const type = $('#templateType').val().trim();
            const desc = $('#templateDesc').val().trim();
            const content = $('#summernote').summernote('code');

            if (!name || !type) {
                alert('يرجى إدخال اسم القالب ونوعه');
                return;
            }

            const template = {
                id: currentTemplate ? currentTemplate.id : Date.now(),
                name: name,
                type: type,
                description: desc,
                content: content,
                createdAt: currentTemplate ? currentTemplate.createdAt : new Date().toISOString(),
                updatedAt: new Date().toISOString()
            };

            if (currentTemplate) {
                const index = templates.findIndex(t => t.id === currentTemplate.id);
                templates[index] = template;
            } else {
                templates.push(template);
            }

            localStorage.setItem('templates', JSON.stringify(templates));
            alert('تم حفظ القالب بنجاح!');

            currentTemplate = null;
            resetForm();
            showViewMode();
        }

        // عرض القوالب
        function displayTemplates() {
            const container = $('#templatesList');
            container.empty();

            if (templates.length === 0) {
                container.html('<p style="text-align: center; color: #999;">لا توجد قوالب محفوظة</p>');
                return;
            }

            templates.forEach(template => {
                const card = $(`
                    <div class="template-card">
                        <h3>${template.name}</h3>
                        <p><strong>النوع:</strong> ${template.type}</p>
                        <p>${template.description || 'لا يوجد وصف'}</p>
                        <div class="template-actions">
                            <button class="btn-primary-custom" onclick="useTemplate(${template.id})">استخدام</button>
                            <button class="btn-secondary-custom" onclick="editTemplate(${template.id})">تعديل</button>
                            <button class="btn-warning-custom" onclick="deleteTemplate(${template.id})">حذف</button>
                        </div>
                    </div>
                `);
                container.append(card);
            });
        }

        // استخدام القالب
        window.useTemplate = function(templateId) {
            const template = templates.find(t => t.id === templateId);
            if (!template) return;

            // استخراج المتغيرات من القالب
            const variableRegex = /\{\{([^}]+)\}\}/g;
            const matches = [...template.content.matchAll(variableRegex)];
            const variables = [...new Set(matches.map(m => m[1]))];

            // عرض المودال
            $('#previewTitle').text(template.name);
            $('#previewBody').html(template.content);

            const inputsContainer = $('#variableInputs');
            inputsContainer.empty();

            variables.forEach(varName => {
                inputsContainer.append(`
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>${varName}</label>
                        <input type="text" class="form-control var-input" data-var="${varName}"
                               placeholder="أدخل قيمة ${varName}">
                    </div>
                `);
            });

            $('#previewModal').css('display', 'flex');
        };

        // ملء المتغيرات
        $('#fillVariables').click(function() {
            let content = $('#previewBody').html();

            $('.var-input').each(function() {
                const varName = $(this).data('var');
                const value = $(this).val();
                const regex = new RegExp(`\\{\\{${varName}\\}\\}`, 'g');
                content = content.replace(regex, `<strong>${value}</strong>`);
            });

            $('#previewBody').html(content);
            alert('تم ملء المتغيرات بنجاح! يمكنك الآن طباعة أو نسخ المحتوى');
        });

        // تعديل القالب
        window.editTemplate = function(templateId) {
            const template = templates.find(t => t.id === templateId);
            if (!template) return;

            currentTemplate = template;
            $('#templateName').val(template.name);
            $('#templateType').val(template.type);
            $('#templateDesc').val(template.description);
            $('#summernote').summernote('code', template.content);

            showEditMode();
        };

        // حذف القالب
        window.deleteTemplate = function(templateId) {
            if (!confirm('هل أنت متأكد من حذف هذا القالب؟')) return;

            templates = templates.filter(t => t.id !== templateId);
            localStorage.setItem('templates', JSON.stringify(templates));
            displayTemplates();
        };

        function resetForm() {
            $('#templateName').val('');
            $('#templateType').val('');
            $('#templateDesc').val('');
            $('#summernote').summernote('code', '');
        }

        function showEditMode() {
            $('#editMode').show();
            $('#viewMode').hide();
        }

        function showViewMode() {
            $('#editMode').hide();
            $('#viewMode').show();
            displayTemplates();
        }

        // التهيئة
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 350,
                direction: 'rtl',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            renderVariables(arabicVariables);

            $('#loadArabicVars').click(() => renderVariables(arabicVariables));
            $('#loadEnglishVars').click(() => renderVariables(englishVariables));
            $('#saveTemplate').click(saveTemplate);
            $('#viewTemplates').click(showViewMode);
            $('#backToEdit').click(showEditMode);
            $('#closePreview').click(() => $('#previewModal').hide());
        });
    </script>
