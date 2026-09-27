<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('profile.php');
}

$userId = current_user_id();
$fullName = trim($_POST['full_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$educationLevel = trim($_POST['education_level'] ?? '');
$collegeName = trim($_POST['college_name'] ?? '');
$interests = trim($_POST['interests'] ?? '');
$skills = trim($_POST['skills'] ?? '');
$hobbies = trim($_POST['hobbies'] ?? '');

if (empty($fullName) || empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)
    || !in_array($gender, ['Male', 'Female', 'Other'], true) || empty($educationLevel) || empty($collegeName)) {
    set_flash('error', 'Please check your details and try again.');
    redirect('profile.php');
}

$stmt = $pdo->prepare('
    UPDATE users SET full_name = ?, mobile = ?, gender = ?, education_level = ?,
    college_name = ?, interests = ?, skills = ?, hobbies = ? WHERE id = ?
');
$stmt->execute([$fullName, $mobile, $gender, $educationLevel, $collegeName, $interests, $skills, $hobbies, $userId]);

$_SESSION['user_name'] = $fullName;
set_flash('success', 'Your profile has been updated.');
redirect('profile.php');
