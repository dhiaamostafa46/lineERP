@extends('layouts.app')

@section('title', __('lang.dashboard'))

@push('css')
    <link href="{{ asset('css/dashboard-premium.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid py-6">
        <!-- 1. Top Bar Filters & Quick Actions -->
        @livewire('dashboard.executive-top-filters', [], key('top-filters-comp'))

        <!-- 2. Top 6 Executive KPI Cards -->
        @livewire('dashboard.executive-kpis', [], key('kpis-comp'))

        <!-- 3. Sales & Financial Trend + Payment Donut + Top Branches + Top Products -->
        @livewire('dashboard.executive-main-charts', [], key('main-charts-comp'))

        <!-- 4. Action Required (Critical Alerts Panel - High Priority) -->
        @livewire('dashboard.executive-critical-alerts', [], key('critical-alerts-comp'))

        <!-- 5. Specialized Modules Sections -->
        <div id="module_section_inventory" class="executive-module-section mb-6">
            @livewire('dashboard.executive-inventory-panel', [], key('inventory-panel-comp'))
        </div>

        <div id="module_section_accounting" class="executive-module-section mb-6">
            @livewire('dashboard.executive-accounting-invoices-panel', [], key('accounting-panel-comp'))
        </div>

        <div id="module_section_pos" class="executive-module-section mb-6">
            @livewire('dashboard.executive-pos-operations-panel', [], key('pos-panel-comp'))
        </div>

        <div id="module_section_hr_fleet" class="executive-module-section mb-6">
            @livewire('dashboard.executive-hr-fleet-panel', [], key('hr-fleet-panel-comp'))
        </div>

        <!-- 6. Recent System Activity Stream -->
        <div id="module_section_activity" class="executive-module-section mb-6">
            @livewire('dashboard.executive-activity-stream', [], key('activity-stream-comp'))
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('executiveTabChanged', (tab) => {
                if (tab === 'all') {
                    $('.executive-module-section').show();
                } else if (tab === 'inventory') {
                    $('.executive-module-section').hide();
                    $('#module_section_inventory').show();
                } else if (tab === 'accounting') {
                    $('.executive-module-section').hide();
                    $('#module_section_accounting').show();
                } else if (tab === 'pos') {
                    $('.executive-module-section').hide();
                    $('#module_section_pos').show();
                } else if (tab === 'hr' || tab === 'fleet') {
                    $('.executive-module-section').hide();
                    $('#module_section_hr_fleet').show();
                } else if (tab === 'activity') {
                    $('.executive-module-section').hide();
                    $('#module_section_activity').show();
                }
            });

            // Auto-refresh charts when Livewire morphs the DOM after filter updates
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(() => {
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 100);
                });
            });
        });
    </script>
@endpush