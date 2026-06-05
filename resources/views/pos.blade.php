@extends('layouts.app')

@section('title', 'POS - Bean & Bites POS')

@section('content')
<div class="pos-container">
    
    <div class="menu-column">
        <div class="pos-card header-card">
            <h2>POINT OF SALE</h2>
        </div>

        <div class="products-container-box">
            <h3 class="section-title-tag">PRODUCTS</h3>
            
            <div class="category-tabs-bar">
                <button class="btn-category active" onclick="filterCategory('Drinks')">Drinks</button>
                <button class="btn-category" onclick="filterCategory('Food')">Food</button>
                <button class="btn-category" onclick="filterCategory('Desserts')">Desserts</button>
            </div>

            <div class="products-grid-layout" id="productsGrid">
                </div>
        </div>
    </div>

    <div class="cart-column">
        <div class="pos-card cart-wrapper-card">
            <h3>CART</h3>
            
            <div class="cart-items-list" id="cartItemsList">
                <div class="empty-cart-notice" id="emptyCartNotice">No items added to checkout yet.</div>
            </div>

            <div class="cart-summary-box">
                <div class="summary-row"><span>Items:</span><span id="summaryItemCount">0</span></div>
                <div class="summary-row"><span>Subtotal:</span><span id="summarySubtotal">₱0</span></div>
                <div class="summary-total"><span>TOTAL:</span><span id="summaryTotal">₱0</span></div>
            </div>

            <button class="btn-checkout" id="checkoutBtn" onclick="openReceiptModal()" disabled>Checkout</button>
        </div>
    </div>

    <div class="pos-modal-overlay" id="quantityModal">
        <div class="pos-modal-card">
            <h2 id="modalProductTitle">PRODUCT NAME</h2>
            
            <div class="spinner-control-row">
                <button class="btn-spinner" onclick="adjustSpinner(-1)"><i class="fa-solid fa-caret-left"></i></button>
                <span class="spinner-value" id="spinnerQuantityValue">1</span>
                <button class="btn-spinner" onclick="adjustSpinner(1)"><i class="fa-solid fa-caret-right"></i></button>
            </div>

            <button class="btn-modal-action action-save" onclick="confirmAddToCart()">Add To Cart</button>
            <button class="btn-modal-action action-cancel" onclick="closeQuantityModal()">Cancel</button>
        </div>
    </div>

    <div class="pos-modal-overlay" id="receiptModal">
        <div class="pos-modal-card receipt-card" id="printableReceiptArea">
            <h2>RECEIPT</h2>
            
            <div class="receipt-body-invoice" id="receiptInvoiceItems">
                </div>

            <div class="receipt-divider-line"></div>
            <div class="receipt-total-display">
                <span>Total:</span><span id="receiptTotalValue">₱0</span>
            </div>

            <button class="btn-modal-action action-save no-print" onclick="triggerHardwarePrint()">Print</button>
            <button class="btn-modal-action action-cancel no-print" onclick="closeReceiptModal()">Close</button>
        </div>
    </div>

</div>

