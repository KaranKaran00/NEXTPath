<?php
/**
 * NEXTPath - Student authentication guard
 * Include at the top of every student/*.php page.
 */

require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    redirect(base_url('login.php'));
}

if (current_user_role() !== 'student') {
    // Admins have their own dashboard; keep the two areas separate.
    redirect(base_url('admin/dashboard.php'));
}
