/**
 * Bean & Bites POS - Stock Inventory Subsystem Engine
 * Handles modal hydration, image previews, and asynchronous product lifecycle changes
 */

/**
 * Universal visibility switch state manager for inventory stock fields
 * @param {HTMLElement} toggleElement - The toggle checkbox control switch
 * @param {String} wrapperId - Container wrapper element ID
 * @param {String} inputId - Numeric stock text field input element ID
 */
function toggleStockFieldVisibility(toggleElement, wrapperId, inputId) {
    const stockWrapper = document.getElementById(wrapperId);
    const stockInput = document.getElementById(inputId);
    
    if (!stockWrapper || !stockInput) return;

    if (toggleElement.checked) {
        stockWrapper.classList.remove('stock-field-disabled');
        stockInput.required = true;
        stockInput.disabled = false;
    } else {
        stockWrapper.classList.add('stock-field-disabled');
        stockInput.required = false;
        stockInput.disabled = true;
        stockInput.value = '';
    }
}

/**
 * Live front-end preview engine pipeline for localized form file uploads
 * @param {HTMLInputElement} inputElement - Input file element upload trigger
 * @param {String} textId - Text string status placeholder indicator label element ID
 */
function previewSelectedImage(inputElement, textId) {
    const placeholderText = document.getElementById(textId);
    if (!inputElement.parentElement) return;
    
    const centerWrapper = inputElement.parentElement.querySelector('.dropzone-center-wrapper');
    if (!placeholderText || !centerWrapper) return;
    
    if (inputElement.files && inputElement.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const oldIcon = centerWrapper.querySelector('.upload-icon');
            const oldImg = centerWrapper.querySelector('.image-upload-preview-thumbnail');
            
            if (oldIcon) oldIcon.remove();
            if (oldImg) oldImg.remove();
            
            const previewImage = document.createElement('img');
            previewImage.className = 'image-upload-preview-thumbnail';
            previewImage.src = e.target.result;
            
            centerWrapper.insertBefore(previewImage, placeholderText);
            placeholderText.textContent = "Change Selected Image";
        }
        reader.readAsDataURL(inputElement.files[0]);
    }
}

/**
 * Hydrates input form values dynamically with existing database records maps
 * @param {Number} id - Unique numeric model context row item reference identity
 */
function prepareEditModal(id) {
    fetch(`/products/edit/${id}`)
        .then(response => response.json())
        .then(product => {
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editCategory').value = product.category;
            document.getElementById('editPrice').value = product.price;

            const toggle = document.getElementById('editTrackStockToggle');
            const stockInput = document.getElementById('editStock');
            
            if (product.stock !== null) {
                toggle.checked = true;
                stockInput.value = product.stock;
                toggleStockFieldVisibility(toggle, 'editStockInputWrapper', 'editStock');
            } else {
                toggle.checked = false;
                stockInput.value = '';
                toggleStockFieldVisibility(toggle, 'editStockInputWrapper', 'editStock');
            }

            const wrapper = document.getElementById('editImageWrapper');
            if (!wrapper) return;

            if (product.image_path) {
                wrapper.innerHTML = `
                    <img src="/storage/${product.image_path}" class="image-upload-preview-thumbnail">
                    <span id="editUploadPlaceholderText">Change Selected Image</span>
                `;
            } else {
                wrapper.innerHTML = `
                    <i class="fa-regular fa-image upload-icon"></i>
                    <span id="editUploadPlaceholderText">Click to upload image</span>
                `;
            }
        })
        .catch(err => console.error("Error fetching product data payload:", err));
}

/**
 * Prepares delete confirmation modal values
 * @param {Number} id - Product ID to remove
 * @param {String} name - Product text string descriptor value label
 */
function prepareDeleteModal(id, name) {
    const targetIdInput = document.getElementById('deleteTargetId');
    const targetNameSpan = document.getElementById('deleteTargetName');
    const remarksInput = document.getElementById('deleteRemarksInput');

    if (targetIdInput) targetIdInput.value = id;
    if (targetNameSpan) targetNameSpan.textContent = `"${name}"`;
    if (remarksInput) remarksInput.value = '';
}

/**
 * Real-Time Table Filter Engine
 * Scans inventory row components and matches characters against product details strings
 */
function searchInventoryTable() {
    const queryText = document.getElementById('inventorySearchInput').value.toLowerCase().trim();
    const tableRows = document.querySelectorAll('#inventoryTableBody tr');

    tableRows.forEach(row => {
        // Skip the fallback empty placeholder row if it's currently showing
        if (row.id === 'emptyRowPlaceholder') return;

        const nameCell = row.querySelector('.product-name-text');
        const categoryCell = row.querySelector('.product-category-text');

        if (nameCell && categoryCell) {
            const nameText = nameCell.textContent.toLowerCase();
            const categoryText = categoryCell.textContent.toLowerCase();

            // Check if either the product name or category matches your typed input string
            if (nameText.includes(queryText) || categoryText.includes(queryText)) {
                row.style.display = ''; // Clear display restrictions to show matching item
            } else {
                row.style.display = 'none'; // Restrict visibility on mismatched items
            }
        }
    });
}

