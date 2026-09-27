<?php
require_once __DIR__ . '/includes/functions.php';

$categories = $pdo->query('SELECT id, name, description FROM categories ORDER BY name ASC LIMIT 12')->fetchAll();

$page_title = 'NEXTPath — Discover Your Skills. Find Your Direction.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" id="home">
  <div class="hero-inner">
    <p class="eyebrow">Adaptive Skill Assessment &amp; Career Discovery</p>
    <h1>DISCOVER<br>YOUR NEXT PATH.</h1>
    <p class="hero-sub">Understand your strengths. Explore your potential. Find careers that fit you.</p>
    <div class="hero-actions">
      <a href="<?= base_url(is_logged_in() ? 'student/assessment.php' : 'register.php') ?>" class="btn btn-primary btn-lg">Start Assessment</a>
      <a href="#career-fields" class="btn btn-outline btn-lg">Explore Career Fields</a>
    </div>
  </div>
</section>

<section class="career-strip">
  <div class="career-strip-inner">
    <img src="<?= base_url('assets/img/career-paths.svg') ?>" alt="Illustration of people working in different professions — healthcare, aviation, business, hospitality and more" loading="lazy">
    <p>Every profession starts the same way: knowing where your strengths fit.</p>
  </div>
</section>

<section class="section" id="how-it-works">
  <div class="section-inner">
    <p class="section-label">How It Works</p>
    <h2 class="section-title">A clear path from questions to direction.</h2>
    <div class="steps-grid">
      <div class="step">
        <span class="step-number">01</span>
        <h3>Create Your Profile</h3>
        <p>Tell us about your education, interests, skills and hobbies.</p>
      </div>
      <div class="step">
        <span class="step-number">02</span>
        <h3>Take the Assessment</h3>
        <p>Answer questions drawn from a wide range of career fields.</p>
      </div>
      <div class="step">
        <span class="step-number">03</span>
        <h3>Understand Your Strengths</h3>
        <p>See a category-wise breakdown of your skill profile.</p>
      </div>
      <div class="step">
        <span class="step-number">04</span>
        <h3>Explore Career Paths</h3>
        <p>Get ranked career recommendations matched to your results.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt" id="career-fields">
  <div class="section-inner">
    <p class="section-label">Career Fields</p>
    <h2 class="section-title">Twenty-eight fields. One assessment.</h2>
    <div class="fields-grid">
      <?php foreach ($categories as $cat): ?>
        <div class="field-card">
          <h3><?= clean($cat['name']) ?></h3>
          <p><?= clean($cat['description']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="about">
  <div class="section-inner">
    <p class="section-label">Why NEXTPath</p>
    <h2 class="section-title">Built for real decisions, not guesswork.</h2>
    <div class="why-grid">
      <div class="why-item">
        <h3>Personalised Assessment</h3>
        <p>Questions span multiple career fields so your results reflect your whole profile, not one narrow test.</p>
      </div>
      <div class="why-item">
        <h3>Data-Driven Results</h3>
        <p>Every score is calculated from your actual answers — nothing here is randomly generated.</p>
      </div>
      <div class="why-item">
        <h3>Multiple Career Recommendations</h3>
        <p>Get a ranked list of careers, not a single verdict, so you can compare real options.</p>
      </div>
      <div class="why-item">
        <h3>Student-Friendly Experience</h3>
        <p>A distraction-free interface designed for focus, clarity and quick understanding.</p>
      </div>
    </div>
  </div>
</section>

<section class="cta">
  <div class="cta-inner">
    <h2>Ready to discover your direction?</h2>
    <a href="<?= base_url(is_logged_in() ? 'student/assessment.php' : 'register.php') ?>" class="btn btn-primary btn-lg">Start Your Assessment</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
