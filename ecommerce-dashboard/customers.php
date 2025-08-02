<?php include('sidebar.php') ?>
<div class="main p-4">
  <h2 class="mb-4">Customer Management</h2>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Registered On</th>
      </tr>
    </thead>
    <tbody id="customerTableBody">
      <tr><td colspan="5" class="text-center">Loading...</td></tr>
    </tbody>
  </table>
</div>
<?php include('bottombar.php') ?>
<script>
  function getCookie(name) {
  const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
  return match ? decodeURIComponent(match[2]) : null;
}
// Frontend code
document.addEventListener("DOMContentLoaded", async function () {
   const token = getCookie("adminToken");
  const tbody = document.getElementById("customerTableBody");

  if (!token) {
    tbody.innerHTML = "<tr><td colspan='5' class='text-center text-danger'>Please login as admin</td></tr>";
    return;
  }

  try {
    const res = await fetch("http://localhost:8000/user", {
      headers: {
        "Authorization": "Bearer " + token
      }
    });

    if (!res.ok) {
      const errorData = await res.json();
      throw new Error(errorData.message || "Failed to fetch customers");
    }

    const data = await res.json();
    
    // Check if response is an array (raw response) or object (wrapped response)
    const customers = Array.isArray(data) ? data : data.customers || data.users || [];
    
    if (customers.length === 0) {
      tbody.innerHTML = "<tr><td colspan='5' class='text-center'>No customers found.</td></tr>";
      return;
    }

    tbody.innerHTML = customers
      .filter(cust => cust.role === "user") // Filter only users if needed
      .map((cust, index) => `
        <tr>
          <td>${index + 1}</td>
          <td>${cust.name}</td>
          <td>${cust.email}</td>
          <td>${cust.phone || '-'}</td>
          <td>${new Date(cust.createdAt).toLocaleDateString()}</td>
        </tr>
      `).join("");

  } catch (err) {
    console.error("Customer fetch error:", err);
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${err.message || "Failed to load customer list"}</td></tr>`;
  }
});
</script>
