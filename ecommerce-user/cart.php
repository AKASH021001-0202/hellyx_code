<?php include('header.php') ?>
<div class="container mt-5">
  <h2>Your Cart</h2>
  <table class="table table-bordered mt-3">
    <thead class="table-dark">
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
        <th>Remove</th>
      </tr>
    </thead>
    <tbody id="cartBody">
      <tr><td colspan="6" class="text-center">Loading...</td></tr>
    </tbody>
  </table>
  <div class="text-end">
    <h4 id="cartTotal">Total: $0.00</h4>
    <button class="btn btn-primary" onclick="placeOrder()">Place Order</button>
  </div>
</div>

<script>
  async function loadCartFromAPI() {
    const token = localStorage.getItem("userToken");
    if (!token) {
      document.getElementById("cartBody").innerHTML = "<tr><td colspan='6' class='text-center'>Please login to view cart.</td></tr>";
  
      return;
    }

    try {
      const res = await fetch("http://localhost:8000/cart", {
        headers: {
          "Authorization": "Bearer " + token,
        },
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.message || "Failed to fetch cart.");
      }

      const items = data.items || [];
      const total = data.total || 0;
      const tbody = document.getElementById("cartBody");

      if (items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Your cart is empty.</td></tr>`;

        return;
      }

      tbody.innerHTML = items.map(item => {
        const itemTotal = item.quantity * item.price;
        return `
          <tr>
            <td><img src="${item.image}" style="height:50px;"></td>
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td>$${itemTotal.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm" onclick="removeFromCart('${item.productId}')">X</button></td>
          </tr>
        `;
      }).join("");

      document.getElementById("cartTotal").textContent = "Total: $" + total.toFixed(2);
    

    } catch (err) {
      console.error("Cart fetch error:", err);
      document.getElementById("cartBody").innerHTML = `<tr><td colspan="6" class="text-center text-danger">Failed to load cart.</td></tr>`;
    }
  }

  async function removeFromCart(productId) {
    if (!confirm("Remove this item?")) return;

    try {
      const token = localStorage.getItem("userToken");
      const res = await fetch(`http://localhost:8000/cart/${productId}`, {
        method: "DELETE",
        headers: {
          "Authorization": "Bearer " + token,
        },
      });

      const data = await res.json();

      if (res.ok) {
        alert("✅ Removed from cart");
        loadCartFromAPI();
      } else {
        alert(data.message || "Failed to remove item");
      }
    } catch (err) {
      console.error("Remove error:", err);
      alert("Server error");
    }
  }

  document.addEventListener("DOMContentLoaded", loadCartFromAPI);
</script>
<script>
  async function placeOrder() {
    try {
      const res = await fetch("http://localhost:8000/order", {
        method: "POST",
        headers: {
          "Authorization": "Bearer " + localStorage.getItem("userToken"),
        },
      });

      const data = await res.json();
      if (res.ok) {
        alert("✅ Order placed!");
        window.location.reload();
      } else {
        alert(data.message || "Order failed.");
      }
    } catch (err) {
      console.error("Order error:", err);
      alert("Server error");
    }
  }
</script>

<?php include('footer.php') ?>
