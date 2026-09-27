<?php
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM careers WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Career deleted.');
}

redirect('careers.php');