<script>
    // 1. Initialized Dummy Databasing Records for testing filter controls
    const dummyDatabase = [
        { id: 1, name: 'Coke', category: 'Drinks', price: 25 },
        { id: 2, name: 'Milktea', category: 'Drinks', price: 85 },
        { id: 3, name: 'Fruit Tea', category: 'Drinks', price: 75 },
        { id: 4, name: 'Burger & Fries', category: 'Food', price: 120 },
        { id: 5, name: 'Chicken Poppers', category: 'Food', price: 95 },
        { id: 6, name: 'Clubhouse Sandwich', category: 'Food', price: 110 },
        { id: 7, name: 'Chocolate Waffles', category: 'Desserts', price: 65 },
        { id: 8, name: 'Coffee Frappe', category: 'Desserts', price: 115 },
        { id: 9, name: 'Mango Cheesecake', category: 'Desserts', price: 130 }
    ];

    let currentCartState = {};
    let activeSelectedProduct = null;
    let currentSpinnerCount = 1;

    // 2. Render and Filter Product Grid Matrix
    function filterCategory(categoryName) {
        // Toggle Active Button Styles
        const tabs = document.querySelectorAll('.btn-category');
        tabs.forEach(tab => {
            if(tab.textContent.trim() === categoryName) tab.classList.add('active');
            else tab.classList.remove('active');
        });

        const grid = document.getElementById('productsGrid');
        grid.innerHTML = '';

        const filteredItems = dummyDatabase.filter(item => item.category === categoryName);
        
        filteredItems.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-item-clickable-card';
            productCard.onclick = () => openQuantityModal(product);
            productCard.innerHTML = `
                <div class="product-mock-image-frame"><span>Image Here</span></div>
                <p class="product-card-title-text">${product.name}</p>
                <p class="product-card-price-tag">₱${product.price}</p>
            `;
            grid.appendChild(productCard);
        });
    }

    // 3. Quantity Spinner Controls Logic Engine
    function openQuantityModal(product) {
        activeSelectedProduct = product;
        currentSpinnerCount = 1;
        document.getElementById('modalProductTitle').textContent = product.name.toUpperCase();
        document.getElementById('spinnerQuantityValue').textContent = currentSpinnerCount;
        document.getElementById('quantityModal').classList.add('active');
    }

    function closeQuantityModal() {
        document.getElementById('quantityModal').classList.remove('active');
        activeSelectedProduct = null;
    }

    function adjustSpinner(value) {
        currentSpinnerCount += value;
        if(currentSpinnerCount < 1) currentSpinnerCount = 1;
        document.getElementById('spinnerQuantityValue').textContent = currentSpinnerCount;
    }

    // 4. State Cart Calculations Engine Subsystem
    function confirmAddToCart() {
        if (!activeSelectedProduct) return;
        
        const productId = activeSelectedProduct.id;
        if (currentCartState[productId]) {
            currentCartState[productId].quantity += currentSpinnerCount;
        } else {
            currentCartState[productId] = {
                name: activeSelectedProduct.name,
                price: activeSelectedProduct.price,
                quantity: currentSpinnerCount
            };
        }
        
        closeQuantityModal();
        renderCartDashboardSubsystem();
    }

    function removeCartItem(productId) {
        delete currentCartState[productId];
        renderCartDashboardSubsystem();
    }

    function renderCartDashboardSubsystem() {
        const listContainer = document.getElementById('cartItemsList');
        const emptyNotice = document.getElementById('emptyCartNotice');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        // Clear old rows except the fallback element
        const rows = listContainer.querySelectorAll('.cart-item-row-entry');
        rows.forEach(row => row.remove());

        const keys = Object.keys(currentCartState);
        let calculatedItemsCount = 0;
        let calculatedSubtotal = 0;

        if (keys.length === 0) {
            emptyNotice.style.display = 'block';
            checkoutBtn.disabled = true;
        } else {
            emptyNotice.style.display = 'none';
            checkoutBtn.disabled = false;

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
                        <span>₱${item.price * item.quantity}</span>
                        <button class="btn-remove-cart-item" onclick="removeCartItem(${id})"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                `;
                listContainer.insertBefore(itemRow, emptyNotice);
            });
        }

        // Output Live Values onto Dashboard
        document.getElementById('summaryItemCount').textContent = calculatedItemsCount;
        document.getElementById('summarySubtotal').textContent = `₱${calculatedSubtotal}`;
        document.getElementById('summaryTotal').textContent = `₱${calculatedSubtotal}`;
    }

    // 5. Receipt Invoice Formatting & Processing System
    function openReceiptModal() {
        const invoiceFrame = document.getElementById('receiptInvoiceItems');
        invoiceFrame.innerHTML = '';
        let totalSum = 0;

        Object.keys(currentCartState).forEach(id => {
            const item = currentCartState[id];
            totalSum += (item.price * item.quantity);
            
            const lineRow = document.createElement('div');
            lineRow.className = 'receipt-invoice-line';
            lineRow.innerHTML = `<span>${item.name} x${item.quantity}</span><span>₱${item.price * item.quantity}</span>`;
            invoiceFrame.appendChild(lineRow);
        });

        document.getElementById('receiptTotalValue').textContent = `₱${totalSum}`;
        document.getElementById('receiptModal').classList.add('active');
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.remove('active');
    }

    // 6. Native Driver Hardware Print Trigger Command 
    function triggerHardwarePrint() {
        window.print();
    }

    // Load Default Category Selection State on init
    document.addEventListener('DOMContentLoaded', () => {
        filterCategory('Drinks');
    });
</script>
@endsection