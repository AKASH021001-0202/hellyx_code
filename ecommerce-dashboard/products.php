<?php include('sidebar.php'); ?>
<div class="main">
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Product Management</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="productTableBody"></tbody>
      </table>
    </div>
  </div>

  <!-- Add Product Modal -->
  <div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form id="addProductForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" name="name" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" name="price" step="0.01" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Stock Quantity</label>
              <input type="number" name="stock_quantity" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Image URL</label>
              <input type="url" name="image" class="form-control" required />
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Product</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form id="editProductForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" />
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" name="name" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" name="price" step="0.01" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Stock Quantity</label>
              <input type="number" name="stock_quantity" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Image URL</label>
              <input type="url" name="image" class="form-control" required />
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update Product</button>
        </div>
      </form>
    </div>
  </div>

  <script>
      function getCookie(name) {
  const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
  return match ? decodeURIComponent(match[2]) : null;
}
    // Load all products
      const token = getCookie("adminToken");
    async function loadProducts() {
      try {
        const res = await fetch("http://localhost:8000/products", {
          headers: {
       "Authorization": "Bearer " + token
          },
        });
        const data = await res.json();
        const tbody = document.getElementById("productTableBody");
        tbody.innerHTML = data.products.map(p => `
          <tr>
            <td><img src="${p.image}" alt="${p.name}" style="height: 50px;" /></td>
            <td>${p.name}</td>
            <td>$${p.price.toFixed(2)}</td>
            <td>${p.stock_quantity}</td>
            <td>
              <button class="btn btn-sm btn-warning me-2" onclick="openEditModal('${p._id}')">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="deleteProduct('${p._id}')">Delete</button>
            </td>
          </tr>
        `).join("");
      } catch (err) {
        console.error("Load error:", err);
      }
    }

    // Add new product
    document.getElementById("addProductForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const form = e.target;
      const newProduct = {
        name: form.name.value.trim(),
        price: parseFloat(form.price.value),
        stock_quantity: parseInt(form.stock_quantity.value),
        image: form.image.value.trim(),
        description: form.description.value.trim(),
      };

      try {
        const res = await fetch("http://localhost:8000/products", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + localStorage.getItem("authToken"),
          },
          body: JSON.stringify(newProduct),
        });

        const result = await res.json();
        if (res.ok) {
          alert("✅ Product added!");
          form.reset();
          bootstrap.Modal.getInstance(document.getElementById("addProductModal")).hide();
          loadProducts();
        } else {
          alert(result.message || "Add failed");
        }
      } catch (err) {
        console.error("Add error:", err);
      }
    });

    // Delete product
    async function deleteProduct(id) {
      if (!confirm("Delete this product?")) return;

      try {
        const res = await fetch(`http://localhost:8000/products/${id}`, {
          method: "DELETE",
          headers: {
            "Authorization": "Bearer " + localStorage.getItem("authToken"),
          },
        });

        const result = await res.json();
        if (res.ok) {
          alert("✅ Deleted");
          loadProducts();
        } else {
          alert(result.message || "Delete failed");
        }
      } catch (err) {
        console.error("Delete error:", err);
      }
    }

    // Open Edit Modal
    async function openEditModal(id) {
      try {
        const res = await fetch(`http://localhost:8000/products/${id}`, {
          headers: {
            "Authorization": "Bearer " + localStorage.getItem("authToken"),
          },
        });

        const p = await res.json();
        const form = document.getElementById("editProductForm");
        form.id.value = p._id;
        form.name.value = p.name;
        form.price.value = p.price;
        form.stock_quantity.value = p.stock_quantity;
        form.image.value = p.image;
        form.description.value = p.description;

        new bootstrap.Modal(document.getElementById("editProductModal")).show();
      } catch (err) {
        console.error("Edit load error:", err);
      }
    }

    // Submit Edit
    document.getElementById("editProductForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const form = e.target;
      const updated = {
        name: form.name.value.trim(),
        price: parseFloat(form.price.value),
        stock_quantity: parseInt(form.stock_quantity.value),
        image: form.image.value.trim(),
        description: form.description.value.trim(),
      };

      try {
        const res = await fetch(`http://localhost:8000/products/${form.id.value}`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + localStorage.getItem("authToken"),
          },
          body: JSON.stringify(updated),
        });

        const result = await res.json();
        if (res.ok) {
          alert("✅ Updated");
          bootstrap.Modal.getInstance(document.getElementById("editProductModal")).hide();
          loadProducts();
        } else {
          alert(result.message || "Update failed");
        }
      } catch (err) {
        console.error("Update error:", err);
      }
    });

    document.addEventListener("DOMContentLoaded", loadProducts);
  </script>
</div>
<?php include('bottombar.php'); ?>
