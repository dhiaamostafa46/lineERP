<div>
    <div class="modal fade" id="reprocessAttendanceModal" tabindex="-1" aria-labelledby="reprocessAttendanceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg style-rounded-4 overflow-hidden" style="border-radius: 1rem;">
                <!-- Header -->
                <div class="modal-header bg-gradient-primary text-white p-4 border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="d-flex align-items-center">
                        <div class="p-3 bg-white-10 rounded-3 me-3 text-white" style="background: rgba(255, 255, 255, 0.1);">
                            <i class="fas fa-sync-alt fa-lg fa-spin" wire:loading wire:target="reprocess"></i>
                            <i class="fas fa-history fa-lg" wire:loading.remove wire:target="reprocess"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="reprocessAttendanceModalLabel">
                                {{ __('hr::models/hr_attendances.reprocess_title') ?? 'إعادة معالجة وتصحيح الحضور والانصراف' }}
                            </h5>
                            <small class="text-white-50">إعادة بناء سجلات التتبع وتقييم السياسات خلفياً</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="reprocess">
                    <div class="modal-body p-4 bg-light">

                        @if ($statusMessage)
                            <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm rounded-3">
                                <i class="fas fa-check-circle me-2 fa-lg"></i>
                                <div>{{ $statusMessage }}</div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="far fa-calendar-alt me-1"></i> من تاريخ
                                </label>
                                <input type="date" class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('start_date') is-invalid @enderror" wire:model.defer="start_date">
                                @error('start_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="far fa-calendar-check me-1"></i> إلى تاريخ
                                </label>
                                <input type="date" class="form-control form-control-lg border-0 shadow-sm rounded-3 @error('end_date') is-invalid @enderror" wire:model.defer="end_date">
                                @error('end_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Employee Filter (Optional) -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-secondary">
                                    <i class="fas fa-user-tie me-1"></i> تحديد الموظف (اختياري - اتركه فارغاً للجميع)
                                </label>
                                <select class="form-select form-select-lg border-0 shadow-sm rounded-3 @error('employee_id') is-invalid @enderror" wire:model.defer="employee_id">
                                    <option value="">-- جميع الموظفين --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Info Banner -->
                        <div class="p-3 mt-4 rounded-3 border-0 bg-white shadow-sm d-flex align-items-center">
                            <div class="me-3 text-info">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div class="small text-muted">
                                سيتم إرسال العملية إلى **الخلفية (Queue Job)** لضمان الاستكمال الشامل والتلقائي لجميع السجلات بدون حدوث انقطاع (HTTP Timeout).
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-white border-top-0 p-3 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-light btn-lg rounded-3 text-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 px-4 shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="reprocess">
                                <i class="fas fa-play me-1"></i> بدء إعادة المعالجة
                            </span>
                            <span wire:loading wire:target="reprocess">
                                <i class="fas fa-spinner fa-spin me-1"></i> جاري التشغيل...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
