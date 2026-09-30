<?php
$active = $active ?? '';
$nav = [
    'dashboard'    => ['label' => 'Dashboard',   'href' => 'dashboard.php',
        'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>'],
    'students'     => ['label' => 'Students',    'href' => 'students.php',
        'icon' => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.5 2.9-6 6.5-6s6.5 2.5 6.5 6"/><circle cx="17.5" cy="8.5" r="2.5"/><path d="M15.5 14.3c2.7.4 4.5 2.4 4.5 5.2"/>'],
    'questions'    => ['label' => 'Questions',   'href' => 'questions.php',
        'icon' => '<circle cx="12" cy="12" r="9"/><path d="M9.2 9.3a2.8 2.8 0 1 1 3.9 2.6c-.9.5-1.1 1-1.1 1.9"/><path d="M12 17h.01"/>'],
    'categories'   => ['label' => 'Categories',  'href' => 'categories.php',
        'icon' => '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/>'],
    'careers'      => ['label' => 'Careers',     'href' => 'careers.php',
        'icon' => '<path d="M4 19V5a1 1 0 0 1 1-1h6l2 2h6a1 1 0 0 1 1 1v4"/><path d="M4 19h16"/><path d="M14 13v8M18 16v5M10 15v6"/>'],
    'courses'      => ['label' => 'Courses',     'href' => 'courses.php',
        'icon' => '<path d="M4 6.5C4 5.1 5.1 4 6.5 4H20v14H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 6.5v14A2.5 2.5 0 0 0 6.5 23H20"/>'],
    'assessments'  => ['label' => 'Assessments', 'href' => 'assessments.php',
        'icon' => '<path d="M9 11l2.5 2.5L16 8"/><rect x="3" y="3" width="18" height="18" rx="2.5"/>'],
    'statistics'   => ['label' => 'Statistics',  'href' => 'statistics.php',
        'icon' => '<path d="M4 20V10M11 20V4M18 20v-7"/>'],
];
?>
<aside class="sidebar sidebar-admin" id="sidebar">
  <div class="sidebar-logo"><img src="<?= base_url('assets/img/nextpath-logo.png') ?>" alt="NEXTPath"><span class="admin-tag">Admin</span></div>
  <nav class="sidebar-nav">
    <?php foreach ($nav as $key => $item): ?>
      <a href="<?= clean($item['href']) ?>" class="<?= $active === $key ? 'active' : '' ?>">
        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $item['icon'] ?></svg>
        <span><?= clean($item['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
  <a href="<?= base_url('logout.php') ?>" class="sidebar-logout">
    <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
    <span>Logout</span>
  </a>
</aside>
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">&#9776;</button>
