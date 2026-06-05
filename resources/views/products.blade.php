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

    <div class="inventory-search-wrapper" style="margin-bottom: 20px; max-width: 320px;">
        <div class="search-input-container" style="position: relative; width: 100%;">
            <input type="text" id="inventorySearchInput" placeholder="Search product name or category..." onkeyup="searchInventoryTable()" autocomplete="off" style="width: 100%; padding: 10px 16px 10px 40px; border: 1px solid #baa495; border-radius: 12px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 0.88rem; color: #3d2514; outline: none;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #baa495; font-size: 0.9rem; pointer-events: none;"></i>
        </div>
    </div>
    
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
            <tbody id="inventoryTableBody">
                @forelse($products as $index => $product)
                    <tr id="product-row-{{ $product->id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td class="product-display-cell">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" class="table-thumbnail-img" style="width: 35px; height: 35px; object-fit: cover; border-radius: 6px; margin-right: 8px; vertical-align: middle;">
                            @else
                                <div class="table-thumbnail-placeholder" style="display:inline-block; width: 35px; height: 35px; background:#baa495; border-radius:6px; margin-right:8px; vertical-align:middle;"></div>
                            @endif
                            <span class="product-name-text" style="vertical-align: middle;">{{ $product->name }}</span>
                        </td>
                        <td class="product-category-text">{{ $product->category }}</td>
                        <td class="product-stock-text">
                            {!! $product->stock !== null ? $product->stock : '<span style="color: #baa495; font-style: italic;">Infinite</span>' !!}
                        </td>
                        <td class="product-price-text">₱{{ number_format($product->price, 2) }}</td>
                        <td>
                            <div class="action-btn-group">
                                <a href="#editProductModal" class="btn-action-edit" onclick="prepareEditModal({{ $product->id }})">EDIT</a>
                                <a href="#deleteProductModal" class="btn-action-delete" onclick="prepareDeleteModal({{ $product->id }}, '{{ addslashes($product->name) }}')">DELETE</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRowPlaceholder">
                        <td colspan="6" class="empty-table-msg">
                            No products registered in stock inventory yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="addProductModal" class="modal-overlay">
        <div class="modal-card">
            <h2>ADD PRODUCT</h2>

            <form id="addProductForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-scroll-body">
                    <div class="modal-input-group" style="margin-bottom: 20px;">
                        <label>Product Image</label>
                        <label for="modalImage" class="image-upload-dropzone">
                            <div class="dropzone-center-wrapper">
                                <i class="fa-regular fa-image upload-icon"></i>
                                <span id="uploadPlaceholderText">Click to upload image</span>
                            </div>
                            <input type="file" id="modalImage" name="image" accept="image/*" onchange="previewSelectedImage(this, 'uploadPlaceholderText')">
                        </label>
                    </div>

                    <div class="modal-input-group">
                        <label for="modalProductName">Product Name</label>
                        <input type="text" id="modalProductName" name="name" placeholder="Product name" required autocomplete="off">
                    </div>

                    <div class="modal-input-group">
                        <label for="modalCategory">Category</label>
                        <div class="select-wrapper">
                            <select id="modalCategory" name="category" required>
                                <option value="" disabled selected hidden>Select category</option>
                                <option value="Drinks">Drinks</option>
                                <option value="Food">Food</option>
                                <option value="Desserts">Desserts</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-input-group">
                        <label for="modalPrice">Price</label>
                        <input type="number" id="modalPrice" name="price" placeholder="Price" step="0.01" required>
                    </div>

                    <div class="modal-input-group toggle-field-group">
                        <label>Track Stock Levels?</label>
                        <label class="switch-control">
                            <input type="checkbox" id="trackStockToggle" name="track_stock" value="1" checked onchange="toggleStockFieldVisibility(this, 'stockInputWrapper', 'modalStock')">
                            <span class="slider-round"></span>
                        </label>
                    </div>

                    <div class="modal-input-group" id="stockInputWrapper">
                        <label for="modalStock">Stock Quantity</label>
                        <input type="number" id="modalStock" name="stock" placeholder="Enter stock quantity" required>
                    </div>
                </div> 
                <button type="submit" class="btn-modal-save">Save</button>
                <a href="#" class="btn-modal-cancel">Cancel</a>
            </form>
        </div>
    </div>

    <div id="editProductModal" class="modal-overlay">
        <div class="modal-card">
            <h2>EDIT PRODUCT</h2>

            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="editProductId">
                
                <div class="modal-scroll-body">
                    <div class="modal-input-group" style="margin-bottom: 20px;">
                        <label>Product Image</label>
                        <label for="editModalImage" class="image-upload-dropzone">
                            <div class="dropzone-center-wrapper" id="editImageWrapper">
                                <i class="fa-regular fa-image upload-icon"></i>
                                <span id="editUploadPlaceholderText">Click to change image</span>
                            </div>
                            <input type="file" id="editModalImage" name="image" accept="image/*" onchange="previewSelectedImage(this, 'editUploadPlaceholderText')">
                        </label>
                    </div>

                    <div class="modal-input-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" id="editProductName" name="name" placeholder="Product name" required autocomplete="off">
                    </div>

                    <div class="modal-input-group">
                        <label for="editCategory">Category</label>
                        <div class="select-wrapper">
                            <select id="editCategory" name="category" required>
                                <option value="Drinks">Drinks</option>
                                <option value="Food">Food</option>
                                <option value="Desserts">Desserts</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-input-group">
                        <label for="editPrice">Price</label>
                        <input type="number" id="editPrice" name="price" placeholder="Price" step="0.01" required>
                    </div>

                    <div class="modal-input-group toggle-field-group">
                        <label>Track Stock Levels?</label>
                        <label class="switch-control">
                            <input type="checkbox" id="editTrackStockToggle" name="track_stock" value="1" onchange="toggleStockFieldVisibility(this, 'editStockInputWrapper', 'editStock')">
                            <span class="slider-round"></span>
                        </label>
                    </div>

                    <div class="modal-input-group" id="editStockInputWrapper">
                        <label for="editStock">Stock Quantity</label>
                        <input type="number" id="editStock" name="stock" placeholder="Enter stock quantity">
                    </div>
                </div> 
                <button type="submit" class="btn-modal-save">Update Changes</button>
                <a href="#" class="btn-modal-cancel">Cancel</a>
            </form>
        </div>
    </div>

    <div id="deleteProductModal" class="modal-overlay">
        <div class="modal-card delete-modal-override">
            <h2>DELETE PRODUCT</h2>
            <p class="delete-confirmation-text">Are you sure you want to delete <b id="deleteTargetName">this product</b>?</p>
            
            <form id="deleteProductForm">
                @csrf
                <input type="hidden" id="deleteTargetId">
                
                <div class="modal-input-group" style="text-align: left; margin-top: 15px;">
                    <label for="deleteRemarksInput">Deletion Remarks / Reason</label>
                    <input type="text" id="deleteRemarksInput" name="remarks" placeholder="Enter reason (e.g., Out of season, expired)" required autocomplete="off">
                </div>

                <div class="delete-modal-footer-buttons">
                    <button type="submit" class="btn-confirm-delete">Delete</button>
                    <a href="#" class="btn-confirm-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="{{ asset('js/products.js') }}"></script>
@endsection