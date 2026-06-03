<?php
require_once __DIR__ . '/includes/app.php';
app_start();
require_user();

$mysqli = db_connect();

$user_id = $_SESSION['user_id'];
$cachedCourses = cache_get('subjects_for_user_' . $user_id, 300);
if ($cachedCourses !== null) {
    $courses = $cachedCourses;
} else {
    $stmt = $mysqli->prepare(
        "SELECT s.id, s.name,
                COALESCE(r.status, 'Not registered') AS status,
                r.registration_date
         FROM subjects s
         LEFT JOIN registrations r ON s.id = r.subject_id AND r.user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    cache_set('subjects_for_user_' . $user_id, $courses, 120);
}
$enrolledCourses = array_filter($courses, function ($course) {
    return !empty($course['status']) && strtolower($course['status']) !== 'not registered';
});
$firstEnrolled = reset($enrolledCourses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Courses</title>
  <?php render_stylesheet(); ?>
</head>
<body>
  <?php render_navigation(); ?>
  <div class="page-container course-page">
    <div class="courses-header">
      <div>
        <h1>Courses</h1>
        <p>Below are the available subjects and your current registration status.</p>
      </div>
      <?php if (!empty($firstEnrolled)): ?>
        <div class="course-actions">
          <a href="modules/module1/index.php" class="btn btn-primary">Continue your course</a>
        </div>
      <?php endif; ?>
    </div>
    <table class="course-table">
      <tr>
        <th>Course</th>
        <th>Status</th>
        <th>Registration Date</th>
        <th>Action</th>
      </tr>
      <?php foreach ($courses as $course): ?>
        <tr>
          <td><?php echo htmlspecialchars($course['name']); ?></td>
          <td><?php echo htmlspecialchars($course['status']); ?></td>
          <td><?php echo htmlspecialchars($course['registration_date'] ?: '-'); ?></td>
          <td>
            <?php if (!empty($course['status']) && strtolower($course['status']) !== 'not registered'): ?>
              <a href="modules/module1/index.php" class="btn btn-soft">Continue</a>
            <?php else: ?>
              <span class="course-label">Not enrolled</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <div class="alert-box course-info-card">
      <h2>Sample Course Description</h2>
      <p>This portal supports courses such as:</p>
      <ul>
        <li><strong>End User Computing NQF3</strong> – basic productivity tools and computer literacy.</li>
        <li><strong>Software Development NQF4</strong> – programming, databases, and application design.</li>
      </ul>
      <p>Once registered, you will receive a full list of modules and assessments for your chosen course.</p>
    </div>
  </div>
</body>
</html>
