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