<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$errors = [];
$old = ['name' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim($_POST['name'] ?? '');
    $old['description'] = trim($_POST['description'] ?? '');

    if (empty($old['name'])) $errors['name'] = 'Please enter a category name.';
    if (empty($old['description'])) $errors['description'] = 'Please enter a description.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([$old['name']]);
        if ($stmt->fetch()) {
            $errors['name'] = 'A category with this name already exists.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
        $stmt->execute([$old['name'], $old['description']]);
        set_flash('success', 'Category added successfully.');
        redirect('categories.php');
    }
}

$page_title = 'Add Category — NEXTPath Admin';
$active = 'categories';
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
  <header class="dashboard-header"><div><h1>Add Category</h1></div></header>
  <section class="card">
    <form method="POST" action="add-category.php" novalidate>
      <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" id="name" name="name" value="<?= clean($old['name']) ?>" required>
        <span class="field-error"><?= clean($errors['name'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" required><?= clean($old['description']) ?></textarea>
        <span class="field-error"><?= clean($errors['description'] ?? '') ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Save Category</button>
      <a href="categories.php" class="btn btn-outline">Cancel</a>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
