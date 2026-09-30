<?php
/**
 * NEXTPath - Shared helper functions
 * Sanitisation, redirects, flash messages, scoring and
 * career recommendation logic all live here so pages stay thin.
 */

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function clean($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: {$path}");
    exit;
}

function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_role() {
    return $_SESSION['role'] ?? null;
}

if (!defined('APP_BASE_PATH')) {
    $appRoot = str_replace('\\', '/', dirname(__DIR__));
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') : '';
    $relative = ($docRoot !== '' && strpos($appRoot, $docRoot) === 0)
        ? substr($appRoot, strlen($docRoot))
        : '';
    define('APP_BASE_PATH', $relative === '' ? '/' : rtrim($relative, '/') . '/');
}

function base_url($path = '') {
    return APP_BASE_PATH . ltrim($path, '/');
}

function hash_password($plain) {
    return password_hash($plain, PASSWORD_DEFAULT);
}

function verify_password($plain, $hash) {
    return password_verify($plain, $hash);
}

function get_setting($pdo, $key, $default = null) {
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function get_assessment_questions($pdo, $limit) {
    $stmt = $pdo->query('
        SELECT q.*, c.name AS category_name
        FROM questions q
        JOIN categories c ON c.id = q.category_id
        ORDER BY RAND()
    ');
    $all = $stmt->fetchAll();

    $byCategory = [];
    foreach ($all as $q) {
        $byCategory[$q['category_id']][] = $q;
    }
    foreach ($byCategory as &$bucket) {
        shuffle($bucket);
    }
    unset($bucket);

    $categoryIds = array_keys($byCategory);
    shuffle($categoryIds);

    $mixed = [];
    $exhausted = false;
    while (!$exhausted && count($mixed) < $limit) {
        $exhausted = true;
        foreach ($categoryIds as $cid) {
            if (!empty($byCategory[$cid])) {
                $mixed[] = array_shift($byCategory[$cid]);
                $exhausted = false;
                if (count($mixed) >= $limit) {
                    break;
                }
            }
        }
    }

    return array_slice($mixed, 0, $limit);
}

function get_category_scores($pdo, $assessment_id) {
    $stmt = $pdo->prepare('
        SELECT c.id AS category_id, c.name,
                SUM(aa.is_correct) AS correct,
                COUNT(*) AS total
        FROM assessment_answers aa
        JOIN questions q ON q.id = aa.question_id
        JOIN categories c ON c.id = q.category_id
        WHERE aa.assessment_id = ?
        GROUP BY c.id, c.name
    ');
    $stmt->execute([$assessment_id]);
    $rows = $stmt->fetchAll();

    $result = [];
    foreach ($rows as $row) {
        $score = $row['total'] > 0 ? round(($row['correct'] / $row['total']) * 100, 1) : 0;
        $result[$row['category_id']] = [
            'name'    => $row['name'],
            'correct' => (int)$row['correct'],
            'total'   => (int)$row['total'],
            'score'   => $score,
        ];
    }
    uasort($result, fn($a, $b) => $b['score'] <=> $a['score']);
    return $result;
}

function get_skill_profile($pdo, $category_scores) {
    if (empty($category_scores)) {
        return [];
    }
    $catIds = array_keys($category_scores);
    $in = implode(',', array_fill(0, count($catIds), '?'));
    $stmt = $pdo->prepare("SELECT category_id, skill_name, weight FROM category_skills WHERE category_id IN ($in)");
    $stmt->execute($catIds);
    $mappings = $stmt->fetchAll();

    $skillTotals = [];
    $skillWeights = [];
    foreach ($mappings as $m) {
        $cid = $m['category_id'];
        if (!isset($category_scores[$cid])) {
            continue;
        }
        $skill = $m['skill_name'];
        $weight = (float)$m['weight'];
        $skillTotals[$skill] = ($skillTotals[$skill] ?? 0) + ($category_scores[$cid]['score'] * $weight);
        $skillWeights[$skill] = ($skillWeights[$skill] ?? 0) + $weight;
    }

    $profile = [];
    foreach ($skillTotals as $skill => $total) {
        $profile[$skill] = round($total / $skillWeights[$skill], 1);
    }
    arsort($profile);
    return $profile;
}

function skill_level_label($score) {
    if ($score >= 85) return 'Excellent';
    if ($score >= 70) return 'Strong';
    if ($score >= 55) return 'Good';
    return 'Developing';
}

function get_career_recommendations($pdo, $category_scores, $limit = 6) {
    if (empty($category_scores)) {
        return [];
    }
    $catIds = array_keys($category_scores);
    $in = implode(',', array_fill(0, count($catIds), '?'));
    $stmt = $pdo->prepare("
        SELECT ca.id, ca.career_name, ca.description, ca.relevance, ca.category_id, c.name AS category_name
        FROM careers ca
        JOIN categories c ON c.id = ca.category_id
        WHERE ca.category_id IN ($in)
    ");
    $stmt->execute($catIds);
    $careers = $stmt->fetchAll();

    $recommendations = [];
    foreach ($careers as $career) {
        $categoryScore = $category_scores[$career['category_id']]['score'];
        $match = round($categoryScore * (float)$career['relevance'], 1);
        $match = min($match, 100);
        $recommendations[] = [
            'career_name'   => $career['career_name'],
            'description'   => $career['description'],
            'category_id'   => $career['category_id'],
            'category_name' => $career['category_name'],
            'match'         => $match,
        ];
    }

    usort($recommendations, fn($a, $b) => $b['match'] <=> $a['match']);
    return array_slice($recommendations, 0, $limit);
}

function get_courses_for_categories($pdo, $categoryIds) {
    if (empty($categoryIds)) {
        return [];
    }
    try {
        $in = implode(',', array_fill(0, count($categoryIds), '?'));
        $stmt = $pdo->prepare("SELECT category_id, course_name, description FROM courses WHERE category_id IN ($in) ORDER BY course_name ASC");
        $stmt->execute($categoryIds);
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('[NEXTPath] courses lookup failed: ' . $e->getMessage());
        return [];
    }

    $byCategory = [];
    foreach ($rows as $row) {
        $byCategory[$row['category_id']][] = $row;
    }
    return $byCategory;
}

function validate_registration($data) {
    $errors = [];

    if (empty($data['full_name']) || strlen(trim($data['full_name'])) < 2) {
        $errors['full_name'] = 'Please enter your full name.';
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (empty($data['mobile']) || !preg_match('/^[0-9]{10}$/', $data['mobile'])) {
        $errors['mobile'] = 'Please enter a valid 10-digit mobile number.';
    }
    if (empty($data['password']) || strlen($data['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    if (empty($data['gender']) || !in_array($data['gender'], ['Male', 'Female', 'Other'], true)) {
        $errors['gender'] = 'Please select a gender.';
    }
    if (empty($data['education_level'])) {
        $errors['education_level'] = 'Please select an education level.';
    }
    if (empty($data['college_name'])) {
        $errors['college_name'] = 'Please enter your school or college name.';
    }

    return $errors;
}
