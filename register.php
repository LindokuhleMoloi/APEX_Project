<?php
require_once __DIR__ . '/includes/app.php';
app_start();

$mysqli = db_connect();

// Fetch subjects for dropdown
$subjects = [];
$result = $mysqli->query("SELECT id, name FROM subjects");
while ($row = $result->fetch_assoc()) {
  $subjects[] = $row;
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $message = "Invalid request.";
  } else {
    $username = sanitize_string($_POST["username"]);
    $password = $_POST["password"];
    $email = sanitize_string($_POST["email"]);
    $subject_id = intval($_POST["subject_id"]);
  }
    if ($username && $password && $email && $subject_id) {
    // Check if username exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $message = "Username already exists.";
    } else {
      // Insert user
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $hashed_password, $email);
      if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        // Register subject
        $stmt2 = $mysqli->prepare("INSERT INTO registrations (user_id, subject_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $user_id, $subject_id);
        $stmt2->execute();
        $message = "Registration successful!";
      } else {
        $message = "Error registering user.";
      }
    }
    $stmt->close();
  } else {
    $message = "Please fill all fields.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
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
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 24px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }
    input[type="text"],
    input[type="password"],
    input[type="email"],
    select {
      width: 100%;
      padding: 8px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    input[type="submit"] {
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    input[type="submit"]:hover {
      background: #0056b3;
    }
    p {
      text-align: center;
      color: #d8000c;
      margin-bottom: 16px;
    }
    .btn-link {
      display: inline-block;
      width: 100%;
      text-align: center;
      padding: 10px;
      margin-top: 10px;
      background: #6c757d;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
    }
    .btn-link:hover {
      background: #5a6268;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>User Registration</h2>
    <?php if ($message) echo "<p>" . html_escape($message) . "</p>"; ?>
    <form method="post" action="">
      <?php echo csrf_input_field(); ?>
      <label>Username:</label>
      <input type="text" name="username" required>
      <label>Password:</label>
      <input type="password" name="password" required>
      <label>Email:</label>
      <input type="email" name="email" required>
      <label>Subject:</label>
      <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $subject): ?>
          <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
        <?php endforeach; ?>
      </select>
      <input type="submit" value="Register">
      <a href="login.php" class="btn-link">Login</a>
    </form>
  </div>
</body>
</html>