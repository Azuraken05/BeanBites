/**
 * Bean & Bites POS - Reports Dashboard Automation Engine
 * Handles dynamic UI state toggles and live client-side data rendering
 */

// Global state cache to hold dataset arrays injected at runtime
let dailyTxDataset = [];
let weeklyTxDataset = [];

/**
 * Initializes the reports subsystem with runtime database variables
 * @param {Array} dailyData - Grouped calendar date transaction totals
 * @param {Array} weeklyData - Grouped weekly date transaction totals
 */
function initializeReportsEngine(dailyData, weeklyData) {
    dailyTxDataset = dailyData || [];
    weeklyTxDataset = weeklyData || [];

    // Automatically trigger the default transaction listing view on load
    const defaultTxToggle = document.querySelector('.tx-toggle.active');
    toggleTransactionsLog('daily', defaultTxToggle);
}

/**
 * Toggles visibility between Weekly and Monthly Sales Analytics Chart Pillars
 * @param {String} viewMode - 'week' or 'month'
 * @param {HTMLElement} clickedButton - The toggle button element triggered
 */
function toggleAnalyticsView(viewMode, clickedButton) {
    // 1. Reset aesthetic state highlights on active toggles row
    document.querySelectorAll('.chart-toggle').forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.color = '#baa495';
    });
    
    // 2. Lock focus styles onto selected control option pill
    clickedButton.style.background = '#baa495';
    clickedButton.style.color = '#2b170c';

    // 3. Swap chart wrapper view container blocks instantly
    const weekChart = document.getElementById('chartViewWeek');
    const monthChart = document.getElementById('chartViewMonth');

    if (viewMode === 'week') {
        if (weekChart) weekChart.style.display = 'flex';
        if (monthChart) monthChart.style.display = 'none';
    } else {
        if (weekChart) weekChart.style.display = 'none';
        if (monthChart) monthChart.style.display = 'flex';
    }
}

/**
 * Handles reconstructing transaction logging feeds on the fly based on selected timeframe fields
 * @param {String} viewMode - 'daily' or 'weekly'
 * @param {HTMLElement} clickedButton - The toggle button element triggered
 */
function toggleTransactionsLog(viewMode, clickedButton) {
    // 1. Clear highlight colors across active transaction tabs row
    document.querySelectorAll('.tx-toggle').forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.color = '#baa495';
    });
    
    // 2. Apply active focus colors to selected toggle component pill node
    if (clickedButton) {
        clickedButton.style.background = '#baa495';
        clickedButton.style.color = '#2b170c';
    }

    // 3. Target list frame parent element and drop old nodes out of memory buffers
    const targetContainer = document.getElementById('txLogContainer');
    if (!targetContainer) return;
    
    targetContainer.innerHTML = '';

    // 4. Select matching source matrix data block stream mapping
    const selectedDataset = (viewMode === 'daily') ? dailyTxDataset : weeklyTxDataset;

    // 5. Append clean dynamic layout fallback row if parameters contain zero records elements
    if (selectedDataset.length === 0) {
        targetContainer.innerHTML = `<div class="empty-logs-notice">No transaction log records found</div>`;
        return;
    }

    // 6. Loop database rows streams and reconstruct structural entry modules
    selectedDataset.forEach(log => {
        const row = document.createElement('div');
        row.className = 'transaction-row-entry';
        
        row.innerHTML = `
            <div class="tx-meta-info">
                <strong class="tx-title-text">${log.title}</strong>
                <small class="tx-subtitle-text">${log.subtitle}</small>
            </div>
            <div style="text-align: right;">
                <span class="tx-amount-display">+₱${log.amount}</span>
            </div>
        `;
        targetContainer.appendChild(row);
    });
}