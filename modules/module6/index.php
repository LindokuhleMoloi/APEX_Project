<?php
require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../includes/module_template.php';
app_start();
require_user();
render_module_page(6);
