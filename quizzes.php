<?php
require_once __DIR__ . '/includes/app.php';
app_start();
require_user();
$moduleId = isset($_GET['module']) ? intval($_GET['module']) : 0;
$moduleTitles = [
  1 => 'Introduction to IT',
  2 => 'Networking Basics',
  3 => 'Programming Fundamentals',
  4 => 'Database Concepts',
  5 => 'Web Development',
  6 => 'Cybersecurity Essentials',
];
$moduleTitle = $moduleTitles[$moduleId] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quizzes & Practice</title>
  <?php render_stylesheet(); ?>
</head>
<body>
  <?php render_navigation(); ?>
  <div class="page-container">
    <h1>Quizzes & Practice</h1>
    <?php if ($moduleId && $moduleTitle): ?>
      <div class="info-panel">
        <h2>Module <?php echo $moduleId; ?> Quiz</h2>
        <p>You are now viewing the quiz path for <strong><?php echo htmlspecialchars($moduleTitle); ?></strong>. Complete this quiz after finishing the module content on the portal home page.</p>
      </div>
    <?php else: ?>
      <p>Review these sample quizzes and learning activities to prepare for your course assessments.</p>
    <?php endif; ?>

    <div class="quiz-card">
      <div class="quiz-card-item">
        <h3>Quiz 1: Computer Fundamentals</h3>
        <p>10 multiple-choice questions covering hardware, software, and basic computing terminology.</p>
        <p><strong>Time:</strong> 15 minutes</p>
        <button class="btn">Start Quiz</button>
      </div>

      <div class="quiz-card-item">
        <h3>Quiz 2: Networking Basics</h3>
        <p>Test your understanding of network types, protocols, and common networking devices.</p>
        <p><strong>Time:</strong> 20 minutes</p>
        <button class="btn">Start Quiz</button>
      </div>

      <div class="quiz-card-item">
        <h3>Quiz 3: Web Development Essentials</h3>
        <p>Practice questions on HTML, CSS, JavaScript, and building simple web pages.</p>
        <p><strong>Time:</strong> 25 minutes</p>
        <button class="btn">Start Quiz</button>
      </div>
    </div>

    <div class="info-panel">
      <h2>Quiz Preparation Tips</h2>
      <ul>
        <li>Read the course material before beginning each quiz.</li>
        <li>Use the progress section in the grades page to identify weak areas.</li>
        <li>Save your work and review answers before submitting.</li>
      </ul>
    </div>

    <div class="course-card">
      <h2>More Learning Resources</h2>
      <p>Use the portal to review your registered course, check your profile details, and stay on top of upcoming assessments.</p>
      <p>Upload any supporting documents or avatars through the `uploads/` folder for future enhancements.</p>
    </div>
  </div>
</body>
</html>
