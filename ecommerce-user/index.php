<?php include('header.php') ?>

<section id="hero">
  <h4>Trade-in-fair</h4>
  <h2>Super value deals</h2>
  <h1>On all Products</h1>
  <p>Save more with coupons and up to 70% off!</p>
  <button>Shop Now</button>

</section>

<section id="feature" class="section-p1">
  <div class="fe-box">
    <img src="images/f3.png" alt="">
    <h6>Free Shipping</h6>
  </div>

  <div class="fe-box">
    <img src="images/f2.png" alt="">
    <h6>Online Order</h6>
  </div>

  <div class="fe-box">
    <img src="images/f7.png" alt="">
    <h6>Save Money</h6>
  </div>

  <div class="fe-box">
    <img src="images/f4.png" alt="">
    <h6>Promotions</h6>
  </div>

  <div class="fe-box">
    <img src="images/f5.png" alt="">
    <h6>Happy Sell</h6>
  </div>

  <div class="fe-box">
    <img src="images/f6.png" alt="">
    <h6>F24/7 Support</h6>
  </div>

</section>
<section id="sm-banner" class="section-p1">
  <div class="banner-box">
    <h4>crazy deals</h4>
    <h2>buy 1 get 1 free</h2>
    <span>The best classic dress is on sales at cara</span>
    <button class="btn white">Learn More</button>

  </div>

  <div class="banner-box banner-box2">
    <h4>spring/summer</h4>
    <h2>upcoming season</h2>
    <span>The best classic dress is on sales at cara</span>
    <button class="btn white">Collection</button>

  </div>

</section>

<section id="product1" class="section-p1">
  <h2>Featured Products</h2>
  <p>Summer Collection New Modern Design</p>

  <div class="pro-container" id="featuredProducts">
    <!-- Products load here -->
  </div>

  <div class="d-flex justify-content-center mt-4">
    <button class="btn btn-outline-dark me-2" onclick="prevPage()">Previous</button>
    <button class="btn btn-outline-dark" onclick="nextPage()">Next</button>
  </div>
</section>







<section id="newsletter" class="section-p1">
  <div class="newstext">
    <h4>Sign Up for Newsletters</h4>
    <p>Get Email updates about our latest shop and <span> special offers.</span> </p>
  </div>
  <div class="form">
    <input type="text" placeholder="Your email address">
    <button class="btn normal">Sign Up</button>
  </div>

  </div>

</section>
<script>
  let products = [];
  let currentPage = 1;
  const productsPerPage = 8;

  async function loadFeaturedProducts() {
    try {
      const res = await fetch("http://localhost:8000/products");
      const data = await res.json();
      products = data.products || [];
      renderProducts();
    } catch (err) {
      console.error("Error fetching products:", err);
    }
  }

  function renderProducts() {
    const container = document.getElementById("featuredProducts");
    container.innerHTML = "";

    const start = (currentPage - 1) * productsPerPage;
    const end = start + productsPerPage;
    const pageProducts = products.slice(start, end);

    if (pageProducts.length === 0 && currentPage > 1) {
      currentPage--;
      renderProducts();
      return;
    }

    container.innerHTML = pageProducts.map(p => `
      <div class="pro">
        <img src="${p.image}" loading="lazy" alt="${p.name}">
        <div class="des">
          <span>adidas</span>
          <h5>${p.name}</h5>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h4>$${p.price.toFixed(2)}</h4>
        </div>
        <button class="btn btn-sm  mt-2" onclick='addToCart(${JSON.stringify(p)})'>
          <i class="fas fa-cart-plus"></i> Add to Cart
        </button>
      </div>
    `).join("");
  }


  function nextPage() {
    if ((currentPage * productsPerPage) < products.length) {
      currentPage++;
      renderProducts();
    }
  }

  function prevPage() {
    if (currentPage > 1) {
      currentPage--;
      renderProducts();
    }
  }

  document.addEventListener("DOMContentLoaded", loadFeaturedProducts);
</script>
<script>
  async function addToCart(product) {
    console.log(localStorage.getItem("userToken")); // or adminToken if admin

    try {
     const token = localStorage.getItem("userToken"); // or use getCookie("userToken")
console.log("Sending token:", token);

const res = await fetch("http://localhost:8000/cart", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Authorization": "Bearer " + token
  },
        body: JSON.stringify({
       _id: product._id, 
          name: product.name,
          price: product.price,
          image: product.image,
        }),
      });

      const result = await res.json();

      if (res.ok) {
        alert("✅ Item added to cart!");
      } else {
        alert(result.message || "Failed to add to cart.");
      }
    } catch (err) {
      console.error("Add to cart error:", err);
      alert("Server error");
    }
  }
</script>



<?php include('footer.php') ?>