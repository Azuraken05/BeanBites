/**
 * Bean & Bites POS - Terminal Interaction & Cart Subsystem Engine
 */

let liveDatabase = [];
let currentCartState = {};
let activeSelectedProduct = null;
let currentSpinnerCount = 1;
let checkoutUrl = '/pos/checkout';
let csrfTokenStr = '';

// NEW: Payment Flow Variables
let selectedPaymentMethod = null;
let cashAmountReceived = 0;

/**
 * Initializes the POS Engine with data models from the backend
 * @param {Array} productsData - The live items dataset array map
 * @param {String} token - Application context security CSRF token
 * @param {String} submitUrl - Custom checkout endpoint URI route mapping
 */
function initializePOSEngine(productsData, token, submitUrl) {
    liveDatabase = productsData || [];
    csrfTokenStr = token || '';
    if (submitUrl) checkoutUrl = submitUrl;

    // Trigger initial structural render
    filterCategory('Drinks');
}

/**
 * Render and Filter Product Grid Matrix matching live database structures
 * @param {String} categoryName - Target category name to highlight
 */
function filterCategory(categoryName) {
    // Clear global search query string when a user explicitly changes tab scopes
    const searchInput = document.getElementById('posSearchInput');
    if (searchInput) searchInput.value = '';

    // Toggle Active Button Styles
    const tabs = document.querySelectorAll('.btn-category');
    tabs.forEach(tab => {
        if (tab.textContent.trim() === categoryName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    // Hydrate data streams based on selection
    renderProductGrid(liveDatabase.filter(item => item.category === categoryName));
}

/**
 * Global Query Search Subsystem Filter Engine
 */
function searchAndFilterProducts() {
    const searchInput = document.getElementById('posSearchInput');
    if (!searchInput) return;

    const queryText = searchInput.value.toLowerCase().trim();
    
    // SAFE FALLBACK: If search field is empty, return straight back to active category items
    if (queryText === '') {
        const activeTab = document.querySelector('.btn-category.active');
        const activeCategory = activeTab ? activeTab.textContent.trim() : 'Drinks';
        renderProductGrid(liveDatabase.filter(item => item.category === activeCategory));
        return;
    }

    // GLOBAL SEARCH MODE: Overrule tab groupings entirely and filter across the complete menu layout array
    const matchedItems = liveDatabase.filter(item => {
        return item.name.toLowerCase().includes(queryText);
    });

    renderProductGrid(matchedItems);
}

/**
 * Modularized function to handle compiling layout cards dynamically
 * @param {Array} itemsList - Filtered array list data feed maps
 */
function renderProductGrid(itemsList) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    grid.innerHTML = '';

    // Output structural empty placeholder if search query rules mismatch records arrays
    if (itemsList.length === 0) {
        grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; color: #a39589; font-style: italic; font-size: 0.85rem; padding: 40px 0;">No matching items found.</div>`;
        return;
    }

    itemsList.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-item-clickable-card';
        
        // Check if tracked stock level has reached 0
        if (product.stock !== null && product.stock <= 0) {
            productCard.style.opacity = '0.4';
            productCard.style.cursor = 'not-allowed';
            productCard.onclick = () => alert(`"${product.name}" is out of stock!`);
        } else {
            productCard.onclick = () => openQuantityModal(product);
        }
        
        let imageMarkup = product.image_path 
            ? `<img src="/storage/${product.image_path}" class="pos-card-img" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">`
            : `<span>Image Here</span>`;

        // Append dynamic stock context layout label notice text metrics below card tag element fields
        let stockNoticeText = '';
        if (product.stock !== null) {
            stockNoticeText = product.stock <= 0 
                ? `<span style="display:block; font-size:11px; color:#ff6b6b; font-weight:600; margin-top:4px;">OUT OF STOCK</span>`
                : `<span style="display:block; font-size:11px; color:#baa495; margin-top:4px;">Stock: ${product.stock}</span>`;
        } else {
            stockNoticeText = `<span style="display:block; font-size:11px; color:#baa495; font-style:italic; margin-top:4px;">Infinite</span>`;
        }

        productCard.innerHTML = `
            <div class="product-mock-image-frame">${imageMarkup}</div>
            <p class="product-card-title-text">${product.name}</p>
            <p class="product-card-price-tag">₱${parseFloat(product.price).toFixed(2)}</p>
            ${stockNoticeText}
        `;
        grid.appendChild(productCard);
    });
}

