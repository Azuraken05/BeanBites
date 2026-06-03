@extends('layouts.app')

@section('title', 'Reports - Bean & Bites POS')

@section('content')
<div class="reports-container">
    
    <div class="metrics-row">
        <div class="metric-card">
            <p class="metric-label">TODAY SALES</p>
            <h2 class="metric-value">₱0</h2>
            <p class="metric-subtext">0 Orders Today</p>
        </div>
        <div class="metric-card">
            <p class="metric-label">WEEK SALES</p>
            <h2 class="metric-value">₱0</h2>
            <p class="metric-subtext">0 Orders This Week</p>
        </div>
        <div class="metric-card">
            <p class="metric-label">TOTAL SALES</p>
            <h2 class="metric-value">₱0</h2>
            <p class="metric-subtext">Overall Sales</p>
        </div>
    </div>

    <div class="reports-middle-grid">
        <div class="chart-card analytics-main-card">
            <div class="chart-header">
                <h3>SALES ANALYTICS</h3>
                <div class="chart-toggle-buttons">
                    <button class="btn-toggle active" id="btnReportWeek">Week</button>
                    <button class="btn-toggle" id="btnReportMonth">Month</button>
                </div>
            </div>
            
            <div class="bar-chart-visualization-empty">
                <div class="empty-chart-fallback-text" id="reportChartText">No Sales Recorded Yet</div>
                <div class="bar-container-wireframe" id="reportBarContainer">
                    <div class="mock-bar" style="height: 45%"></div>
                    <div class="mock-bar" style="height: 25%"></div>
                    <div class="mock-bar" style="height: 65%"></div>
                    <div class="mock-bar" style="height: 40%"></div>
                    <div class="mock-bar" style="height: 55%"></div>
                    <div class="mock-bar" style="height: 35%"></div>
                    <div class="mock-bar" style="height: 15%"></div>
                </div>
            </div>
            
            <div class="chart-days-axis" id="chartDaysAxis">
                <span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span><span>SUN</span>
            </div>
        </div>

        <div class="chart-card transactions-feed-card">
            <div class="chart-header">
                <h3>TRANSACTIONS</h3>
                <div class="chart-toggle-buttons">
                    <button class="btn-toggle active" id="btnFeedDaily">Daily</button>
                    <button class="btn-toggle" id="btnFeedWeekly">Weekly</button>
                </div>
            </div>
            
            <div class="transactions-log-wrapper" id="transactionsLogWrapper">
                <div class="empty-chart-fallback-text static-msg">No transactions log records found</div>
            </div>
        </div>
    </div>

    <div class="low-stock-alert-card">
        <div class="low-stock-header">
            <h3>LOW STOCK ALERT</h3>
        </div>
        <div class="low-stock-list-frame">
            <div class="empty-chart-fallback-text static-msg">All products are currently well stocked.</div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Chart Toggle Selectors
        const btnWeek = document.getElementById('btnReportWeek');
        const btnMonth = document.getElementById('btnReportMonth');
        const chartText = document.getElementById('reportChartText');
        const barContainer = document.getElementById('reportBarContainer');
        const axisLine = document.getElementById('chartDaysAxis');

        // Feed Toggle Selectors
        const btnDaily = document.getElementById('btnFeedDaily');
        const btnWeekly = document.getElementById('btnFeedWeekly');
        const feedContainer = document.getElementById('transactionsLogWrapper');

        // Weekly vs Monthly graph setups
        const weekBars = ['45%', '25%', '65%', '40%', '55%', '35%', '15%'];
        const monthBars = ['30%', '50%', '15%', '65%', '40%', '70%', '25%', '85%', '45%', '60%'];

        function setBars(heights) {
            barContainer.innerHTML = '';
            heights.forEach(h => {
                const b = document.createElement('div');
                b.className = 'mock-bar';
                b.style.height = h;
                barContainer.appendChild(b);
            });
        }

        btnWeek.addEventListener('click', () => {
            btnWeek.classList.add('active');
            btnMonth.classList.remove('active');
            chartText.textContent = "No Sales Recorded Yet";
            axisLine.style.opacity = "1";
            setBars(weekBars);
        });

        btnMonth.addEventListener('click', () => {
            btnMonth.classList.add('active');
            btnWeek.classList.remove('active');
            chartText.textContent = "No Data Available for this Month";
            axisLine.style.opacity = "0"; // Hide days axis on monthly look
            setBars(monthBars);
        });

        // Feed Switch Logic
        btnDaily.addEventListener('click', () => {
            btnDaily.classList.add('active');
            btnWeekly.classList.remove('active');
            feedContainer.innerHTML = '<div class="empty-chart-fallback-text static-msg">No transactions log records found</div>';
        });

        btnWeekly.addEventListener('click', () => {
            btnWeekly.classList.add('active');
            btnDaily.classList.remove('active');
            feedContainer.innerHTML = '<div class="empty-chart-fallback-text static-msg">No weekly transaction history found</div>';
        });
    });
</script>
@endsection