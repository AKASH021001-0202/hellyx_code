
  document.getElementById('loginForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const saveLogin = document.getElementById('Save-login').checked;

    const payload = {
      email: email,
      password: password,
      saveLogin: saveLogin
    };

    try {
      const response = await fetch('https://your-api-endpoint.com/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (response.ok && data.token) {
        // Store token in localStorage
        localStorage.setItem('userToken', data.token);

        // Optional: store email if "save login" is checked
        if (saveLogin) {
          localStorage.setItem('savedEmail', email);
        } else {
          localStorage.removeItem('savedEmail');
        }

        // Redirect to index.php
        window.location.href = 'index.php';
      } else {
        alert(data.message || 'Login failed. Please try again.');
      }

    } catch (error) {
      console.error('Error during login:', error);
      alert('Could not connect to the server.');
    }
  });