/**
 * Quantity Spinner Controls Logic Engine
 * @param {Object} product - Database model context instance object data map 
 */
function openQuantityModal(product) {
    activeSelectedProduct = product;
    currentSpinnerCount = 1;
    
    const titleEl = document.getElementById('modalProductTitle');
    const valEl = document.getElementById('spinnerQuantityValue');
    const modalEl = document.getElementById('quantityModal');

    if (titleEl) titleEl.textContent = product.name.toUpperCase();
    if (valEl) valEl.textContent = currentSpinnerCount;
    if (modalEl) modalEl.classList.add('active');
}

function closeQuantityModal() {
    const modalEl = document.getElementById('quantityModal');
    if (modalEl) modalEl.classList.remove('active');
    activeSelectedProduct = null;
}

/**
 * Adjust counter elements and crosscheck safe stock levels boundaries buffers
 * @param {Number} value - Increment step step integer value modification rule
 */
function adjustSpinner(value) {
    currentSpinnerCount += value;
    if (currentSpinnerCount < 1) currentSpinnerCount = 1;
    
    if (activeSelectedProduct && activeSelectedProduct.stock !== null) {
        let existingCartQty = currentCartState[activeSelectedProduct.id] ? currentCartState[activeSelectedProduct.id].quantity : 0;
        let availableLimit = activeSelectedProduct.stock - existingCartQty;

        if (currentSpinnerCount > availableLimit) {
            currentSpinnerCount = availableLimit;
            if (availableLimit <= 0) currentSpinnerCount = 1;
            alert(`Insufficient stock levels. Maximum remaining quantity available to add is ${availableLimit}.`);
        }
    }

    const valEl = document.getElementById('spinnerQuantityValue');
    if (valEl) valEl.textContent = currentSpinnerCount;
}

/**
 * State Cart Calculations Engine Subsystem
 */
function confirmAddToCart() {
    if (!activeSelectedProduct) return;
    
    const productId = activeSelectedProduct.id;
    let existingQty = currentCartState[productId] ? currentCartState[productId].quantity : 0;
    let finalQuantityAttempt = existingQty + currentSpinnerCount;

    // Prevent adding to cart if item state attempt exceeds database parameters constraints
    if (activeSelectedProduct.stock !== null && finalQuantityAttempt > activeSelectedProduct.stock) {
        alert(`Cannot complete action. Stock limit reached. You already have x${existingQty} of this item in your cart.`);
        return;
    }

    if (currentCartState[productId]) {
        currentCartState[productId].quantity = finalQuantityAttempt;
    } else {
        currentCartState[productId] = {
            name: activeSelectedProduct.name,
            price: parseFloat(activeSelectedProduct.price),
            quantity: currentSpinnerCount
        };
    }
    
    closeQuantityModal();
    renderCartDashboardSubsystem();
}

/**
 * Completely drop specific index targets out of your local session buffer array
 * @param {Number} productId - Product unique numerical row model reference identity 
 */
function removeCartItem(productId) {
    delete currentCartState[productId];
    renderCartDashboardSubsystem();
}

