<?php
// dashboard.php

require_once __DIR__ . '/includes/app.php';
app_start();
require_admin();

// Database connection
$conn = db_connect();

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['request_id'])) {
  if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'] === 'accept' ? 'Accepted' : 'Rejected';
    $stmt = $conn->prepare("UPDATE registrations SET status=? WHERE id=?");
    $stmt->bind_param('si', $action, $request_id);
    $stmt->execute();
    $stmt->close();
  }
}

// Fetch all pending course registration requests
$sql = "SELECT cr.id, cr.status, u.id AS user_id, u.email as email ,u.username AS name, s.name AS course_name
    FROM registrations cr 
    JOIN users u ON cr.user_id = u.id 
    Join subjects s ON cr.subject_id = s.id
    WHERE cr.status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - Course Requests</title>
  <?php render_stylesheet(); ?>
  <style>
    body {
      background: #181a20;
      color: #f1f1f1;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    h1, h2 {
      text-align: center;
      color: #f1f1f1;
      margin-top: 24px;
    }
    table {
      border-collapse: collapse;
      width: 90%;
      margin: 30px auto;
      background: #23272f;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 16px rgba(0,0,0,0.4);
    }
    th, td {
      border: 1px solid #444;
      padding: 12px;
      text-align: left;
    }
    th {
      background: #2c313c;
      color: #f1f1f1;
    }
    tr:nth-child(even) {
      background: #20232a;
    }
    tr:nth-child(odd) {
      background: #23272f;
    }
    button[type="submit"] {
      padding: 6px 16px;
      margin-right: 6px;
      border: none;
      border-radius: 4px;
      background: #007bff;
      color: #fff;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.2s;
    }
    button[type="submit"]:hover {
      background: #0056b3;
    }
    form {
      display: inline;
    }
  </style>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <h2>Pending Course Registration Requests</h2>
  <table>
    <tr>
      <th>Learner Name</th>
      <th>Email</th>
      <th>Course</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['course_name']) ?></td>
      <td>
        <form method="post" style="display:inline;">
          <?php echo csrf_input_field(); ?>
          <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['id']); ?>">
          <button type="submit" name="action" value="accept">Accept</button>
          <button type="submit" name="action" value="reject">Reject</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
<?php
$conn->close();
?>