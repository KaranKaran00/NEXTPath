<?php
/**
 * NEXTPath - Database Connection
 * Uses PDO with prepared statements throughout the application.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'nextpath';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('[NEXTPath DB Error] ' . $e->getMessage());
    http_response_code(500);
    die('Something went wrong. Please try again later.');
}
