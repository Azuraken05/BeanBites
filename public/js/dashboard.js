/**
 * Bean & Bites POS - Dashboard Component Automation Script
 * Handles real-time timeline switches and dynamic donut chart rendering
 */

// Global dataset state pointers
let weeklySalesOverviewData = [];
let monthlySalesOverviewData = [];

/**
 * Initializes the main dashboard state arrays using server-side runtime variables
 * @param {Array} weeklyData - Daily distribution totals for current week
 * @param {Array} monthlyData - Weekly distribution totals for current month
 */
function initializeDashboardEngine(weeklyData, monthlyData) {
    weeklySalesOverviewData = weeklyData || [];
    monthlySalesOverviewData = monthlyData || [];
}

/**
 * Handles switching timeline charts layout blocks view bounds instantly
 * @param {String} targetRange - 'weekly' or 'monthly'
 * @param {HTMLElement} activeBtn - Triggered click component context node 
 */
function toggleOverviewTimelineView(targetRange, activeBtn) {
    document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.classList.remove('active');
    });

    activeBtn.classList.add('active');

    const weekBlock = document.getElementById('weeklySalesTimelineBlock');
    const monthBlock = document.getElementById('monthlySalesTimelineBlock');
    const chartText = document.getElementById('chartFallbackText');

    if (targetRange === 'weekly') {
        if (weekBlock) weekBlock.style.display = 'flex';
        if (monthBlock) monthBlock.style.display = 'none';
        if (chartText) chartText.textContent = "Sales Performance (This Week)";
    } else {
        if (weekBlock) weekBlock.style.display = 'none';
        if (monthBlock) monthBlock.style.display = 'flex';
        if (chartText) chartText.textContent = "Sales Performance (This Month)";
    }
}

/**
 * Recalculates category transaction donut wheel graphic segments dynamically at runtime
 * @param {Number} drinksPct - Percentage share parameter
 * @param {Number} foodPct - Percentage share parameter
 * @param {Number} dessertsPct - Percentage share parameter
 */
function renderCategoryDonutChartMetrics(drinksPct, foodPct, dessertsPct) {
    const donutWidget = document.getElementById('concentricCategoryDonutRing');
    if (!donutWidget) return;

    const d = parseFloat(drinksPct) || 0;
    const f = parseFloat(foodPct) || 0;
    const s = parseFloat(dessertsPct) || 0;

    // Output raw neutral gray default color track if menu sales volume is flat
    if (d === 0 && f === 0 && s === 0) {
        donutWidget.style.background = '#e2d6c5';
        return;
    }

    // Compute relative degree arc boundaries coordinates vectors
    const drinksEndArc = d;
    const foodEndArc = drinksEndArc + f;

    // Render graphic layers utilizing conic gradients matching store branding aesthetic palette colors
    donutWidget.style.background = `conic-gradient(
        #2b170c 0% ${drinksEndArc}%, 
        #8c5a3c ${drinksEndArc}% ${foodEndArc}%, 
        #baa495 ${foodEndArc}% 100%
    )`;
}