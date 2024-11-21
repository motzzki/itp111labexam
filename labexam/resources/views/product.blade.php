<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>Product Management</title>
</head>

<body>
    <div class="container mt-5">
        <h1>Product Management</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Selling Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <!-- The product rows are generated from the controller -->
                @foreach ($products as $product)
                <tr id="product-{{ $product->id }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                    <td>{{ $product->description ?? 'N/A' }}</td>
                    <td>${{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal" onclick="editProduct('{{ $product->id }}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct('{{ $product->id }}')">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addProductForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category_name" name="category_name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="selling_price" class="form-label">Selling Price</label>
                            <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_product_id">
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_name" class="form-label">Category</label>
                            <input type="text" class="form-control" id="edit_category_name" name="category_name">
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_selling_price" class="form-label">Selling Price</label>
                            <input type="number" class="form-control" id="edit_selling_price" name="selling_price" step="0.01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Add Product
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            axios.post('/product', Object.fromEntries(formData), {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    const product = response.data.product;
                    const row = document.createElement('tr');
                    row.id = `product-${product.id}`;
                    row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.product_name}</td>
                    <td>${product.category_name || 'N/A'}</td>
                    <td>${product.description || 'N/A'}</td>
                     <td>$${parseFloat(product.selling_price).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal" onclick="editProduct(${JSON.stringify(product)})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                `;
                    document.getElementById('productTableBody').appendChild(row);
                    $('#addProductModal').modal('hide');
                })
                .catch(error => console.error(error));
        });

        // Edit Product
        function editProduct(productId) {
            axios.get(`/product/${productId}`)
                .then(response => {
                    const product = response.data;
                    document.getElementById('edit_product_id').value = product.id;
                    document.getElementById('edit_product_name').value = product.product_name;
                    document.getElementById('edit_category_name').value = product.category_name;
                    document.getElementById('edit_description').value = product.description;
                    document.getElementById('edit_selling_price').value = product.selling_price;


                    $('#editProductModal').modal('show');

                })
                .catch(error => console.error(error));
        }

        // Update Product
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('edit_product_id').value;
            const formData = new FormData(this);

            axios.put(`/product/${productId}`, Object.fromEntries(formData), {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    const product = response.data.product;
                    let row = document.getElementById(`product-${product.id}`);
                    row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.product_name}</td>
                    <td>${product.category_name}</td>
                    <td>${product.description}</td>
                    <td>$${parseFloat(product.selling_price).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal" onclick="editProduct(${JSON.stringify(product)})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                `;
                    document.getElementById('productTableBody').appendChild(row);
                    $('#editProductModal').modal('hide');
                })
                .catch(error => console.error(error));
        });

        // Delete Product
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                axios.delete(`/product/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        const row = document.getElementById(`product-${id}`);
                        row.remove();
                    })
                    .catch(error => console.error(error));
            }
        }
    </script>
</body>

</html>