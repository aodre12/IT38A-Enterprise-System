<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // TODO: Connect to DB and insert into users table
    // Example: mysqli_query($conn, "INSERT INTO users (...) VALUES (...)");

    echo "<script>alert('Account created successfully! You can now log in.'); window.location.href='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="style.css"> <!-- Optional: Your global CSS -->
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('background.gif') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 30px 40px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="register-container">
  <h2>Create an Account</h2>
  <form method="POST" action="register.php">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
  </form>

  <div class="login-link">
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</div>

</body>
</html>
