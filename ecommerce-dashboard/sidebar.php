<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
      min-height: 100vh;
      background-color: #f5f5f5;
    }

    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      height: 100%;
      width: 250px;
      background-color: #1a202c;
      color: #fff;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      padding-top: 60px;
      z-index: 1000;
    }

    .sidebar.open {
      transform: translateX(0);
    }

    .sidebar nav a {
      display: block;
      padding: 15px 20px;
      color: #fff;
      text-decoration: none;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar nav a:hover {
      background-color: #2d3748;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      height: 60px;
      width: 100%;
      background-color: #2d3748;
      color: #fff;
      display: flex;
      align-items: center;
      padding: 0 20px;
      z-index: 999;
      justify-content: space-between;
    }

    .hamburger {
      font-size: 24px;
      cursor: pointer;
    }

    .main {
      margin-left: 0;
      padding: 80px 20px 20px;
      flex: 1;
      width: 100%;
      transition: margin-left 0.3s ease;
    }

    .sidebar.open ~ .main {
      margin-left: 250px;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    @media (min-width: 768px) {
      .sidebar {
        transform: translateX(0);
      }
      .main {
        margin-left: 250px;
      }
      .hamburger {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
       <span class="hamburger" onclick="toggleSidebar()">☰</span>
    <h5>Admin Dashboard</h5>
    <nav>
      <a href="index.php">Dashboard</a>
      <a href="products.php">Products</a>
      <a href="customers.php">Customers</a>
      <a href="orders.php">Orders</a>
    
    </nav>
  </div>
