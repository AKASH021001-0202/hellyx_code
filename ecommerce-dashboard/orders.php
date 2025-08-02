<?php include('sidebar.php') ?>
<div class="main p-4">
  <h2 class="mb-4">All Orders</h2>
  <div id="ordersList">
    <p>Loading orders...</p>
  </div>
</div>
<?php include('bottombar.php') ?>

<script>
    function getCookie(name) {
  const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
  return match ? decodeURIComponent(match[2]) : null;
}
  async function loadAllOrders() {
  const token = getCookie("adminToken");

    if (!token) {
      document.getElementById("ordersList").innerHTML = `
        <div class="alert alert-warning">Please log in as admin to view orders.</div>
      `;
      return;
    }

    try {
      const res = await fetch("http://localhost:8000/order/admin-orders", {
        headers: {
          "Authorization": "Bearer " + token
        }
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.message || "Failed to load orders");
      }

      const orders = data.orders;
      if (!orders.length) {
        document.getElementById("ordersList").innerHTML = `
          <div class="alert alert-info">No orders found.</div>
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
          <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between">
              <strong>Order #${order._id}</strong>
              <span class="badge bg-${order.status === 'pending' ? 'warning' : 'success'} text-uppercase">${order.status}</span>
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
                <tbody>${itemList}</tbody>
              </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
              <div>
                <strong>User:</strong> ${order.user?.email || 'Unknown'}<br>
                <small>Created: ${new Date(order.createdAt).toLocaleString()}</small>
              </div>
              ${order.status === 'pending' ? `
                <button class="btn btn-sm btn-success" onclick="approveOrder('${order._id}')">Approve</button>
              ` : ''}
            </div>
          </div>
        `;
      }).join("");

      document.getElementById("ordersList").innerHTML = html;

    } catch (err) {
      console.error("Load error:", err);
      document.getElementById("ordersList").innerHTML = `
        <div class="alert alert-danger">Failed to load orders.</div>
      `;
    }
  }

  async function approveOrder(orderId) {
    if (!confirm("Approve this order?")) return;
    try {
      const token = localStorage.getItem("authToken");
      const res = await fetch(`http://localhost:8000/order/${orderId}/accept`, {
        method: "PUT",
        headers: {
          "Authorization": "Bearer " + token
        }
      });

      const data = await res.json();
      if (res.ok) {
        alert("✅ Order approved");
        loadAllOrders();
      } else {
        alert(data.message || "Approval failed");
      }
    } catch (err) {
      alert("Server error");
    }
  }

  document.addEventListener("DOMContentLoaded", loadAllOrders);
</script>
