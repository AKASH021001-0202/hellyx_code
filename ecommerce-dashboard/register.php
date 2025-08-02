<?php include('head.php') ?>
<script>
  if (localStorage.getItem("authToken")) {
    window.location.href = "index.php";
  }
</script>
<section class="loginpage">
  <div class="container">
    <form id="registerForm">
      <h2>Register-form</h2>

      <div class="input-field">
        <input type="text" name="name" id="name" required />
        <label>Enter Name</label>
      </div>

      <div class="input-field">
        <input type="email" name="email" id="email" required />
        <label>Enter Email</label>
      </div>

      <div class="input-field">
        <input type="number" name="phone" id="phone" required />
        <label>Phone Number</label>
      </div>

      <div class="input-field">
        <input type="password" name="password" id="password" required />
        <label>Enter Password</label>
      </div>

      <div class="input-field">
        <input type="password" name="confirm_password" required />
        <label>Confirm Password</label>
      </div>

      <button type="submit">Submit</button>

      <div class="Create-account">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </form>
  </div>
</section>

<script>
  document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      const res = await fetch("http://localhost:8000/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          name,
          email,
          phone,
          password,
          role: "admin"
        })
      });

      const data = await res.json();

      if (res.status === 201) {
        alert("✅ " + data.msg);
        window.location.href = "login.php"; // success redirect
      } else {
        alert("❌ " + data.msg);
      }
    } catch (err) {
      console.error("Registration Error:", err);
      alert("❌ Something went wrong. Please try again.");
    }
  });
</script>

<?php include('footer-bottom.php') ?>