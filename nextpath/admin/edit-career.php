<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM careers WHERE id = ?');
$stmt->execute([$id]);
$career = $stmt->fetch();

if (!$career) {
    set_flash('error', 'Career not found.');
    redirect('careers.php');
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
$errors = [];
$old = $career;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'career_name', 'description', 'relevance'] as $key) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    if (empty($old['category_id'])) $errors['category_id'] = 'Please select a category.';
    if (empty($old['career_name'])) $errors['career_name'] = 'Please enter a career name.';
    if (!is_numeric($old['relevance']) || $old['relevance'] < 0 || $old['relevance'] > 1) {
        $errors['relevance'] = 'Relevance must be between 0 and 1.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE careers SET category_id = ?, career_name = ?, description = ?, relevance = ? WHERE id = ?');
        $stmt->execute([(int)$old['category_id'], $old['career_name'], $old['description'], (float)$old['relevance'], $id]);
        set_flash('success', 'Career updated successfully.');
        redirect('careers.php');
    }
}

$page_title = 'Edit Career — NEXTPath Admin';
$active = 'careers';
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
  <header class="dashboard-header"><div><h1>Edit Career</h1></div></header>
  <section class="card">
    <form method="POST" action="edit-career.php?id=<?= $id ?>" novalidate>
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
        <label for="career_name">Career Name</label>
        <input type="text" id="career_name" name="career_name" value="<?= clean($old['career_name']) ?>" required>
        <span class="field-error"><?= clean($errors['career_name'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="2"><?= clean($old['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="relevance">Relevance (0.00 – 1.00)</label>
        <input type="text" id="relevance" name="relevance" value="<?= clean($old['relevance']) ?>" required>
        <span class="field-error"><?= clean($errors['relevance'] ?? '') ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Update Career</button>
      <a href="careers.php" class="btn btn-outline">Cancel</a>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
