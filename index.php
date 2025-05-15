<!DOCTYPE html>
<html>
<head>
  <title>Welcome to My UtiliTrack</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fef6e4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: #fff;
      width: 350px;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      text-align: center;
      position: relative;
    }

    .header-wave {
      position: absolute;
      top: 0;
      left: 0;
      height: 100px;
      width: 100%;
      background: #001F54;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      clip-path: ellipse(100% 80% at 50% 0%);
    }

    h2 {
      margin-top: 100px;
      font-size: 28px;
      font-weight: 700;
      color: #000;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 30px;
    }

    .button-group a {
      padding: 10px 20px;
      background-color: #0056b3;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .button-group a:hover {
      background-color: #003d80;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="header-wave"></div>
    <h2>Welcome to My UtiliTrack</h2>
    <p>Please choose your role:</p>
    <div class="button-group">
      <a href="admin_login.php">Admin</a>
      <a href="login.php">User</a>
    </div>
  </div>

</body>
</html>
