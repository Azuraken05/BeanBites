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
            
            <div class="pos-search-wrapper" style="margin-bottom: 20px;">
                <div class="search-input-container" style="position: relative; width: 100%;">
                    <input type="text" id="posSearchInput" placeholder="Search any product globally..." onkeyup="searchAndFilterProducts()" autocomplete="off" style="width: 100%; padding: 12px 16px 12px 42px; border: none; border-radius: 12px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 0.9rem; color: #3d2514; outline: none;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #baa495; font-size: 0.95rem; pointer-events: none;"></i>
                </div>
            </div>
            
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

        <!-- PAYMENT METHOD SELECTOR -->
        <div class="payment-method-section no-print" id="stepPaymentMethod">
            <h3 class="payment-method-label">Payment Method</h3>
            <div class="payment-method-scroll">
                <label class="payment-option selected" id="pay-cash">
                    <input type="radio" name="payment_method" value="cash" checked onchange="highlightPayment(this)">
                    <span>💵 Cash</span>
                </label>
                <label class="payment-option" id="pay-gcash">
                    <input type="radio" name="payment_method" value="gcash" onchange="highlightPayment(this)">
                    <span>📱 GCash</span>
                </label>
                <label class="payment-option" id="pay-bank">
                    <input type="radio" name="payment_method" value="bank_transfer" onchange="highlightPayment(this)">
                    <span>🏦 Bank Transfer</span>
                </label>
            </div>
            <button class="btn-modal-action action-save" style="margin-top:14px;" onclick="proceedToPaymentDetails()">Next</button>
            <button class="btn-modal-action action-cancel" onclick="closeReceiptModal()">Cancel</button>
        </div>

        <!-- CASH PAYMENT DETAILS -->
        <div class="payment-detail-section no-print" id="stepCashPayment" style="display:none;">
            <h3 class="payment-method-label" style="font-size:1.1rem; margin-bottom:14px;">CASH PAYMENT</h3>
            <p style="font-size:0.95rem; font-weight:600; color:#3b1f0e; margin-bottom:14px;">Total: <span id="cashTotalDisplay">₱0</span></p>
            <input type="number" id="cashAmountInput" placeholder="Enter cash amount" min="0" step="0.01"
                oninput="computeCashChange()"
                style="width:100%; padding:12px 14px; border:2px solid #e0d5c8; border-radius:10px; font-family:'Poppins',sans-serif; font-size:0.9rem; color:#3b1f0e; outline:none; box-sizing:border-box; margin-bottom:12px;">
            <p style="font-size:0.95rem; font-weight:600; color:#3b1f0e; margin-bottom:20px;">Change: <span id="cashChangeDisplay">₱0</span></p>
            <button class="btn-modal-action action-save" onclick="confirmPaymentAndShowReceipt()">Confirm Payment</button>
            <button class="btn-modal-action action-cancel" onclick="backToPaymentMethod()">Cancel</button>
        </div>

        <!-- GCASH PAYMENT DETAILS -->
        <div class="payment-detail-section no-print" id="stepGcashPayment" style="display:none;">
            <h3 class="payment-method-label" style="font-size:1.1rem; margin-bottom:14px;">GCASH PAYMENT</h3>
            <div style="width:180px; height:180px; background-color:#1a73e8; border-radius:14px; display:flex; justify-content:center; align-items:center; margin:0 auto 16px auto;">
                <span style="color:#fff; font-family:'Oswald',sans-serif; font-size:1.4rem; letter-spacing:1px;">QR CODE</span>
            </div>
            <p style="color:#1a73e8; font-weight:700; font-size:1rem; margin-bottom:8px;">0912-345-6789</p>
            <p style="font-size:0.82rem; color:#8c6b53; margin-bottom:20px;">Scan the QR code to pay.</p>
            <button class="btn-modal-action action-save" onclick="confirmPaymentAndShowReceipt()">Payment Received</button>
            <button class="btn-modal-action action-cancel" onclick="backToPaymentMethod()">Cancel</button>
        </div>

        <!-- BANK TRANSFER PAYMENT DETAILS -->
        <div class="payment-detail-section no-print" id="stepBankPayment" style="display:none;">
            <h3 class="payment-method-label" style="font-size:1.1rem; margin-bottom:14px;">BANK TRANSFER</h3>
            <select id="bankSelectInput"
                style="width:100%; padding:12px 14px; border:2px solid #e0d5c8; border-radius:10px; font-family:'Poppins',sans-serif; font-size:0.9rem; color:#3b1f0e; outline:none; background:#fff; margin-bottom:12px; box-sizing:border-box;">
                <option value="" disabled selected>Select Bank</option>
                <option value="BDO">BDO</option>
                <option value="BPI">BPI</option>
                <option value="Metrobank">Metrobank</option>
                <option value="UnionBank">UnionBank</option>
                <option value="Landbank">Landbank</option>
            </select>
            <input type="text" id="bankReferenceInput" placeholder="Reference Number"
                style="width:100%; padding:12px 14px; border:2px solid #e0d5c8; border-radius:10px; font-family:'Poppins',sans-serif; font-size:0.9rem; color:#3b1f0e; outline:none; box-sizing:border-box; margin-bottom:20px;">
            <button class="btn-modal-action action-save" onclick="confirmPaymentAndShowReceipt()">Payment Received</button>
            <button class="btn-modal-action action-cancel" onclick="backToPaymentMethod()">Cancel</button>
        </div>

        <!-- RECEIPT BODY (shown after payment confirmed) -->
        <div id="stepReceiptBody" style="display:none;">
            <div class="receipt-body-invoice" id="receiptInvoiceItems"></div>
            <div class="receipt-divider-line"></div>
            <div class="receipt-total-display">
                <span>Total:</span><span id="receiptTotalValue">₱0</span>
            </div>
            <div id="receiptPaymentMethodLine" style="font-size:0.82rem; color:#8c6b53; margin-bottom:4px; text-align:left;"></div>
<div id="receiptCashLine" style="font-size:0.82rem; color:#8c6b53; margin-bottom:4px; text-align:left; display:none;"></div>
<div id="receiptChangeLine" style="font-size:0.82rem; color:#8c6b53; margin-bottom:16px; text-align:left; display:none;"></div>
            <button class="btn-modal-action action-save no-print" onclick="triggerHardwarePrint()">Print</button>
            <button class="btn-modal-action action-cancel no-print" onclick="closeReceiptModal()">Close</button>
        </div>
    </div>

</div>

<!-- Linked Externalized POS Subsystem Script Asset -->
<script src="{{ asset('js/pos.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Hydrate live dataset loop values and instantiate operations
        initializePOSEngine(
            @json($products),
            '{{ csrf_token() }}',
            '{{ route("pos.checkout") }}'
        );
    });
</script>
@endsection