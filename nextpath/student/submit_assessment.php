<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['assessment_question_ids'])) {
    redirect('assessment.php');
}

$questionIds = $_SESSION['assessment_question_ids'];
$userId = current_user_id();

$in = implode(',', array_fill(0, count($questionIds), '?'));
$stmt = $pdo->prepare("SELECT id, category_id, correct_answer FROM questions WHERE id IN ($in)");
$stmt->execute($questionIds);
$questions = $stmt->fetchAll();
$questionsById = [];
foreach ($questions as $q) {
    $questionsById[$q['id']] = $q;
}

$totalQuestions = count($questionIds);
$correctCount = 0;
$answers = [];

foreach ($questionIds as $qid) {
    $selected = $_POST["q_{$qid}"] ?? null;
    if (!in_array($selected, ['A', 'B', 'C', 'D'], true) || !isset($questionsById[$qid])) {
        set_flash('error', 'Please answer every question before submitting.');
        redirect('assessment.php');
    }
    $isCorrect = ($selected === $questionsById[$qid]['correct_answer']) ? 1 : 0;
    $correctCount += $isCorrect;
    $answers[] = [
        'question_id' => $qid,
        'selected'    => $selected,
        'is_correct'  => $isCorrect,
        'score'       => $isCorrect ? 1.0 : 0.0,
    ];
}

$overallScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO assessments (user_id, total_questions, correct_answers, overall_score)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$userId, $totalQuestions, $correctCount, $overallScore]);
    $assessmentId = $pdo->lastInsertId();

    $stmt = $pdo->prepare('
        INSERT INTO assessment_answers (assessment_id, question_id, selected_answer, is_correct, score)
        VALUES (?, ?, ?, ?, ?)
    ');
    foreach ($answers as $a) {
        $stmt->execute([$assessmentId, $a['question_id'], $a['selected'], $a['is_correct'], $a['score']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('[NEXTPath] Assessment submit failed: ' . $e->getMessage());
    set_flash('error', 'Something went wrong. Please try again.');
    redirect('assessment.php');
}

unset($_SESSION['assessment_question_ids']);
redirect("result.php?id={$assessmentId}");
