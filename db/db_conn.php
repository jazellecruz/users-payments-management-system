<?php

require_once __DIR__ . '/../config/config.php';

function getDBConnection($retryCount = 1) {
    $MAX_RETRIES = 5;
    $retryCount = $retryCount;
    $conn = null;

    if (!isset($conn)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

        if ($conn->connect_error) {
            echo "Connection failed: " . $conn->connect_error;
        }
    }

    return $conn;
}

return getDBConnection();

?>