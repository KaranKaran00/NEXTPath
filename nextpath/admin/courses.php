<?php
require_once __DIR__ . '/../includes/admin_auth.php';

try {
    $courses = $pdo->query('
        SELECT co.id, co.course_name, co.description, c.name AS category_name
        FROM courses co JOIN categories c ON c.id = co.category_id
        ORDER BY c.name ASC, co.course_name ASC
    ')->fetchAll();
    $coursesTableMissing = false;
} catch (PDOException $e) {
    error_log('[NEXTPath] courses lookup failed: ' . $e->getMessage());
    $courses = [];
    $coursesTableMissing = true;
}

$page_title = 'Courses — NEXTPath Admin';
$active = 'courses';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/admin-sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>Courses</h1>
      <p class="dashboard-sub"><?= count($courses) ?> suggested courses, used for College / Course Recommendations.</p>
    </div>
    <a href="add-course.php" class="btn btn-primary">Add Course</a>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($coursesTableMissing): ?>
    <div class="flash flash-error">
      The <code>courses</code> table hasn't been created yet. Import
      <code>database/migration_v2_courses.sql</code> in phpMyAdmin to enable
      College / Course Recommendations.
    </div>
  <?php endif; ?>

  <section class="card">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Course</th><th>Category</th><th>Description</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($courses as $c): ?>
            <tr>
              <td><?= clean($c['course_name']) ?></td>
              <td><span class="badge"><?= clean($c['category_name']) ?></span></td>
              <td><?= clean($c['description']) ?></td>
              <td class="actions-cell">
                <a href="edit-course.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="delete-course.php" class="confirm-form" data-confirm="Delete this course?">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($courses)): ?>
            <tr><td colspan="4">No courses added yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
