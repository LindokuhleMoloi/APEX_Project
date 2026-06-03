<?php
require_once __DIR__ . '/includes/app.php';
app_start();

$requestedModule = isset($_GET['module']) ? (int) $_GET['module'] : 0;
$contentType = isset($_GET['content']) ? $_GET['content'] : 'module';
$moduleNumber = $requestedModule > 0 ? $requestedModule : null;
$previousModule = $moduleNumber && $moduleNumber > 1 ? $moduleNumber - 1 : 1;
$pageTitle = $contentType === 'quiz' ? 'Quiz Locked' : 'Module Locked';
$message = $moduleNumber
    ? sprintf('Oops! Module %d is still locked. Finish Module %d first to unlock this content.', $moduleNumber, $previousModule)
    : 'Oops! This content is currently locked. Finish the current module to continue.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo html_escape($pageTitle); ?> - Student Portal</title>
  <?php render_stylesheet(); ?>
</head>
<body>
  <?php render_navigation(); ?>
  <div class="page-container locked-page">
    <section class="locked-hero-card">
      <div class="locked-icon">⚠️</div>
      <div>
        <h1><?php echo html_escape($pageTitle); ?></h1>
        <p><?php echo html_escape($message); ?></p>
      </div>
    </section>

    <section class="locked-info-card">
      <h2>Keep your learning flow</h2>
      <p>Complete the currently unlocked module first. The course follows a step-by-step drip path, and this helps you master the material before moving on.</p>
      <div class="locked-actions">
        <a href="<?php echo htmlspecialchars(site_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Return to course path</a>
        <a href="<?php echo htmlspecialchars(site_url("modules/module{$previousModule}/index.php"), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Continue Module <?php echo $previousModule; ?></a>
      </div>
    </section>
  </div>
  <footer>
    <p>&copy; <?php echo date('Y'); ?> Student Portal</p>
  </footer>
</body>
</html>
