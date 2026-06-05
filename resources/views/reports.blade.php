@extends('layouts.app')

@section('title', 'Reports - Bean & Bites POS')

@section('content')
<!-- Core Layout Stylesheet Link -->
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">

<div class="reports-container">

    <!-- 1. SUMMARY CARDS BANNER ROW -->
    <div class="metrics-dashboard-row">
        <!-- Today Sales Card -->
        <div class="metric-card-box">
            <small>TODAY SALES</small>
            <h2>₱{{ number_format($todaySales, 0) }}</h2>
            <span>{{ $todayOrdersCount }} Orders Today</span>
        </div>

        <!-- Week Sales Card -->
        <div class="metric-card-box">
            <small>WEEK SALES</small>
            <h2>₱{{ number_format($weekSales, 0) }}</h2>
            <span>{{ $weekOrdersCount }} Orders This Week</span>
        </div>

        <!-- Total Sales Overall Card -->
        <div class="metric-card-box">
            <small>TOTAL SALES</small>
            <h2>₱{{ number_format($totalSalesOverall, 0) }}</h2>
            <span>Overall Sales</span>
        </div>
    </div>

    <!-- 2. ANALYTICS CHART MATRIX & TRANSACTION HISTORY SPLIT BLOCK -->
    <div class="analytics-log-split-row">
        
        <!-- Left Column Module: Sales Analytics Bar Graph Card Box -->
        <div class="chart-wrapper-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3>SALES ANALYTICS</h3>
                
                <!-- Toggle Controls Tab Links for Weekly and Monthly Chart Ranges -->
                <div class="toggle-pill-container">
                    <button class="btn-toggle-switch chart-toggle active" onclick="toggleAnalyticsView('week', this)">Week</button>
                    <button class="btn-toggle-switch chart-toggle" onclick="toggleAnalyticsView('month', this)">Month</button>
                </div>
            </div>
            
            <!-- Graphic Chart Pillars Dynamic Root View Renderers -->
            <div class="chart-viewport">
                
                <!-- WEEK ANALYTICS CORE BLUEPRINT (MON-SUN) -->
                <div id="chartViewWeek" class="chart-data-wrapper">
                    @php $maxWeekVal = max($weeklyChartData) > 0 ? max($weeklyChartData) : 1; @endphp
                    @foreach($weeklyChartData as $dayIdx => $amount)
                        @php $barHeight = ($amount / $maxWeekVal) * 100; @endphp
                        <div class="chart-column-node">
                            <span class="amount-label">{{ $amount > 0 ? '₱'.number_format($amount/1000, 1).'K' : '' }}</span>
                            <div class="chart-pillar-bar" style="height: {{ $amount > 0 ? $barHeight : 10 }}%;"></div>
                            <span class="axis-label">{{ $daysOfWeek[$dayIdx] }}</span>
                        </div>
                    @endforeach
                    @if(max($weeklyChartData) == 0)
                        <div class="empty-chart-watermark">No Sales Recorded Yet This Week</div>
                    @endif
                </div>

                <!-- MONTH ANALYTICS CORE BLUEPRINT (W1-W5) -->
                <div id="chartViewMonth" class="chart-data-wrapper" style="display: none;">
                    @php $maxMonthVal = max($monthlyChartData) > 0 ? max($monthlyChartData) : 1; @endphp
                    @foreach($monthlyChartData as $weekIdx => $amount)
                        @php $barHeight = ($amount / $maxMonthVal) * 100; @endphp
                        <div class="chart-column-node">
                            <span class="amount-label">{{ $amount > 0 ? '₱'.number_format($amount/1000, 1).'K' : '' }}</span>
                            <div class="chart-pillar-bar" style="height: {{ $amount > 0 ? $barHeight : 10 }}%;"></div>
                            <span class="axis-label">{{ $monthlyChartLabels[$weekIdx] }}</span>
                        </div>
                    @endforeach
                    @if(max($monthlyChartData) == 0)
                        <div class="empty-chart-watermark">No Sales Recorded Yet This Month</div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Right Column Module: Transactions History Dynamic Logger List Box -->
        <div class="transactions-wrapper-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-shrink: 0;">
                <h3>TRANSACTIONS</h3>
                
                <div class="toggle-pill-container">
                    <button class="btn-toggle-switch tx-toggle active" onclick="toggleTransactionsLog('daily', this)">Daily</button>
                    <button class="btn-toggle-switch tx-toggle" onclick="toggleTransactionsLog('weekly', this)">Weekly</button>
                </div>
            </div>
            
            <div class="transactions-scroll-viewport">
                <div id="txLogContainer">
                    <!-- Script dynamically appends list layout items inside this view track -->
                </div>
            </div>
        </div>
    </div>

    <!-- 3. LOW STOCK & DELETED PRODUCTS TRACKER SPLIT ROW -->
    <div class="bottom-trackers-row">
        
        <!-- Left Sub-Box: Low Stock Monitor Alarm Container Panel -->
        <div class="low-stock-alert-panel">
            <h3>LOW STOCK ALERT</h3>
            <div class="inner-tracker-viewport">
                @forelse($lowStockProducts as $lowItem)
                    <div class="low-stock-row">
                        <span class="low-stock-name">{{ $lowItem->name }} (<span class="low-stock-category">{{ $lowItem->category }}</span>)</span>
                        @if($lowItem->stock == 0)
                            <span class="badge-alert-status sold-out">SOLD OUT</span>
                        @else
                            <span class="badge-alert-status critical">CRITICAL STOCK: {{ $lowItem->stock }} left</span>
                        @endif
                    </div>
                @empty
                    <div class="empty-trackers-msg">All products are currently well stocked.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Sub-Box: Deleted Product Audit Transaction Recorder Panel -->
        <div class="deleted-products-audit-panel">
            <h3>DELETED PRODUCT TRACKER</h3>
            <div class="inner-tracker-viewport">
                @forelse($deletedProducts as $trashedItem)
                    <div class="audit-row-entry">
                        <div class="audit-header-line">
                            <span class="audit-product-name">{{ $trashedItem->name }}</span>
                            <span class="badge-audit-user">
                                Removed by: {{ $trashedItem->deletedBy ? $trashedItem->deletedBy->name : 'Unknown User' }}
                            </span>
                        </div>
                        <div class="audit-footer-line">
                            <span class="audit-reason-text">
                                Reason: "{{ $trashedItem->delete_remarks ?? 'No explicit explanation supplied' }}"
                            </span>
                            <small class="audit-timestamp">
                                {{ $trashedItem->deleted_at ? $trashedItem->deleted_at->format('M d, Y h:i A') : '' }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="empty-trackers-msg">No deleted history logs tracked inside current parameters bounds.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Core Operations Modular Script Link -->
<script src="{{ asset('js/reports.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Hydrate data structures array collections instantly into active cache memory
        initializeReportsEngine(
            @json($dailyTransactions),
            @json($weeklyTransactions)
        );
    });
</script>
@endsection