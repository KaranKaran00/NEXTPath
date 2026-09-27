<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$careers = $pdo->query('
    SELECT ca.id, ca.career_name, ca.description, ca.relevance, c.name AS category_name
    FROM careers ca JOIN categories c ON c.id = ca.category_id
    ORDER BY c.name ASC, ca.career_name ASC
')->fetchAll();

$page_title = 'Careers — NEXTPath Admin';
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
  <header class="dashboard-header">
    <div>
      <h1>Careers</h1>
      <p class="dashboard-sub"><?= count($careers) ?> mapped careers.</p>
    </div>
    <a href="add-career.php" class="btn btn-primary">Add Career</a>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="card">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Career</th><th>Category</th><th>Relevance</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($careers as $c): ?>
            <tr>
              <td><?= clean($c['career_name']) ?></td>
              <td><span class="badge"><?= clean($c['category_name']) ?></span></td>
              <td><?= clean($c['relevance']) ?></td>
              <td class="actions-cell">
                <a href="edit-career.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="delete-career.php" class="confirm-form" data-confirm="Delete this career?">
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
