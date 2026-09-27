<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$categories = $pdo->query('
    SELECT c.id, c.name, c.description, c.created_at, COUNT(q.id) AS question_count
    FROM categories c LEFT JOIN questions q ON q.category_id = c.id
    GROUP BY c.id, c.name, c.description, c.created_at
    ORDER BY c.name ASC
')->fetchAll();

$page_title = 'Categories — NEXTPath Admin';
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
  <header class="dashboard-header">
    <div>
      <h1>Categories</h1>
      <p class="dashboard-sub"><?= count($categories) ?> career categories.</p>
    </div>
    <a href="add-category.php" class="btn btn-primary">Add Category</a>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="card">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Category</th><th>Description</th><th>Questions</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td><?= clean($c['name']) ?></td>
              <td><?= clean($c['description']) ?></td>
              <td><?= (int)$c['question_count'] ?></td>
              <td class="actions-cell">
                <a href="edit-category.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="delete-category.php" class="confirm-form" data-confirm="Delete this category and all its questions?">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
