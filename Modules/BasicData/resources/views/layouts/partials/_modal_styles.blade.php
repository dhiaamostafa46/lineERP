<style>
    .premium-modal-backdrop {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1050;
    }
    .premium-modal-dialog {
        z-index: 1055;
        animation: modalScaleUp 0.24s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalScaleUp {
        0% { opacity: 0; transform: scale(0.96) translateY(10px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .premium-modal-content {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 1.25rem;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
        background: #ffffff;
    }
    .segmented-tabs-wrapper {
        background: #f1f5f9;
        padding: 0.35rem;
        border-radius: 0.75rem;
        display: inline-flex;
        gap: 0.25rem;
    }
    .segmented-tab-btn {
        border: none;
        background: transparent;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    .segmented-tab-btn:hover {
        color: #1e293b;
    }
    .segmented-tab-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .modern-form-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
        display: block;
    }
    .modern-input {
        border: 1px solid #cbd5e1;
        border-radius: 0.625rem;
        padding: 0.6rem 0.85rem;
        font-size: 0.875rem;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.18s ease;
    }
    .modern-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
        background: #ffffff;
    }
    .modern-unit-card {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem;
        transition: all 0.2s ease;
    }
    .modern-unit-card:hover {
        border-color: #cbd5e1;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .btn-save-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 600;
        border-radius: 0.625rem;
        padding: 0.55rem 1.75rem;
        border: none;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
    }
    .btn-save-gradient:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
    }
</style>
