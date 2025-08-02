
<?php include('sidebar.php')?>



  <div class="main">
    <h2>Overview</h2>
    <div class="cards">
      <div class="card">
        <h3>Total Products</h3>
        <p id="productCount">--</p>
      </div>
      <div class="card">
        <h3>Total Customers</h3>
        <p id="customerCount">--</p>
      </div>
      <div class="card">
        <h3>Total Orders</h3>
        <p id="orderCount">--</p>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("open");
    }

    // Fetch dashboard stats
    fetch("http://localhost:8000/admin/dashboard", {
      headers: {
        Authorization: "Bearer " + localStorage.getItem("authToken"),
      },
    })
      .then((res) => res.json())
      .then((data) => {
        document.getElementById("productCount").textContent = data.products;
        document.getElementById("customerCount").textContent = data.customers;
        document.getElementById("orderCount").textContent = data.orders;
      })
      .catch(() => {
        document.getElementById("productCount").textContent = "N/A";
        document.getElementById("customerCount").textContent = "N/A";
        document.getElementById("orderCount").textContent = "N/A";
      });
  </script>
  
<?php include('bottombar.php')?>