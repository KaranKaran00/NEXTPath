<?php
/**
 * Public site header (landing, login, register, static pages).
 * Expects optional $page_title to be set before include.
 */
if (!isset($page_title)) {
    $page_title = 'NEXTPath — Discover Your Skills. Find Your Direction.';
}
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
</head>
<body>
<header class="site-nav">
  <div class="nav-inner">
    <a href="<?= base_url('index.php') ?>" class="nav-logo" aria-label="NEXTPath"><img src="<?= base_url('assets/img/nextpath-logo.png') ?>" alt="NEXTPath"></a>
    <nav class="nav-links" id="navLinks">
      <a href="<?= base_url('index.php') ?>#home" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="<?= base_url('index.php') ?>#how-it-works">How It Works</a>
      <a href="<?= base_url('index.php') ?>#career-fields">Career Fields</a>
      <a href="<?= base_url('index.php') ?>#about">About</a>
    </nav>
    <div class="nav-actions">
      <?php if (is_logged_in()): ?>
        <a href="<?= base_url(current_user_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php') ?>" class="btn btn-outline btn-sm">Dashboard</a>
      <?php else: ?>
        <a href="<?= base_url('login.php') ?>" class="btn btn-outline btn-sm">Login</a>
        <a href="<?= base_url('register.php') ?>" class="btn btn-primary btn-sm">Get Started</a>
      <?php endif; ?>
    </div>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
<?php $flash = get_flash(); if ($flash): ?>
  <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
<?php endif; ?>
<main>
