<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    set_flash('error', 'Course not found.');
    redirect('courses.php');
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
$errors = [];
$old = $course;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'course_name', 'description'] as $key) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    if (empty($old['category_id'])) $errors['category_id'] = 'Please select a category.';
    if (empty($old['course_name'])) $errors['course_name'] = 'Please enter a course name.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE courses SET category_id = ?, course_name = ?, description = ? WHERE id = ?');
        $stmt->execute([(int)$old['category_id'], $old['course_name'], $old['description'], $id]);
        set_flash('success', 'Course updated successfully.');
        redirect('courses.php');
    }
}

$page_title = 'Edit Course — NEXTPath Admin';
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
  <header class="dashboard-header"><div><h1>Edit Course</h1></div></header>
  <section class="card">
    <form method="POST" action="edit-course.php?id=<?= $id ?>" novalidate>
      <input type="hidden" name="id" value="<?= $id ?>">
      <div class="form-group">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (string)$old['category_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="field-error"><?= clean($errors['category_id'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="course_name">Course Name</label>
        <input type="text" id="course_name" name="course_name" value="<?= clean($old['course_name']) ?>" required>
        <span class="field-error"><?= clean($errors['course_name'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="2"><?= clean($old['description']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Update Course</button>
      <a href="courses.php" class="btn btn-outline">Cancel</a>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
