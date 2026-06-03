<?php
require_once __DIR__ . '/includes/app.php';
app_start();

$mysqli = db_connect();

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $login_msg = "Invalid request.";
  } else {
    $username = sanitize_string($_POST['username']);
    $password = $_POST['password'];
  }
    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
      // Fetch subject
      $stmt2 = $mysqli->prepare("SELECT s.name FROM registrations r JOIN subjects s ON r.subject_id = s.id WHERE r.user_id = ? LIMIT 1");
      $stmt2->bind_param("i", $user_id);
      $stmt2->execute();
      $stmt2->bind_result($subject_name);
      $stmt2->fetch();
      $stmt2->close();

      $_SESSION['user_id'] = $user_id;
      $_SESSION['username'] = $username;
      $_SESSION['subject'] = $subject_name ?: 'No Subject';
      header("Location: index.php");
      exit;
    } else {
      $login_msg = "Invalid password.";
    }
  } else {
    $login_msg = "User not found.";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <?php render_stylesheet(); ?>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .container {
      background: #fff;
      max-width: 400px;
      margin: 40px auto;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #333;
    }
    form {
      border: 1px solid #ccc;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    input[type=text], input[type=password], select {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    input[type=submit] {
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    input[type=submit]:hover {
      background: #0056b3;
    }
    .msg {
      color: red;
      text-align: center;
    }
    .btn-link {
      display: block;
      text-align: center;
      margin-top: 10px;
      color: #007bff;
      text-decoration: none;
    }
    .btn-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Login</h2>
  <?php if (isset($login_msg)) echo "<div class='msg'>" . html_escape($login_msg) . "</div>"; ?>
  <form method="post">
    <?php echo csrf_input_field(); ?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
  </form>
  <a href="register.php" class="btn-link">Sign Up</a>
</div>
</body>
</html>