// Register form event listeners on document boot paths
document.addEventListener('DOMContentLoaded', () => {

    const addForm = document.getElementById('addProductForm');
    const editForm = document.getElementById('editProductForm');
    const deleteForm = document.getElementById('deleteProductForm');

    // Asynchronous Store Engine Handler Mapping
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch('/products/store', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const emptyRow = document.getElementById('emptyRowPlaceholder');
                    if (emptyRow) emptyRow.remove();

                    const tableBody = document.getElementById('inventoryTableBody');
                    if (!tableBody) return;

                    const nextIndex = tableBody.querySelectorAll('tr').length + 1;

                    let stockText = data.product.stock !== null ? data.product.stock : '<span style="color: #baa495; font-style: italic;">Infinite</span>';
                    let priceFormatted = parseFloat(data.product.price).toFixed(2);
                    let imageTag = data.product.image_path 
                        ? `<img src="/storage/${data.product.image_path}" style="width: 35px; height: 35px; object-fit: cover; border-radius: 6px; margin-right: 8px; vertical-align: middle;">` 
                        : '<div style="display:inline-block; width: 35px; height: 35px; background:#baa495; border-radius:6px; margin-right:8px; vertical-align:middle;"></div>';

                    let newRow = `
                        <tr id="product-row-${data.product.id}">
                            <td class="row-index">${nextIndex}</td>
                            <td class="product-display-cell">
                                ${imageTag}
                                <span class="product-name-text" style="vertical-align: middle;">${data.product.name}</span>
                            </td>
                            <td class="product-category-text">${data.product.category}</td>
                            <td class="product-stock-text">${stockText}</td>
                            <td class="product-price-text">₱${priceFormatted}</td>
                            <td>
                                <div class="action-btn-group">
                                    <a href="#editProductModal" class="btn-action-edit" onclick="prepareEditModal(${data.product.id})">EDIT</a>
                                    <a href="#deleteProductModal" class="btn-action-delete" onclick="prepareDeleteModal(${data.product.id}, '${data.product.name.replace(/'/g, "\\'")}')">DELETE</a>
                                </div>
                            </td>
                        </tr>
                    `;

                    tableBody.insertAdjacentHTML('afterbegin', newRow);
                    addForm.reset();
                    window.location.hash = '';
                    
                    // Reset the search filter if an item is added while typing
                    const searchInput = document.getElementById('inventorySearchInput');
                    if (searchInput && searchInput.value !== '') {
                        searchInventoryTable();
                    }
                }
            })
            .catch(err => console.error("Error storing inventory product:", err));
        });
    }

    // Asynchronous Update Engine Handler Mapping
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('editProductId').value;
            let formData = new FormData(this);

            fetch(`/products/update/${productId}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const targetRow = document.getElementById(`product-row-${productId}`);
                    if (targetRow) {
                        targetRow.querySelector('.product-name-text').textContent = data.product.name;
                        targetRow.querySelector('.product-category-text').textContent = data.product.category;
                        targetRow.querySelector('.product-price-text').textContent = '₱' + parseFloat(data.product.price).toFixed(2);
                        
                        let stockContent = data.product.stock !== null ? data.product.stock : '<span style="color: #baa495; font-style: italic;">Infinite</span>';
                        targetRow.querySelector('.product-stock-text').innerHTML = stockContent;

                        if (data.product.image_path) {
                            const img = targetRow.querySelector('.table-thumbnail-img');
                            if (img) {
                                img.src = `/storage/${data.product.image_path}?t=${new Date().getTime()}`;
                            } else {
                                const placeholder = targetRow.querySelector('.table-thumbnail-placeholder');
                                if (placeholder) {
                                    const newImg = document.createElement('img');
                                    newImg.className = 'table-thumbnail-img';
                                    newImg.src = `/storage/${data.product.image_path}`;
                                    newImg.style = "width: 35px; height: 35px; object-fit: cover; border-radius: 6px; margin-right: 8px; vertical-align: middle;";
                                    placeholder.replaceWith(newImg);
                                }
                            }
                        }
                    }
                    window.location.hash = '';
                }
            })
            .catch(err => console.error("Error updating inventory product details:", err));
        });
    }

    // Asynchronous Soft-Deletion Audit Logger Subsystem Pipeline
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const targetId = document.getElementById('deleteTargetId').value;
            const formData = new FormData(this);

            fetch(`/products/delete/${targetId}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const targetRow = document.getElementById(`product-row-${targetId}`);
                    if (targetRow) {
                        targetRow.style.animation = "fadeIn 0.2s reverse ease-out";
                        setTimeout(() => {
                            targetRow.remove();
                            const tableBody = document.getElementById('inventoryTableBody');
                            if (!tableBody) return;

                            if (tableBody.querySelectorAll('tr').length === 0) {
                                tableBody.innerHTML = `
                                    <tr id="emptyRowPlaceholder">
                                        <td colspan="6" class="empty-table-msg">No products registered in stock inventory yet.</td>
                                    </tr>
                                `;
                            } else {
                                tableBody.querySelectorAll('tr').forEach((row, i) => {
                                    const indexCell = row.querySelector('.row-index');
                                    if (indexCell) indexCell.textContent = i + 1;
                                });
                            }
                        }, 200);
                    }
                    window.location.hash = '';
                }
            })
            .catch(err => console.error("Error executing soft delete routine:", err));
        });
    }
});