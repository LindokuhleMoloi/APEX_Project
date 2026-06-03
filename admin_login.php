<?php
require_once __DIR__ . '/includes/app.php';
app_start();

$mysqli = db_connect();

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid request.';
  } else {
    $username = sanitize_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT password FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($hashed_password);
      $stmt->fetch();
      if (password_verify($password, $hashed_password) || $password === $hashed_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
      } else {
        $error = 'Invalid username or password.';
      }
    } else {
      $error = 'Invalid username or password.';
    }
    $stmt->close();
  }
}

// Handle admin registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $register_error = 'Invalid request.';
  } else {
    $new_username = sanitize_string($_POST['new_username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if ($new_username && $new_password) {
      // Check if username exists
      $stmt = $mysqli->prepare("SELECT id FROM admin WHERE username = ?");
      $stmt->bind_param("s", $new_username);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows > 0) {
        $register_error = "Username already exists.";
      } else {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt2 = $mysqli->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt2->bind_param("ss", $new_username, $hashed_new_password);
        if ($stmt2->execute()) {
          $register_success = "Admin registered successfully!";
        } else {
          $register_error = "Registration failed.";
        }
        $stmt2->close();
      }
      $stmt->close();
    } else {
      $register_error = "Please fill all fields.";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <?php render_stylesheet(); ?>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #181a20;
      margin: 0;
      padding: 0;
      color: #f1f1f1;
    }
    .container {
      background: #23272f;
      max-width: 350px;
      margin: 60px auto;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.4);
    }
    h2 {
      text-align: center;
      margin-bottom: 24px;
      color: #f1f1f1;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #e0e0e0;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 16px;
      border: 1px solid #444;
      border-radius: 4px;
      background: #181a20;
      color: #f1f1f1;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #007bff;
      outline: none;
      background: #23272f;
    }
    button[type="submit"] {
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.2s;
    }
    button[type="submit"]:hover {
      background: #0056b3;
    }
    p {
      text-align: center;
      color: #ff4c4c;
      margin-bottom: 16px;
    }
    .success {
      color: #4caf50;
      text-align: center;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Admin Login</h2>
    <?php if (!empty($error)): ?>
      <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <?php echo csrf_input_field(); ?>
      <label>Username:</label>
      <input type="text" name="username" required>
      <label>Password:</label>
      <input type="password" name="password" required>
      <button type="submit" name="login">Login</button>
    </form>

    <h2>Register Admin</h2>
    <?php if (!empty($register_error)): ?>
      <p><?php echo htmlspecialchars($register_error); ?></p>
    <?php endif; ?>
    <?php if (!empty($register_success)): ?>
      <p class="success"><?php echo htmlspecialchars($register_success); ?></p>
    <?php endif; ?>
    <form method="post" action="">
      <?php echo csrf_input_field(); ?>
      <label>New Username:</label>
      <input type="text" name="new_username" required>
      <label>New Password:</label>
      <input type="password" name="new_password" required>
      <button type="submit" name="register">Register</button>
    </form>
  </div>
</body>
</html>