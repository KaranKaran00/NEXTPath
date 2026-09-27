<?php
/**
 * Student dashboard sidebar.
 * Expects $active to be set (dashboard|assessment|career|recommendations|explorer|history|profile).
 */
$active = $active ?? '';
$nav = [
    'dashboard'       => ['label' => 'Dashboard',        'href' => 'dashboard.php',
        'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>'],
    'assessment'      => ['label' => 'Assessment',       'href' => 'assessment.php',
        'icon' => '<path d="M9 11l2.5 2.5L16 8"/><rect x="3" y="3" width="18" height="18" rx="2.5"/>'],
    'career'          => ['label' => 'Career Profile',   'href' => 'career-dashboard.php',
        'icon' => '<path d="M4 19V5a1 1 0 0 1 1-1h6l2 2h6a1 1 0 0 1 1 1v4"/><path d="M4 19h16"/><path d="M14 13v8M18 16v5M10 15v6"/>'],
    'recommendations' => ['label' => 'Recommendations',  'href' => 'career-dashboard.php#recommendations',
        'icon' => '<path d="M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z"/>'],
    'explorer'        => ['label' => 'Career Explorer',  'href' => 'explorer.php',
        'icon' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>'],
    'history'         => ['label' => 'History',          'href' => 'history.php',
        'icon' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 8v4l3 2"/>'],
    'profile'         => ['label' => 'My Profile',       'href' => 'profile.php',
        'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>'],
];
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo"><img src="<?= base_url('assets/img/nextpath-logo.png') ?>" alt="NEXTPath"></div>
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
