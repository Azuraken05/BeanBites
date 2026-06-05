@extends('layouts.app')

@section('title', 'Dashboard - Bean & Bites POS')

@section('content')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="dashboard-container">

    <div class="metrics-summary-banner-row">
        <div class="summary-metric-card">
            <small>ORDER PROCESSED</small>
            <h2>{{ $ordersProcessedCount }}</h2>
            <span>Catered Checkout Items</span>
        </div>

        <div class="summary-metric-card">
            <small>TOTAL BALANCE</small>
            <h2>₱0</h2>
            <span>Account Balance Logs</span>
        </div>

        <div class="summary-metric-card">
            <small>TOTAL PROFIT</small>
            <h2>₱{{ number_format($totalProfitOverall, 2) }}</h2>
            <span>Cumulative Sales Yield</span>
        </div>
    </div>

    <div class="main-charts-split-row">
        
        <div class="overview-graph-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>SALES OVERVIEW</h3>
                <div class="toggle-pill-container">
                    <button class="btn-toggle-switch btn-toggle active" onclick="toggleOverviewTimelineView('weekly', this)">Weekly</button>
                    <button class="btn-toggle-switch btn-toggle" onclick="toggleOverviewTimelineView('monthly', this)">Monthly</button>
                </div>
            </div>

            <div class="chart-viewport">
                <div id="weeklySalesTimelineBlock" class="chart-pillar-timeline-viewport">
                    @php $maxW = max($weeklySalesOverview) > 0 ? max($weeklySalesOverview) : 1; @endphp
                    @foreach($weeklySalesOverview as $idx => $val)
                        <div class="timeline-column-node">
                            <span class="val-tag">{{ $val > 0 ? '₱'.number_format($val/1000, 1).'K' : '' }}</span>
                            <div class="bar-fill" style="height: {{ $val > 0 ? ($val / $maxW) * 100 : 8 }}%;"></div>
                            <span class="lbl-tag">{{ $daysOfWeekLabels[$idx] }}</span>
                        </div>
                    @endforeach
                    @if(max($weeklySalesOverview) == 0)
                        <div class="empty-chart-watermark" id="chartFallbackText">No Sales Recorded Yet This Week</div>
                    @endif
                </div>

                <div id="monthlySalesTimelineBlock" class="chart-pillar-timeline-viewport" style="display: none;">
                    @php $maxM = max($monthlySalesOverview) > 0 ? max($monthlySalesOverview) : 1; @endphp
                    @foreach($monthlySalesOverview as $idx => $val)
                        <div class="timeline-column-node">
                            <span class="val-tag">{{ $val > 0 ? '₱'.number_format($val/1000, 1).'K' : '' }}</span>
                            <div class="bar-fill" style="height: {{ $val > 0 ? ($val / $maxM) * 100 : 8 }}%;"></div>
                            <span class="lbl-tag">W{{ $idx + 1 }}</span>
                        </div>
                    @endforeach
                    @if(max($monthlySalesOverview) == 0)
                        <div class="empty-chart-watermark">No Sales Registered for this Month</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="category-pie-card">
            <h3 style="align-self: flex-start;">CATEGORY TRANSACTIONS</h3>
            
            <div class="pie-graph-flex-box">
                <div class="concentric-donut-wheel-frame" id="concentricCategoryDonutRing">
                    <div class="donut-inner-white-core">
                        @php 
                            // Determine which category holds the highest transaction value
                            $topCategorySharePercentage = '0%';
                            if($categoryPercentages['Drinks'] >= $categoryPercentages['Food'] && $categoryPercentages['Drinks'] >= $categoryPercentages['Desserts'] && $categoryPercentages['Drinks'] > 0) $topCategorySharePercentage = $categoryPercentages['Drinks'].'%';
                            elseif($categoryPercentages['Food'] >= $categoryPercentages['Drinks'] && $categoryPercentages['Food'] >= $categoryPercentages['Desserts'] && $categoryPercentages['Food'] > 0) $topCategorySharePercentage = $categoryPercentages['Food'].'%';
                            elseif($categoryPercentages['Desserts'] > 0) $topCategorySharePercentage = $categoryPercentages['Desserts'].'%';
                        @endphp
                        <h4>{{ $topCategorySharePercentage }}</h4>
                        <span>Top Category<br>Distribution</span>
                    </div>
                </div>

                <div class="category-legend-list-grid">
                    <div class="legend-pill-item"><div class="legend-dot-indicator" style="background-color: #2b170c;"></div>Drinks ({{ $categoryPercentages['Drinks'] }}%)</div>
                    <div class="legend-pill-item"><div class="legend-dot-indicator" style="background-color: #8c5a3c;"></div>Food ({{ $categoryPercentages['Food'] }}%)</div>
                    <div class="legend-pill-item"><div class="legend-dot-indicator" style="background-color: #baa495;"></div>Desserts ({{ $categoryPercentages['Desserts'] }}%)</div>
                </div>
            </div>
        </div>

    </div>

    <div class="bottom-dashboard-split-row">
        
        <div class="available-products-tracker-panel">
            <h3>AVAILABLE PRODUCT</h3>
            <div class="products-pill-scroll-viewport">
                @forelse($availableProducts as $prod)
                    <div class="product-horizontal-status-pill">
                        <strong>{{ $prod->name }}</strong>
                        <span style="background-color: rgba(255,255,255,0.15); padding: 2px 8px; border-radius: 6px; font-size: 11px;">
                            {!! $prod->stock !== null ? 'Stock: '.$prod->stock : 'Infinite' !!}
                        </span>
                    </div>
                @empty
                    <div style="font-size:0.85rem; color:#baa495; font-style:italic; padding:10px 0;">No active menu items available.</div>
                @endforelse
            </div>
        </div>

        <div class="summary-metric-card customer-count-card-override">
            <small>TOTAL CUSTOMER</small>
            <h2>{{ $totalCustomers }}</h2>
            <span>Total Unique Orders Tracked</span>
        </div>

    </div>

</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Hydrate baseline structures into script cache arrays
        initializeDashboardEngine(
            @json($weeklySalesOverview),
            @json($monthlySalesOverview)
        );

        // Compile category distribution ring slices dynamically
        renderCategoryDonutChartMetrics(
            {{ $categoryPercentages['Drinks'] }},
            {{ $categoryPercentages['Food'] }},
            {{ $categoryPercentages['Desserts'] }}
        );
    });
</script>
@endsection