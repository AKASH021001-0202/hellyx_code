<?php include('header.php') ?>

<section id="hero">
  <h4>Trade-in-fair</h4>
  <h2>Super value deals</h2>
  <h1>On all Products</h1>
  <p>Save more with coupons and up to 70% off!</p>
  <button>Shop Now</button>
</section>

<section id="feature" class="section-p1">
  <div class="fe-box"><img src="images/f3.png" alt=""><h6>Free Shipping</h6></div>
  <div class="fe-box"><img src="images/f2.png" alt=""><h6>Online Order</h6></div>
  <div class="fe-box"><img src="images/f7.png" alt=""><h6>Save Money</h6></div>
  <div class="fe-box"><img src="images/f4.png" alt=""><h6>Promotions</h6></div>
  <div class="fe-box"><img src="images/f5.png" alt=""><h6>Happy Sell</h6></div>
  <div class="fe-box"><img src="images/f6.png" alt=""><h6>24/7 Support</h6></div>
</section>

<section id="product1" class="section-p1">
  <h2>Featured Products</h2>
  <p>Summer Collection New Modern Design</p>

  <div class="pro-container" id="featuredProducts">
    <!-- Products will be inserted here -->
  </div>

  <div class="d-flex justify-content-center mt-4">
    <button class="btn btn-outline-dark me-2" onclick="prevPage()">Previous</button>
    <button class="btn btn-outline-dark" onclick="nextPage()">Next</button>
  </div>
</section>

<section id="newsletter" class="section-p1">
  <div class="newstext">
    <h4>Sign Up for Newsletters</h4>
    <p>Get Email updates about our latest shop and <span>special offers</span></p>
  </div>
  <div class="form">
    <input type="text" placeholder="Your email address">
    <button class="btn normal">Sign Up</button>
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
        <span>Brand</span>
        <h5>${p.name}</h5>
        <div class="star">
          <i class="fas fa-star"></i><i class="fas fa-star"></i>
          <i class="fas fa-star"></i><i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
        </div>
        <h4>$${p.price.toFixed(2)}</h4>
      </div>
      <button class="btn btn-sm mt-2 add-to-cart-btn"
        data-id="${p._id}"
        data-name="${p.name}"
        data-price="${p.price}"
        data-image="${p.image}">
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
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".add-to-cart-btn");
  if (!btn) return;

  const product = {
    _id: btn.dataset.id,
    name: btn.dataset.name,
    price: parseFloat(btn.dataset.price),
    image: btn.dataset.image
  };
  addToCart(product);
});

async function addToCart(product) {
  try {
    const token = localStorage.getItem("userToken"); // Replace with cookie if needed
    if (!token) {
      alert("❌ Please login to add to cart");
      return;
    }

    const res = await fetch("http://localhost:8000/cart", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer " + token
      },
      body: JSON.stringify(product),
    });

    const result = await res.json();

    if (res.ok) {
      alert("✅ Item added to cart!");
    } else {
      alert(result.message || "Failed to add to cart.");
    }
  } catch (err) {
    console.error("Add to cart error:", err);
    alert("❌ Server error while adding to cart.");
  }
}
</script>

<?php include('footer.php') ?>
