@extends('layouts.app')

@section('title', 'Products - Bean & Bites POS')

@section('content')
<div class="inventory-container">
    
    <div class="inventory-header">
        <h2>STOCK INVENTORY</h2>
    </div>

    <a href="#addProductModal" class="btn-add-product">
        + ADD PRODUCT
    </a>

    <div class="table-card">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 15%;">Price</th>
                    <th style="width: 17%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="empty-table-msg">
                        No products registered in stock inventory yet.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="addProductModal" class="modal-overlay">
        <div class="modal-card">
            <h2>ADD PRODUCT</h2>

            <form action="javascript:void(0);" id="addProductForm">
                <div class="modal-input-group">
                    <label for="modalProductName">Product Name</label>
                    <input type="text" id="modalProductName" placeholder="Product name" required autocomplete="off">
                </div>

                <div class="modal-input-group">
                    <label for="modalCategory">Category</label>
                    <div class="select-wrapper">
                        <select id="modalCategory" required>
                            <option value="" disabled selected hidden>Select category</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Food">Food</option>
                            <option value="Desserts">Desserts</option>
                        </select>
                    </div>
                </div>

                <div class="modal-input-group">
                    <label for="modalPrice">Price</label>
                    <input type="number" id="modalPrice" placeholder="Price" step="0.01" required>
                </div>

                <div class="modal-input-group">
                    <label for="modalStock">Stock</label>
                    <input type="number" id="modalStock" placeholder="Stock" required>
                </div>

                <button type="submit" class="btn-modal-save">Save</button>
                <a href="#" class="btn-modal-cancel">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection