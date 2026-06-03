<?php
require_once __DIR__ . '/includes/app.php';
app_start();
require_user();

$mysqli = db_connect();
$user_id = $_SESSION['user_id'];
$profileUpdateMessage = '';
$theme = $_SESSION['profile_theme'] ?? 'default';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = sanitize_string($_POST['username'] ?? '');
    $email = sanitize_string($_POST['email'] ?? '');
    $theme = in_array($_POST['theme'] ?? '', ['default', 'soft', 'dark'], true) ? $_POST['theme'] : 'default';

    if ($username && $email) {
        $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $username, $email, $user_id);
        if ($stmt->execute()) {
            $profileUpdateMessage = 'Profile updated successfully.';
            $_SESSION['username'] = $username;
            $_SESSION['profile_theme'] = $theme;
        } else {
            $profileUpdateMessage = 'Unable to save changes. Please try again.';
        }
        $stmt->close();
    } else {
        $profileUpdateMessage = 'Please provide both username and email.';
    }
}

$stmt = $mysqli->prepare(
    "SELECT u.username, u.email, s.name AS subject_name, r.status, r.registration_date
     FROM users u
     LEFT JOIN registrations r ON u.id = r.user_id
     LEFT JOIN subjects s ON r.subject_id = s.id
     WHERE u.id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

$username = $profile['username'] ?? $_SESSION['username'] ?? 'Student';
$avatarInitials = strtoupper(substr($username, 0, 1));
$themeClass = 'theme-' . ($theme === 'soft' ? 'soft' : ($theme === 'dark' ? 'dark' : 'default'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <?php render_stylesheet(); ?>
</head>
<body class="<?php echo htmlspecialchars($themeClass); ?>">
  <?php render_navigation(); ?>
  <div class="page-container">
    <div class="avatar-section">
      <div class="avatar-placeholder"><?php echo htmlspecialchars($avatarInitials); ?></div>
      <div>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <p>Your profile shows your current registration and customization options.</p>
        <p><strong>Registered Course:</strong> <?php echo htmlspecialchars($profile['subject_name'] ?? 'No course registered'); ?></p>
      </div>
    </div>

    <?php if ($profileUpdateMessage): ?>
      <div class="alert-box"><?php echo htmlspecialchars($profileUpdateMessage); ?></div>
    <?php endif; ?>

    <div class="profile-card">
      <h2>Edit Your Details</h2>
      <p>Update your display details, choose a theme, and manage your upload preferences.</p>
      <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="theme">Portal Theme</label>
            <select id="theme" name="theme">
              <option value="default"<?php echo $theme === 'default' ? ' selected' : ''; ?>>Default</option>
              <option value="soft"<?php echo $theme === 'soft' ? ' selected' : ''; ?>>Soft Blue</option>
              <option value="dark"<?php echo $theme === 'dark' ? ' selected' : ''; ?>>Dark Mode</option>
            </select>
          </div>
          <div class="form-group file-upload">
            <label for="avatar_file">Avatar / Document Upload</label>
            <input type="file" id="avatar_file" name="avatar_file">
            <small>File uploads are supported through the `uploads/` folder for avatars and documents.</small>
          </div>
        </div>

        <button type="submit" name="update_profile" class="btn">Save Changes</button>
      </form>
    </div>

    <div class="course-card">
      <h2>Account Information</h2>
      <dl class="profile-info">
        <dt>Username</dt>
        <dd><?php echo htmlspecialchars($username); ?></dd>
        <dt>Email</dt>
        <dd><?php echo htmlspecialchars($profile['email'] ?? 'Not provided'); ?></dd>
        <dt>Registration Status</dt>
        <dd><?php echo htmlspecialchars($profile['status'] ?? 'Not registered'); ?></dd>
        <dt>Registration Date</dt>
        <dd><?php echo htmlspecialchars($profile['registration_date'] ?? '-'); ?></dd>
      </dl>
    </div>

    <div class="info-panel">
      <h2>Personalize Your View</h2>
      <ul>
        <li>Choose a theme to customize the portal experience.</li>
        <li>Upload avatars and documents using the `uploads/` folder.</li>
        <li>Use the profile edit form to keep your details current.</li>
      </ul>
    </div>
  </div>
</body>
</html>
