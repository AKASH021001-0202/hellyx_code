<?php include('header.php') ?>
<div class="container mt-5">
  <h2>My Orders</h2>

  <div id="ordersContainer" class="mt-4">
    <p>Loading your orders...</p>
  </div>
</div>

<script>
  async function loadMyOrders() {
    const token = localStorage.getItem("userToken");

    if (!token) {
      document.getElementById("ordersContainer").innerHTML = `
        <div class="alert alert-warning">Please log in to view your orders.</div>
      `;
      return;
    }

    try {
      const res = await fetch("http://localhost:8000/order/my-orders", {
        headers: {
          "Authorization": "Bearer " + token
        }
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.message || "Failed to fetch orders.");
      }

      const orders = data.orders;

      if (orders.length === 0) {
        document.getElementById("ordersContainer").innerHTML = `
          <div class="alert alert-info">You have no orders yet.</div>
        `;
        return;
      }

      const html = orders.map(order => {
        const itemList = order.items.map(item => `
          <tr>
            <td><img src="${item.image}" style="height:40px;"></td>
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td>$${(item.price * item.quantity).toFixed(2)}</td>
          </tr>
        `).join("");

        const total = order.items.reduce((sum, i) => sum + i.price * i.quantity, 0).toFixed(2);

        return `
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <strong>Order #${order._id}</strong>
              <span class="badge bg-${order.status === 'pending' ? 'warning' : 'success'} text-uppercase">
                ${order.status}
              </span>
            </div>
            <div class="card-body p-0">
              <table class="table mb-0">
                <thead>
                  <tr class="table-light">
                    <th>Image</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  ${itemList}
                </tbody>
              </table>
            </div>
            <div class="card-footer text-end">
              <strong>Total: $${total}</strong> <br>
              <small class="text-muted">Ordered on: ${new Date(order.createdAt).toLocaleString()}</small>
            </div>
          </div>
        `;
      }).join("");

      document.getElementById("ordersContainer").innerHTML = html;

    } catch (err) {
      console.error("Order fetch error:", err);
      document.getElementById("ordersContainer").innerHTML = `
        <div class="alert alert-danger">Failed to load your orders.</div>
      `;
    }
  }

  document.addEventListener("DOMContentLoaded", loadMyOrders);
</script>

<?php include('footer.php') ?>
