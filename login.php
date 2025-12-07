<?php
session_start();
if (isset($_SESSION["user"])) {
  header("Location: index.php");
}

include_once("functions.php");

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  checkLogin($_POST, $errors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>E-Jurnal Guru Login</title>
  <link rel="stylesheet" href="style.css"/>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap');

* {
  box-sizing: border-box;
}

body {
  font-family: 'Roboto Mono', monospace;
  background-color: #f5f5f5;
  margin: 0;

}

.container {
  width: 800px;
  max-width: 100%;
  margin: 50px auto;
  display: flex;
  box-shadow: 0 0 20px rgba(0,0,0,0.1);
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
}

.form-container img{
    width: 150px;
    height: auto;
     margin-bottom: 0px;

}
.form-container {
  width: 50%;
  padding: 60px 40px;
  background-color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;  /* Vertical center */
  height: 100vh;            /* Full viewport height */
  text-align: center;       /* Center text content */
}

.form-container h1 {
    margin-top: 5px; 
  margin-bottom: 30px;
  color: #2c3e50;
  font-size: 50px;
  font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  align-items: center;
}

input {
  width: 100%;
  padding: 15px;
  margin: 10px 0;
  border: none;
  background: #f1f1f1;
  box-shadow: 5px 5px 5px #ccc;
  border-radius: 10px;
  font-family: 'Roboto Mono', monospace;
}

button.signin {
  margin-top: 20px;
  padding: 15px 45px; 
  border: none;
  background-color: #f1f1f1;
  color: #2c3e50;
  font-weight: bold;
  border-radius: 20px;
  box-shadow: 5px 5px 5px #ccc;
  cursor: pointer;
}

button.signin:hover {
  background-color: #ddd;

}
button.signup {
  padding: 0.9rem 4rem;
  background-color: #cbe1fd;
  border: none;
  font-weight: bold;
  color: #2c3e50;
  border-radius: 20px;
   box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.422);
  cursor: pointer;
  margin-top: 20px;
}

button.signup:hover {
  background-color: #b0d3ef;
}

.overlay-container {
  width: 50%;
  background-color: #b1d1f8;
  display: flex;
  justify-content: center;
  align-items: center;
}

.overlay {
  text-align: center;
  padding: 40px;
  color: #2c3e50;
}

.overlay h1 {
  font-size: 40px;
  margin-bottom: 20px;
  font-weight: 700;
  font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
}

.overlay p {
  font-size: 14px;
  margin: 10px 0;
  font-weight: 400;
}

.forgot {
  font-size: 14px;
  margin-top: 10px;
  text-decoration: none;
  color: #2c3e50;
}

  </style>
</head>
<body>
  <div class="container">
    <div class="form-container sign-in-container">
      <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
        <img src="logo.png" alt="E-JurnalGuru Logo" class="logo"/>
        <h1>Sign In</h1>
        <input name="email" type="email" placeholder="Email" /><br>
        <input name="password" type="password" placeholder="Password" />
       <button type="submit" class="signin">Sign In</button><br><br>
        
    </form>
    <?php
    if (!empty($errors)) {
        echo '<div class="alert alert-danger mt-4" role="alert">';
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
        echo '</div>';
    }
    ?>
    </div>
    <div class="overlay-container">
      <div class="overlay">
        <h1>Welcome To<br/>E–JurnalGuru!</h1>
        <p>New Here?</p>
        <a href="BuatAkun.php">
          <button class="signup">Sign Up</button>
        </a>
      </div>
    </div>
  </div>
</body>
</html>
