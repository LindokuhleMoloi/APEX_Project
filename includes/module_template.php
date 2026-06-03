<?php
function get_module_metadata(): array
{
    return [
        1 => [
            'title' => 'Introduction to IT',
            'overview' => 'Build a strong foundation in IT literacy, hardware, software, and the role of technology in the workplace.',
            'goal' => 'Learn how computers, software, networks and data work together to support everyday tasks.',
            'topics' => [
                'IT concepts and terminology',
                'Difference between hardware and software',
                'File management and basic productivity tools',
            ],
            'learning_outcomes' => [
                'Understand basic IT terminology and systems.',
                'Recognise the role of hardware and software in daily computing.',
                'Explore the first steps in managing digital information securely.',
            ],
        ],
        2 => [
            'overview' => 'Explore networking fundamentals, including how devices connect, communicate, and share information safely.',
            'goal' => 'Learn how computers and devices communicate over wired and wireless networks.',
            'topics' => [
                'Network topologies and connection types',
                'IP addressing, routers, and switches',
                'Basic network security and safe sharing',
            ],
            'learning_outcomes' => [
                'Describe common network architectures.',
                'Identify key networking hardware and protocols.',
                'Explain basic network security measures.',
            ],
        ],
        3 => [
            'title' => 'Programming Fundamentals',
            'overview' => 'Practice core programming concepts such as logic, variables, and syntax to build basic applications.',
            'goal' => 'Build confidence solving problems with programming logic and flow control.',
            'topics' => [
                'Variables, data types, and expressions',
                'Conditional statements and loops',
                'Debugging and testing basic code',
            ],
            'learning_outcomes' => [
                'Understand variables, loops, and conditionals.',
                'Write simple code to solve common problems.',
                'Trace program flow and identify errors.',
            ],
        ],
        4 => [
            'title' => 'Database Concepts',
            'overview' => 'Gain an introduction to databases, SQL queries, and how data is structured for applications.',
            'goal' => 'Learn how databases organize data and support applications through structured records.',
            'topics' => [
                'Database table design and data types',
                'Basic SELECT queries and filtering data',
                'How applications store and retrieve information',
            ],
            'learning_outcomes' => [
                'Understand tables, records, and fields.',
                'Learn the basics of SQL SELECT queries.',
                'Recognise common database terminology.',
            ],
        ],
        5 => [
            'title' => 'Web Development',
            'overview' => 'Study the essentials of web pages, including HTML structure, CSS styling, and how the web works.',
            'goal' => 'Learn how to create basic web pages using HTML, CSS, and modern design principles.',
            'topics' => [
                'HTML page structure and semantic tags',
                'CSS styling and responsive layouts',
                'Introduction to web page workflows',
            ],
            'learning_outcomes' => [
                'Understand HTML and CSS basics.',
                'Learn how web pages are structured and styled.',
                'Recognise the web development workflow.',
            ],
        ],
        6 => [
            'title' => 'Cybersecurity Essentials',
            'overview' => 'Learn how to protect yourself and your data using strong passwords, secure practices, and awareness of common cyber threats.',
            'goal' => 'Build habits that reduce risk and improve security when using computers and the internet.',
            'topics' => [
                'Common cyber threats, such as phishing and malware',
                'Best practices for strong passwords and account protection',
                'Safe browsing and data privacy awareness',
            ],
            'learning_outcomes' => [
                'Identify common cyber threats and attacks.',
                'Use strong passwords and safe online habits.',
                'Understand the importance of data privacy and protection.',
            ],
        ],
    ];
}

function get_quiz_questions(): array
{
    return [
        1 => [
            [
                'question' => 'Which component is considered software?',
                'options' => ['Operating system', 'Keyboard', 'Hard drive'],
            ],
            [
                'question' => 'Which of the following is not a hardware device?',
                'options' => ['Monitor', 'Application', 'Mouse'],
            ],
            [
                'question' => 'What is the best practice for saving files?',
                'options' => ['Save in random folders', 'Use descriptive names and organised folders', 'Never save'],
            ],
        ],
        2 => [
            [
                'question' => 'What does IP stand for?',
                'options' => ['Internet Protocol', 'Internal Process', 'Interface Port'],
            ],
            [
                'question' => 'Which device forwards data between networks?',
                'options' => ['Switch', 'Router', 'Monitor'],
            ],
            [
                'question' => 'What should you use to protect your home network?',
                'options' => ['Public Wi-Fi', 'Strong password and encryption', 'Leave all settings open'],
            ],
        ],
        3 => [
            [
                'question' => 'What symbol is commonly used for assignment in many languages?',
                'options' => ['==', '=', '!='],
            ],
            [
                'question' => 'Which statement repeats a block of code while a condition is true?',
                'options' => ['if', 'for', 'return'],
            ],
            [
                'question' => 'Which concept helps your program decide between different options?',
                'options' => ['Loop', 'Condition', 'Variable'],
            ],
        ],
        4 => [
            [
                'question' => 'What is a row in a database table?',
                'options' => ['A single record', 'The database engine', 'A query'],
            ],
            [
                'question' => 'Which SQL keyword retrieves data from a table?',
                'options' => ['DELETE', 'SELECT', 'INSERT'],
            ],
            [
                'question' => 'What identifies a unique record in a table?',
                'options' => ['Primary key', 'Password', 'Folder name'],
            ],
        ],
        5 => [
            [
                'question' => 'What does HTML stand for?',
                'options' => ['Hypertext Markup Language', 'Hot Text Management Language', 'Hyperlink Model Language'],
            ],
            [
                'question' => 'Which element adds a paragraph in HTML?',
                'options' => ['<p>', '<div>', '<img>'],
            ],
            [
                'question' => 'What CSS property changes text color?',
                'options' => ['font-size', 'color', 'background'],
            ],
        ],
        6 => [
            [
                'question' => 'Which password is strongest?',
                'options' => ['password123', 'P@55w0rd!', '123456'],
            ],
            [
                'question' => 'What is phishing?',
                'options' => ['A harmless email newsletter', 'A deceptive message trying to steal information', 'A software update'],
            ],
            [
                'question' => 'What should you do before clicking a link in an unknown email?',
                'options' => ['Click immediately', 'Verify the sender and check the URL', 'Forward it to friends'],
            ],
        ],
    ];
}

