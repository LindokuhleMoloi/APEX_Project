<?php
// index.php - Student Portal Home Page

require_once __DIR__ . '/includes/app.php';
app_start();
$isLoggedIn = isset($_SESSION['user_id']);
$subject = $isLoggedIn ? ($_SESSION['subject'] ?? 'No Subject Selected') : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Portal</title>
  <?php render_stylesheet(); ?>
  <?php render_script('js/index.js'); ?>
</head>
<body>
  <?php render_navigation(); ?>
  <div class="page-container">
    <header>
      <div class="section-header">
        <div>
          <h1>Welcome to the Student Portal</h1>
          <?php if ($isLoggedIn): ?>
            <div class="subject-header">Your IT Qualification: <strong><?php echo htmlspecialchars($subject); ?></strong></div>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! Ready to continue your course path?</p>
          <?php else: ?>
            <p>Please log in to access your dashboard and course materials.</p>
          <?php endif; ?>
        </div>
        <?php if ($isLoggedIn): ?>
          <a href="courses.php" class="btn btn-primary">Open My Courses</a>
        <?php endif; ?>
      </div>
    </header>
    <?php if ($isLoggedIn): ?>
      <?php
        $modules = [
          [
            'step' => 1,
            'title' => 'Introduction to IT',
            'description' => 'Learn the fundamentals of IT, technology literacy, and course expectations.',
            'details' => 'Complete the orientation video, review the syllabus, and answer the warm-up reflection questions.',
            'quiz_link' => 'modules/module1/quiz.php',
            'state' => 'Available',
            'enabled' => true,
          ],
          [
            'step' => 2,
            'title' => 'Networking Basics',
            'description' => 'Understand network types, protocols, and how devices communicate.',
            'details' => 'Study topology diagrams, compare LAN/WAN models, and complete the network mapping activity.',
            'quiz_link' => 'modules/module2/quiz.php',
            'state' => 'Locked',
            'enabled' => false,
          ],
          [
            'step' => 3,
            'title' => 'Programming Fundamentals',
            'description' => 'Explore programming logic, variables, and foundational code concepts.',
            'details' => 'Work through beginner exercises, trace code flow, and prepare for the applied coding quiz.',
            'quiz_link' => 'modules/module3/quiz.php',
            'state' => 'Locked',
            'enabled' => false,
          ],
          [
            'step' => 4,
            'title' => 'Database Concepts',
            'description' => 'Build confidence with data storage, SQL basics, and database design.',
            'details' => 'Review table structure, practice SELECT queries, and visualize how data is organized.',
            'quiz_link' => 'modules/module4/quiz.php',
            'state' => 'Locked',
            'enabled' => false,
          ],
          [
            'step' => 5,
            'title' => 'Web Development',
            'description' => 'Apply HTML, CSS, and responsive design principles to build pages.',
            'details' => 'Design a simple page layout, test responsiveness, and prepare for the front-end quiz.',
            'quiz_link' => 'modules/module5/quiz.php',
            'state' => 'Locked',
            'enabled' => false,
          ],
          [
            'step' => 6,
            'title' => 'Cybersecurity Essentials',
            'description' => 'Learn the basics of online safety, threat types, and risk reduction.',
            'details' => 'Complete security awareness tasks, compare threat vectors, and ready yourself for the final quiz.',
            'quiz_link' => 'modules/module6/quiz.php',
            'state' => 'Locked',
            'enabled' => false,
          ],
        ];
      ?>

      <div class="dashboard">
        <div class="main-content course-flow">
          <div class="course-card">
            <div class="section-header">
              <div>
                <h2>Course Drip Path</h2>
                <p>Modules unlock sequentially. Finish each module to unlock the next quiz.</p>
              </div>
              <span class="course-chip">Drip release strategy</span>
            </div>

            <?php foreach ($modules as $module): ?>
              <article class="module-card<?php echo $module['enabled'] ? '' : ' module-locked'; ?>" data-step="<?php echo $module['step']; ?>">
                <div class="module-header">
                  <div>
                    <h3 class="module-title">Module <?php echo $module['step']; ?>: <?php echo htmlspecialchars($module['title']); ?></h3>
                    <p class="module-description"><?php echo htmlspecialchars($module['description']); ?></p>
                  </div>
                  <div class="module-status"><?php echo htmlspecialchars($module['state']); ?></div>
                </div>

                <div class="module-details">
                  <p><?php echo htmlspecialchars($module['details']); ?></p>
                </div>

                <div class="module-actions">
                  <a href="<?php echo $module['enabled'] ? 'modules/module' . $module['step'] . '/index.php' : 'locked_module.php?module=' . $module['step']; ?>" class="btn btn-soft<?php echo $module['enabled'] ? '' : ' btn-disabled-link'; ?>"><?php echo $module['enabled'] ? 'View module' : 'Module locked'; ?></a>
                  <button type="button" class="btn btn-primary complete-module"<?php echo $module['enabled'] ? '' : ' disabled'; ?>>Mark module complete</button>
                  <a href="<?php echo $module['enabled'] ? $module['quiz_link'] : 'locked_module.php?module=' . $module['step'] . '&content=quiz'; ?>" class="btn btn-secondary quiz-link<?php echo $module['enabled'] ? '' : ' btn-disabled-link'; ?>">Start quiz</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>

        <aside class="sidebar course-drip">
          <h3>Upcoming modules</h3>
          <p>Follow each step in order. Quizzes appear after each module to help you retain the concepts.</p>
          <ul class="drip-list">
            <li class="drip-list-item"><span></span><strong>Module 1</strong> unlocked now</li>
            <li class="drip-list-item"><span></span><strong>Module 2</strong> unlocks after Module 1</li>
            <li class="drip-list-item"><span></span><strong>Module 3</strong> unlocks after Module 2</li>
            <li class="drip-list-item"><span></span><strong>Module 4</strong> unlocks after Module 3</li>
            <li class="drip-list-item"><span></span><strong>Module 5</strong> unlocks after Module 4</li>
            <li class="drip-list-item"><span></span><strong>Module 6</strong> unlocks after Module 5</li>
          </ul>
        </aside>
      </div>
    <?php endif; ?>
  </div>
  <footer>
    <p>&copy; <?php echo date('Y'); ?> Student Portal</p>
  </footer>
</body>
</html>