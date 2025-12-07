<?php
session_start();
include("functions.php");
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  buatakun($_POST, $errors);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buat Akun</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    .buat-akun-page .container {
      display: flex;
      height: 100vh;
    }

    .buat-akun-page .left-panel {
      width: 50%;
      background-color: #a4c9ea;
      color: #2e3b4e;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .buat-akun-page .left-panel h1 {
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 20px;
      font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    .buat-akun-page .left-panel p {
      margin-bottom: 20px;
      color: #2e3b4e;
    }

    .buat-akun-page .signin-btn {
      padding: 0.9rem 4rem;
      background-color: #c7def3;
      color: #2e3b4e;
      font-weight: bold;
      border: none;
      border-radius: 20px;
      box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.422);
      text-decoration: none;
      transition: background 0.3s;
    }

    .buat-akun-page .signin-btn:hover {
      background-color: #b0d3ef;
    }

    .buat-akun-page .right-panel {
      width: 50%;
      background-color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .buat-akun-page .right-panel .logo {
      width: 100px;
      margin-bottom: 20px;
    }

    .buat-akun-page .right-panel h2 {
      font-size: 32px;
      color: #2e3b4e;
      margin-bottom: 30px;
      font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    .buat-akun-page .right-panel form {
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: 300px;
    }

    .buat-akun-page .right-panel input,
    .buat-akun-page .right-panel select {
      padding: 12px 15px;
      margin-bottom: 20px;
      border: none;
      border-radius: 8px;
      background-color: #f6f6f6;
      box-shadow: 6px 6px 6px rgba(0, 0, 0, 0.2);
      font-size: 14px;
      color: #2e3b4e;
    }

    .buat-akun-page .right-panel select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-color: #f6f6f6;
    }

    .buat-akun-page .right-panel select:focus,
    .buat-akun-page .right-panel input:focus {
      outline: none;
      background-color: #e9e9e9;
    }

    .buat-akun-page .right-panel select option {
      background-color: #ffffff;
      color: #2e3b4e;
    }

    .buat-akun-page .signup-btn {
      padding: 15px 45px;
      align-self: center;
      background-color: #f6f6f6;
      color: #2e3b4e;
      font-weight: bold;
      border: none;
      border-radius: 20px;
      box-shadow: 6px 6px 6px rgba(0, 0, 0, 0.351);
      transition: background 0.3s;
    }

    .buat-akun-page .signup-btn:hover {
      background-color: #ddd;
    }
  </style>
</head>
<body>
  <div class="buat-akun-page">
    <div class="container">
      <!-- Kiri -->
      <div class="left-panel">
        <h1>Welcome back To<br> E–JurnalGuru!</h1>
        <p>Already Have An Account?</p>
        <a href="login.php" class="signin-btn">Log In</a>
      </div>

      <!-- Kanan -->
      <div class="right-panel">
        <img src="logo.png" alt="Logo" class="logo" />
        <h2>Create Account</h2>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
          <input type="text" name="nama_user" placeholder="Name" required />
          <input type="email" name="email" placeholder="Email" required />
          <input type="number" name="no_hp" placeholder="No HP" required />
          <input type="password" name="password" placeholder="Password" required />
          <select name="role" readonly>
            <option value="1" selected>Guru</option>
          </select>
          <button type="submit" class="signup-btn">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
