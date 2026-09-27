<?php
/**
 * NEXTPath - Admin authentication guard
 * Include at the top of every admin/*.php page.
 * Students must never be able to reach /admin/*.
 */

require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    redirect(base_url('login.php'));
}

if (current_user_role() !== 'admin') {
    redirect(base_url('student/dashboard.php'));
}