function render_module_navigation(int $moduleId, string $context = 'module'): void
{
    $modules = get_module_metadata();
    echo '<nav class="module-tabs">';
    echo '<span>Modules:</span>';
    foreach ($modules as $id => $module) {
        $active = $id === $moduleId ? ' active' : '';
        $target = $context === 'quiz' ? site_url("modules/module{$id}/quiz.php") : site_url("modules/module{$id}/index.php");
        echo '<a href="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '" class="module-tab' . $active . '">Module ' . $id . '</a>';
    }
    echo '<a href="' . htmlspecialchars(site_url('index.php'), ENT_QUOTES, 'UTF-8') . '" class="btn btn-soft module-nav-return">Course path</a>';
    echo '</nav>';
}

function render_module_page(int $moduleId): void
{
    $modules = get_module_metadata();
    if (!isset($modules[$moduleId])) {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Module not found</title></head><body><h1>Module not found</h1><p>The requested module does not exist.</p></body></html>';
        return;
    }

    $module = $modules[$moduleId];
    $quizLink = 'quiz.php';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Module <?php echo $moduleId; ?> - <?php echo htmlspecialchars($module['title']); ?></title>
      <?php render_stylesheet(); ?>
      <?php render_script('js/module.js'); ?>
    </head>
    <body>
      <?php render_navigation(); ?>
      <?php render_module_navigation($moduleId, 'module'); ?>
      <div class="page-container module-page">
        <h1>Module <?php echo $moduleId; ?> – <?php echo htmlspecialchars($module['title']); ?></h1>
        <div class="module-summary">
          <p><?php echo htmlspecialchars($module['overview']); ?></p>
          <p><strong>Module goal:</strong> <?php echo htmlspecialchars($module['goal']); ?></p>
        </div>
        <div class="module-card">
          <h2>What you will cover</h2>
          <ul>
            <?php foreach ($module['topics'] as $topic): ?>
              <li><?php echo htmlspecialchars($topic); ?></li>
            <?php endforeach; ?>
          </ul>
          <h3>Learning outcomes</h3>
          <ul>
            <?php foreach ($module['learning_outcomes'] as $goal): ?>
              <li><?php echo htmlspecialchars($goal); ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="module-actions">
            <a href="<?php echo htmlspecialchars(site_url("modules/module{$moduleId}/quiz.php"), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Take Module <?php echo $moduleId; ?> Exit Quiz</a>
            <a href="<?php echo htmlspecialchars(site_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Back to course path</a>
          </div>
        </div>
      </div>
    </body>
    </html>
    <?php
}

function render_module_quiz(int $moduleId): void
{
    $modules = get_module_metadata();
    $questions = get_quiz_questions();
    if (!isset($modules[$moduleId]) || !isset($questions[$moduleId])) {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Quiz not found</title></head><body><h1>Quiz not found</h1><p>The requested quiz does not exist.</p></body></html>';
        return;
    }

    $module = $modules[$moduleId];
    $moduleQuestions = $questions[$moduleId];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Module <?php echo $moduleId; ?> Exit Quiz</title>
      <?php render_stylesheet(); ?>
      <?php render_script('js/module.js'); ?>
    </head>
    <body>
      <?php render_navigation(); ?>
      <?php render_module_navigation($moduleId, 'quiz'); ?>
      <div class="page-container quiz-page">
        <h1>Module <?php echo $moduleId; ?> Exit Quiz</h1>
        <div class="exit-quiz-card">
          <p>Complete this quiz to check your understanding of <?php echo htmlspecialchars($module['title']); ?>.</p>
          <ol class="question-list">
            <?php foreach ($moduleQuestions as $index => $item): ?>
              <li class="question-item">
                <p><?php echo ($index + 1) . '. ' . htmlspecialchars($item['question']); ?></p>
                <div class="question-options">
                  <?php foreach ($item['options'] as $option): ?>
                    <label><input type="radio" name="q<?php echo $index + 1; ?>"> <?php echo htmlspecialchars($option); ?></label>
                  <?php endforeach; ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ol>
          <div class="module-actions">
            <button class="btn btn-primary">Submit answers</button>
            <a href="<?php echo htmlspecialchars(site_url("modules/module{$moduleId}/index.php"), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Back to module</a>
          </div>
        </div>
      </div>
    </body>
    </html>
    <?php
}
