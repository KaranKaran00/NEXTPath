<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();

$filterCategory = (int)($_GET['category_id'] ?? 0);

if ($filterCategory > 0) {
    $stmt = $pdo->prepare('
        SELECT q.id, q.question_text, q.correct_answer, q.created_at, c.name AS category_name
        FROM questions q JOIN categories c ON c.id = q.category_id
        WHERE q.category_id = ? ORDER BY q.created_at DESC
    ');
    $stmt->execute([$filterCategory]);
} else {
    $stmt = $pdo->query('
        SELECT q.id, q.question_text, q.correct_answer, q.created_at, c.name AS category_name
        FROM questions q JOIN categories c ON c.id = q.category_id
        ORDER BY q.created_at DESC
    ');
}
$questions = $stmt->fetchAll();

$page_title = 'Questions — NEXTPath Admin';
$active = 'questions';
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
      <h1>Questions</h1>
      <p class="dashboard-sub"><?= count($questions) ?> questions.</p>
    </div>
    <a href="add-question.php" class="btn btn-primary">Add Question</a>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="card">
    <form method="GET" action="questions.php" class="filter-bar">
      <label for="category_id">All Categories</label>
      <select id="category_id" name="category_id" onchange="this.form.submit()">
        <option value="0">All Categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $filterCategory === (int)$c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr><th>Question</th><th>Category</th><th>Correct</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($questions as $q): ?>
            <tr>
              <td><?= clean($q['question_text']) ?></td>
              <td><span class="badge"><?= clean($q['category_name']) ?></span></td>
              <td><?= clean($q['correct_answer']) ?></td>
              <td><?= date('d M Y', strtotime($q['created_at'])) ?></td>
              <td class="actions-cell">
                <a href="edit-question.php?id=<?= $q['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="POST" action="delete-question.php" class="confirm-form" data-confirm="Delete this question?">
                  <input type="hidden" name="id" value="<?= $q['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($questions)): ?>
            <tr><td colspan="5">No questions found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