function renderCartDashboardSubsystem() {
    const listContainer = document.getElementById('cartItemsList');
    const emptyNotice = document.getElementById('emptyCartNotice');
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (!listContainer) return;
    
    // Clear old rows except the fallback element
    const rows = listContainer.querySelectorAll('.cart-item-row-entry');
    rows.forEach(row => row.remove());

    const keys = Object.keys(currentCartState);
    let calculatedItemsCount = 0;
    let calculatedSubtotal = 0;

    if (keys.length === 0) {
        if (emptyNotice) emptyNotice.style.display = 'block';
        if (checkoutBtn) checkoutBtn.disabled = true;
    } else {
        if (emptyNotice) emptyNotice.style.display = 'none';
        if (checkoutBtn) checkoutBtn.disabled = false;

        keys.forEach(id => {
            const item = currentCartState[id];
            calculatedItemsCount += item.quantity;
            calculatedSubtotal += (item.price * item.quantity);

            const itemRow = document.createElement('div');
            itemRow.className = 'cart-item-row-entry';
            itemRow.innerHTML = `
                <div class="item-name-meta">
                    <strong>${item.name}</strong>
                    <span class="qty-badge-pill">x${item.quantity}</span>
                </div>
                <div class="item-actions-meta">
                    <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                    <button class="btn-remove-cart-item" onclick="removeCartItem(${id})"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            `;
            if (emptyNotice) {
                listContainer.insertBefore(itemRow, emptyNotice);
            } else {
                listContainer.appendChild(itemRow);
            }
        });
    }

    // Output Live Values onto Dashboard Panel Interface
    const countEl = document.getElementById('summaryItemCount');
    const subtotalEl = document.getElementById('summarySubtotal');
    const totalEl = document.getElementById('summaryTotal');

    if (countEl) countEl.textContent = calculatedItemsCount;
    if (subtotalEl) subtotalEl.textContent = `₱${calculatedSubtotal.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `₱${calculatedSubtotal.toFixed(2)}`;
}

/**
 * Receipt Invoice Formatting & Processing System
 */
function openReceiptModal() {
    const invoiceFrame = document.getElementById('receiptInvoiceItems');
    if (!invoiceFrame) return;

    invoiceFrame.innerHTML = '';
    let totalSum = 0;

    Object.keys(currentCartState).forEach(id => {
        const item = currentCartState[id];
        totalSum += (item.price * item.quantity);
        const lineRow = document.createElement('div');
        lineRow.className = 'receipt-invoice-line';
        lineRow.innerHTML = `<span>${item.name} x${item.quantity}</span><span>₱${(item.price * item.quantity).toFixed(2)}</span>`;
        invoiceFrame.appendChild(lineRow);
    });

    const totalValEl = document.getElementById('receiptTotalValue');
    const cashTotalEl = document.getElementById('cashTotalDisplay');
    if (totalValEl) totalValEl.textContent = `₱${totalSum.toFixed(2)}`;
    if (cashTotalEl) cashTotalEl.textContent = `₱${totalSum.toFixed(2)}`;

    // Always start at step 1
    showPaymentStep('stepPaymentMethod');

    // Reset to Cash
    document.querySelectorAll('.payment-option').forEach(l => l.classList.remove('selected'));
    const defaultPay = document.querySelector('input[name="payment_method"][value="cash"]');
    if (defaultPay) { defaultPay.checked = true; defaultPay.closest('.payment-option').classList.add('selected'); }

    // Reset cash fields
    const cashInput = document.getElementById('cashAmountInput');
    if (cashInput) cashInput.value = '';
    const changeEl = document.getElementById('cashChangeDisplay');
    if (changeEl) changeEl.textContent = '₱0';

    // Reset bank fields
    const bankSelect = document.getElementById('bankSelectInput');
    if (bankSelect) bankSelect.selectedIndex = 0;
    const bankRef = document.getElementById('bankReferenceInput');
    if (bankRef) bankRef.value = '';

    const modalEl = document.getElementById('receiptModal');
    if (modalEl) modalEl.classList.add('active');
}

function closeReceiptModal() {
    const modalEl = document.getElementById('receiptModal');
    if (modalEl) modalEl.classList.remove('active');
}

/**
 * Process checkout details through dynamic AJAX submission right upon printing receipts
 */
function triggerHardwarePrint() {
    const countEl = document.getElementById('summaryItemCount');
    const subtotalEl = document.getElementById('summarySubtotal');

    let calculatedItemsCount = countEl ? parseInt(countEl.textContent) : 0;
    let calculatedSubtotal = subtotalEl ? parseFloat(subtotalEl.textContent.replace('₱', '')) : 0.00;

const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

    let payloadData = {
        cart: currentCartState,
        total_amount: calculatedSubtotal,
        total_items: calculatedItemsCount,
        payment_method: selectedPayment,
        _token: csrfTokenStr
    };

    fetch(checkoutUrl, {
        method: 'POST',
        body: JSON.stringify(payloadData),
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update local JavaScript memory cache layer properties inside liveDatabase array collection
            Object.keys(currentCartState).forEach(id => {
                let matchingDbProduct = liveDatabase.find(p => p.id == id);
                if (matchingDbProduct && matchingDbProduct.stock !== null) {
                    matchingDbProduct.stock -= currentCartState[id].quantity;
                }
            });

            // Launch browser driver printing subsystem panel layout options natively
            window.print();

            // Clear out terminal state arrays cleanly
            currentCartState = {};
            renderCartDashboardSubsystem();
            closeReceiptModal();

            // Instantly re-render the view profile matrix to visually lock newly sold out products cards
            const activeTab = document.querySelector('.btn-category.active');
            filterCategory(activeTab ? activeTab.textContent.trim() : 'Drinks');
        } else {
            alert('Checkout Transaction Failure: ' + data.message);
        }
    })
    .catch(err => {
        console.error('System error sending checkout bundle maps:', err);
        alert('Could not complete checkout execution. Server database down.');
    });
}
/**
 * Payment Method Selector Highlight Engine
 */
function highlightPayment(radio) {
    document.querySelectorAll('.payment-option').forEach(label => {
        label.classList.remove('selected');
    });
    radio.closest('.payment-option').classList.add('selected');
}

function showPaymentStep(stepId) {
    const steps = ['stepPaymentMethod', 'stepCashPayment', 'stepGcashPayment', 'stepBankPayment', 'stepReceiptBody'];
    steps.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
    const target = document.getElementById(stepId);
    if (target) target.style.display = 'block';
}

function proceedToPaymentDetails() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
    if (selected === 'cash') showPaymentStep('stepCashPayment');
    else if (selected === 'gcash') showPaymentStep('stepGcashPayment');
    else if (selected === 'bank_transfer') showPaymentStep('stepBankPayment');
}

function backToPaymentMethod() {
    showPaymentStep('stepPaymentMethod');
}

function computeCashChange() {
    const totalText = document.getElementById('cashTotalDisplay')?.textContent.replace('₱', '') || '0';
    const total = parseFloat(totalText);
    const given = parseFloat(document.getElementById('cashAmountInput')?.value) || 0;
    const change = given - total;
    const changeEl = document.getElementById('cashChangeDisplay');
    if (changeEl) changeEl.textContent = `₱${change >= 0 ? change.toFixed(2) : '0.00'}`;
}

function confirmPaymentAndShowReceipt() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

    if (selected === 'cash') {
        const total = parseFloat(document.getElementById('cashTotalDisplay')?.textContent.replace('₱', '')) || 0;
        const given = parseFloat(document.getElementById('cashAmountInput')?.value) || 0;
        if (given < total) {
            alert('Cash amount is less than the total. Please enter a sufficient amount.');
            return;
        }
        const change = given - total;

        // Show cash details on receipt
        const cashLine = document.getElementById('receiptCashLine');
        const changeLine = document.getElementById('receiptChangeLine');
        if (cashLine) { cashLine.textContent = `Cash: ₱${given.toFixed(2)}`; cashLine.style.display = 'block'; }
        if (changeLine) { changeLine.textContent = `Change: ₱${change.toFixed(2)}`; changeLine.style.display = 'block'; }
    } else {
        // Hide cash lines for non-cash payments
        const cashLine = document.getElementById('receiptCashLine');
        const changeLine = document.getElementById('receiptChangeLine');
        if (cashLine) cashLine.style.display = 'none';
        if (changeLine) changeLine.style.display = 'none';
    }

    if (selected === 'bank_transfer') {
        const bank = document.getElementById('bankSelectInput')?.value;
        const ref = document.getElementById('bankReferenceInput')?.value.trim();
        if (!bank) { alert('Please select a bank.'); return; }
        if (!ref) { alert('Please enter a reference number.'); return; }
    }

    const labels = { cash: '💵 Cash', gcash: '📱 GCash', bank_transfer: '🏦 Bank Transfer' };
    const paymentLine = document.getElementById('receiptPaymentMethodLine');
    if (paymentLine) paymentLine.textContent = `Payment: ${labels[selected] || selected}`;

    showPaymentStep('stepReceiptBody');
}