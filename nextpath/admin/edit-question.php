<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM questions WHERE id = ?');
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    set_flash('error', 'Question not found.');
    redirect('questions.php');
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
$errors = [];
$old = $question;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['category_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'] as $key) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    if (empty($old['category_id'])) $errors['category_id'] = 'Please select a category.';
    if (empty($old['question_text'])) $errors['question_text'] = 'Please enter the question.';
    foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $opt) {
        if (empty($old[$opt])) $errors[$opt] = 'Required.';
    }
    if (!in_array($old['correct_answer'], ['A', 'B', 'C', 'D'], true)) {
        $errors['correct_answer'] = 'Please select the correct answer.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            UPDATE questions SET category_id = ?, question_text = ?, option_a = ?, option_b = ?,
            option_c = ?, option_d = ?, correct_answer = ? WHERE id = ?
        ');
        $stmt->execute([
            (int)$old['category_id'], $old['question_text'],
            $old['option_a'], $old['option_b'], $old['option_c'], $old['option_d'],
            $old['correct_answer'], $id,
        ]);
        set_flash('success', 'Question updated successfully.');
        redirect('questions.php');
    }
}

$page_title = 'Edit Question — NEXTPath Admin';
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
    <div><h1>Edit Question</h1></div>
  </header>

  <section class="card">
    <form method="POST" action="edit-question.php?id=<?= $id ?>" novalidate>
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
        <label for="question_text">Question</label>
        <textarea id="question_text" name="question_text" rows="2" required><?= clean($old['question_text']) ?></textarea>
        <span class="field-error"><?= clean($errors['question_text'] ?? '') ?></span>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label for="option_a">Option A</label>
          <input type="text" id="option_a" name="option_a" value="<?= clean($old['option_a']) ?>" required>
          <span class="field-error"><?= clean($errors['option_a'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_b">Option B</label>
          <input type="text" id="option_b" name="option_b" value="<?= clean($old['option_b']) ?>" required>
          <span class="field-error"><?= clean($errors['option_b'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_c">Option C</label>
          <input type="text" id="option_c" name="option_c" value="<?= clean($old['option_c']) ?>" required>
          <span class="field-error"><?= clean($errors['option_c'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_d">Option D</label>
          <input type="text" id="option_d" name="option_d" value="<?= clean($old['option_d']) ?>" required>
          <span class="field-error"><?= clean($errors['option_d'] ?? '') ?></span>
        </div>
      </div>
      <div class="form-group">
        <label for="correct_answer">Correct Answer</label>
        <select id="correct_answer" name="correct_answer" required>
          <?php foreach (['A', 'B', 'C', 'D'] as $letter): ?>
            <option value="<?= $letter ?>" <?= $old['correct_answer'] === $letter ? 'selected' : '' ?>>Option <?= $letter ?></option>
          <?php endforeach; ?>
        </select>
        <span class="field-error"><?= clean($errors['correct_answer'] ?? '') ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Update Question</button>
      <a href="questions.php" class="btn btn-outline">Cancel</a>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
