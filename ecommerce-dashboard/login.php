<?php include('head.php'); ?>

<section class="loginpage">
  <div class="container">
    <!-- ✅ Form won't auto-submit -->
    <form id="loginForm" action="javascript:void(0);" novalidate>
      <h2>Admin Login-form</h2>

      <div class="input-field">
        <input type="email" id="email" name="email" required />
        <label>Enter Email</label>
      </div>

      <div class="input-field">
        <input type="password" id="password" name="password" required />
        <label>Enter Password</label>
      </div>

      <div class="forget">
        <label for="Save-login">
          <input type="checkbox" id="Save-login" name="saveLogin" />
          Save login information
        </label>
   
      </div>

      <!-- ✅ Use type="button" to prevent native submit -->
      <button type="button" id="loginBtn">Log In</button>

      <div class="Create-account">
        <p>Don't have an account? <a href="register.php">Create account</a></p>
      </div>
    </form>
  </div>
</section>

<script>

  function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    expires = ";expires=" + d.toUTCString();
  }
  document.cookie = name + "=" + encodeURIComponent(value) + expires + ";path=/";
}

document.addEventListener("DOMContentLoaded", function () {
  const loginBtn = document.getElementById("loginBtn");
  const form = document.getElementById("loginForm");

  loginBtn.addEventListener("click", async function () {
    const email = form.email.value.trim();
    const password = form.password.value.trim();

    loginBtn.disabled = true;
    loginBtn.textContent = "Logging in...";

    try {
      const res = await fetch("http://localhost:8000/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password, role: "admin" }),
      });

      const result = await res.json();

      if (res.ok && result.token) {
        const { token, user } = result;

        if (user.role === "admin") {
          setCookie("adminToken", token, 7); // store for 7 days
          alert("✅ Admin login successful");
          window.location.href = "index.php";
        } else {
          alert("❌ You are not an admin");
        }
      } else {
        alert("❌ " + (result.message || "Login failed"));
      }
    } catch (err) {
      console.error("Login error:", err);
      alert("❌ Server error");
    } finally {
      loginBtn.disabled = false;
      loginBtn.textContent = "Log In";
    }
  });
});

</script>


<?php include('bottombar.php')?>
