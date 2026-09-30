<?php
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    redirect(base_url('login.php'));
}

if (current_user_role() !== 'admin') {
    redirect(base_url('student/dashboard.php'));
}
