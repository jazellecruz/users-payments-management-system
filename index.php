<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/db/db_conn.php';
require_once __DIR__ . '/utils/utils.php';

echo "<h2>TRY 👋</h2>";
echo "<p>Connected to DB: " . ($_ENV['DB_NAME'] ?? 'Not set') . "</p>";
