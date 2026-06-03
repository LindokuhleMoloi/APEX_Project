<?php
require_once __DIR__ . '/includes/app.php';
app_start();
require_user();

$mysqli = db_connect();

$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare(
    "SELECT u.username, u.email, s.name AS subject_name, r.status
     FROM users u
     LEFT JOIN registrations r ON u.id = r.user_id
     LEFT JOIN subjects s ON r.subject_id = s.id
     WHERE u.id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$grade_message = 'No grades available yet. Your trainer will publish grades after assessment is complete.';
if ($user && $user['status'] === 'Approved') {
    $grade_message = 'Your course has been approved. Grades will be available once assessments are submitted.';
} elseif ($user && $user['status'] === 'Pending') {
    $grade_message = 'Your registration is still pending approval. Grades will be published after approval and assessment.';
} elseif ($user && $user['status'] === 'Rejected') {
    $grade_message = 'Your course registration was rejected. Please contact administration for assistance.';
}

$sampleGrades = [
    ['assessment' => 'Assignment 1', 'grade' => '85%', 'status' => 'Completed'],
    ['assessment' => 'Midterm Quiz', 'grade' => '78%', 'status' => 'Completed'],
    ['assessment' => 'Practical Project', 'grade' => '92%', 'status' => 'Completed'],
    ['assessment' => 'Final Exam', 'grade' => 'Pending', 'status' => 'Not yet graded'],
];

$sectionProgress = [
    ['section' => 'Module 1 - Fundamentals', 'progress' => 90],
    ['section' => 'Module 2 - Networking', 'progress' => 75],
    ['section' => 'Module 3 - Programming', 'progress' => 65],
    ['section' => 'Module 4 - Database', 'progress' => 50],
    ['section' => 'Module 5 - Web Development', 'progress' => 40],
];

$avatarInitials = strtoupper(substr($user['username'] ?? $_SESSION['username'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grades</title>
  <?php render_stylesheet(); ?>
</head>
<body>
  <?php render_navigation(); ?>
  <div class="page-container">
    <div class="avatar-section">
      <div class="avatar-placeholder"><?php echo htmlspecialchars($avatarInitials); ?></div>
      <div>
        <h1>Grade Dashboard</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars($user['username'] ?? $_SESSION['username']); ?></strong>. Keep track of your progress across modules and assessments.</p>
      </div>
    </div>

    <div class="info-panel">
      <h2>Current Summary</h2>
      <p><strong>Course:</strong> <?php echo htmlspecialchars($user['subject_name'] ?? 'No course registered'); ?></p>
      <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status'] ?? 'Not registered'); ?></p>
      <div class="alert-box"><strong>Grade status:</strong> <?php echo htmlspecialchars($grade_message); ?></div>
    </div>

    <h2>Module Progress</h2>
    <?php foreach ($sectionProgress as $section): ?>
      <div class="progress-row">
        <div class="progress-title"><?php echo htmlspecialchars($section['section']); ?></div>
        <div class="progress-value"><?php echo htmlspecialchars($section['progress']); ?>%</div>
      </div>
      <div class="progress-bar-bg">
        <div class="progress-bar-fill" style="width: <?php echo intval($section['progress']); ?>%;"></div>
      </div>
    <?php endforeach; ?>

    <div class="grade-chart">
      <?php foreach ($sampleGrades as $grade): ?>
        <div class="grade-chart-item">
          <h3><?php echo htmlspecialchars($grade['assessment']); ?></h3>
          <p><?php echo htmlspecialchars($grade['grade']); ?></p>
          <p><?php echo htmlspecialchars($grade['status']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="course-card">
      <h2>What to do next</h2>
      <ul>
        <li>Review your module progress and focus on sections below 70%.</li>
        <li>Prepare for upcoming quizzes by checking the course resources.</li>
        <li>Visit the quizzes page for sample questions and learning tips.</li>
      </ul>
    </div>
  </div>
</body>
</html>
