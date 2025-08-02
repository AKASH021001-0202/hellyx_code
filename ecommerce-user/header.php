<?php include('head.php') ?>

<style>
  #header .logo {
    width: 7%  !important;
}
</style>
<section id="header">
  <img class="logo" src="images/aks.webp" />
  <div>
    <ul id="navbar">
    
            <li><a href="index.php">Home</a></li>
            <li><a onclick="logout()">My Order</a></li>
            <li>
              <a href="cart.php" id="lg-bag"><i class="fal fa-shopping-bag"></i></a>
           
            </li>
            <li><a onclick="logout()">Log Out</a></li>
      <li>
        <a href="#" id="close"><i class="far fa-times"></i></a>
      </li>
    </ul>
  </div>
    <div id="mobile">
    <a href="cart.php"
      ><i class="fal fa-shopping-bag"></i>
      <span class="quantity">0</span>
    </a>
    <i id="bar" class="fas fa-outdent"></i>
  </div>
</section>

<script>
  function logout() {
    localStorage.removeItem("userToken");
    window.location.href = "login.php";
  }
</script>