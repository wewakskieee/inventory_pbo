const API_URL = 'api.php?path=';
let products = [];
let categories = [];

// Load data saat halaman dibuka
document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadProducts();
    setupEventListeners();
});

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');

    searchInput.addEventListener('input', debounce(() => {
        const keyword = searchInput.value.trim();
        if (keyword) {
            searchProducts(keyword);
        } else {
            loadProducts();
        }
    }, 500));

    categoryFilter.addEventListener('change', () => {
        const categoryId = categoryFilter.value;
        if (categoryId) {
            filterByCategory(categoryId);
        } else {
            loadProducts();
        }
    });
}

function debounce(func, delay) {
    let timeout;
    return function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, arguments), delay);
    };
}

async function loadCategories() {
    try {
        const response = await fetch(`${API_URL}categories`);
        const result = await response.json();

        if (result.success) {
            categories = result.data;
            populateCategoryOptions();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryOptions() {
    const categoryFilter = document.getElementById('categoryFilter');
    const categorySelect = document.getElementById('category_id');

    categories.forEach(cat => {
        const option1 = document.createElement('option');
        option1.value = cat.id;
        option1.textContent = cat.name;
        categoryFilter.appendChild(option1);

        const option2 = document.createElement('option');
        option2.value = cat.id;
        option2.textContent = cat.name;
        categorySelect.appendChild(option2);
    });
}


async function loadProducts() {
    const tbody = document.getElementById('productTableBody');
    
    try {
        const response = await fetch(`${API_URL}products`);
        const result = await response.json();

        if (result.success) {
            products = result.data;
            renderProducts(products);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error loading products:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <h3>Error</h3>
                    <p>${error.message}</p>
                </td>
            </tr>
        `;
        showError('Gagal memuat data produk');
    }
}


async function searchProducts(keyword) {
    try {
        const response = await fetch(`${API_URL}products&search=${encodeURIComponent(keyword)}`);
        const result = await response.json();

        if (result.success) {
            renderProducts(result.data);
        }
    } catch (error) {
        console.error('Error searching products:', error);
    }
}

async function filterByCategory(categoryId) {
    try {
        const response = await fetch(`${API_URL}products&category=${categoryId}`);
        const result = await response.json();

        if (result.success) {
            renderProducts(result.data);
        }
    } catch (error) {
        console.error('Error filtering products:', error);
    }
}

function renderProducts(data) {
    const tbody = document.getElementById('productTableBody');

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <h3>Tidak ada data</h3>
                    <p>Belum ada produk yang ditambahkan</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.map(product => {
        const isLowStock = product.quantity <= product.minimum_stock;
        const statusBadge = isLowStock 
            ? '<span class="badge badge-danger">Stok Rendah</span>'
            : '<span class="badge badge-success">Tersedia</span>';

        return `
            <tr>
                <td>${product.sku}</td>
                <td>${product.name}</td>
                <td>${product.category_name}</td>
                <td>${product.quantity}</td>
                <td>Rp ${formatNumber(product.price)}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-small btn-primary" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-small btn-danger" onclick="deleteProduct(${product.id})">Hapus</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Produk';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    clearErrors();
    document.getElementById('productModal').classList.add('active');
}

async function editProduct(id) {
    try {
        const response = await fetch(`${API_URL}products/${id}`);
        const result = await response.json();

        if (result.success) {
            const product = result.data;
            document.getElementById('modalTitle').textContent = 'Edit Produk';
            document.getElementById('productId').value = product.id;
            document.getElementById('sku').value = product.sku;
            document.getElementById('name').value = product.name;
            document.getElementById('description').value = product.description || '';
            document.getElementById('category_id').value = product.category_id;
            document.getElementById('quantity').value = product.quantity;
            document.getElementById('price').value = product.price;
            document.getElementById('minimum_stock').value = product.minimum_stock;
            
            clearErrors();
            document.getElementById('productModal').classList.add('active');
        }
    } catch (error) {
        console.error('Error loading product:', error);
        showError('Gagal memuat data produk');
    }
}

async function submitForm(event) {
    event.preventDefault();
    clearErrors();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    const productId = document.getElementById('productId').value;

    try {
        const url = productId ? `${API_URL}products/${productId}` : `${API_URL}products`;
        const method = productId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showSuccess(result.message);
            closeModal();
            loadProducts();
        } else {
            if (result.errors) {
                displayErrors(result.errors);
            } else {
                showError(result.message);
            }
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        showError('Terjadi kesalahan saat menyimpan data');
    }
}

async function deleteProduct(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}products/${id}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            showSuccess(result.message);
            loadProducts();
        } else {
            showError(result.message);
        }
    } catch (error) {
        console.error('Error deleting product:', error);
        showError('Gagal menghapus produk');
    }
}

function closeModal() {
    document.getElementById('productModal').classList.remove('active');
    document.getElementById('productForm').reset();
    clearErrors();
}

function displayErrors(errors) {
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(`${field}Error`);
        const formGroup = errorElement.closest('.form-group');
        
        if (errorElement) {
            errorElement.textContent = errors[field];
            formGroup.classList.add('error');
        }
    });
}

function clearErrors() {
    document.querySelectorAll('.form-error').forEach(el => {
        el.textContent = '';
        el.closest('.form-group').classList.remove('error');
    });
}

function showSuccess(message) {
    const alert = document.getElementById('alertSuccess');
    alert.textContent = message;
    alert.classList.add('active');
    
    setTimeout(() => {
        alert.classList.remove('active');
    }, 3000);
}

function showError(message) {
    const alert = document.getElementById('alertError');
    alert.textContent = message;
    alert.classList.add('active');
    
    setTimeout(() => {
        alert.classList.remove('active');   
    }, 3000);
}
