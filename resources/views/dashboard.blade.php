@extends('layouts.app')

@section('title', 'Dashboard - Bean & Bites POS')

@section('content')
<div class="dashboard-container">
    
    <div class="metrics-row">
        <div class="metric-card">
            <p class="metric-label">ORDER PROCESSED</p>
            <h2 class="metric-value">0</h2>
        </div>
        <div class="metric-card">
            <p class="metric-label">TOTAL BALANCE</p>
            <h2 class="metric-value">₱0</h2>
        </div>
        <div class="metric-card">
            <p class="metric-label">TOTAL PROFIT</p>
            <h2 class="metric-value">₱0</h2>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card main-bar-chart">
            <div class="chart-header">
                <h3>SALES OVERVIEW</h3>
                <div class="chart-toggle-buttons">
                    <button class="btn-toggle active" id="btnWeekly">Weekly</button>
                    <button class="btn-toggle" id="btnMonthly">Monthly</button>
                </div>
            </div>
            <div class="bar-chart-visualization-empty">
                <div class="empty-chart-fallback-text" id="chartFallbackText">No Sales Registered Yet</div>
                <div class="bar-container-wireframe" id="chartBarContainer">
                    <div class="mock-bar" style="height: 15%"></div>
                    <div class="mock-bar" style="height: 25%"></div>
                    <div class="mock-bar" style="height: 45%"></div>
                    <div class="mock-bar" style="height: 30%"></div>
                    <div class="mock-bar" style="height: 40%"></div>
                    <div class="mock-bar" style="height: 20%"></div>
                    <div class="mock-bar" style="height: 55%"></div>
                    <div class="mock-bar" style="height: 50%"></div>
                    <div class="mock-bar" style="height: 60%"></div>
                    <div class="mock-bar" style="height: 10%"></div>
                </div>
            </div>
        </div>

        <div class="chart-card category-pie-card">
            <h3>CATEGORY TRANSACTIONS</h3>
            <div class="pie-chart-wrapper">
                <div class="empty-pie-circle">
                    <div class="pie-center-content">
                        <span class="pie-percentage">0%</span>
                        <div class="pie-fallback-note">No Transactions Tracked</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-grid-layout">
        <div class="products-snapshot-card">
            <div class="section-badge">AVAILABLE PRODUCT</div>
            <div class="products-list-table-wireframe">
                <div class="empty-chart-fallback-text" style="position: static; padding: 20px 0;">
                    No Products Available Yet
                </div>
            </div>
        </div>

        <div class="customer-count-card">
            <p class="customer-label">TOTAL CUSTOMER</p>
            <h2 class="customer-value">0</h2>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnWeekly = document.getElementById('btnWeekly');
        const btnMonthly = document.getElementById('btnMonthly');
        const chartText = document.getElementById('chartFallbackText');
        const barContainer = document.getElementById('chartBarContainer');

        // Mock heights array configurations for active visual changes
        const weeklyHeights = ['15%', '25%', '45%', '30%', '40%', '20%', '55%', '50%', '60%', '10%'];
        const monthlyHeights = ['40%', '55%', '20%', '70%', '35%', '60%', '25%', '80%', '45%', '50%'];

        function updateBars(heightsArray) {
            const bars = barContainer.querySelectorAll('.mock-bar');
            bars.forEach((bar, index) => {
                if(heightsArray[index]) {
                    bar.style.height = heightsArray[index];
                }
            });
        }

        // Click Handler for the Weekly Data Configuration Tab
        btnWeekly.addEventListener('click', () => {
            btnWeekly.classList.add('active');
            btnMonthly.classList.remove('active');
            chartText.textContent = "No Sales Registered Yet";
            updateBars(weeklyHeights);
        });

        // Click Handler for the Monthly Data Configuration Tab
        btnMonthly.addEventListener('click', () => {
            btnMonthly.classList.add('active');
            btnWeekly.classList.remove('active');
            chartText.textContent = "No Sales Registered for this Month";
            updateBars(monthlyHeights);
        });
    });
</script>
@endsection