<?php /** @var string $content */ ?>
<?php
  // Active route helper
  $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

  // Define nav items once
  $navItems = [
      [ 'href' => '/admin/dashboard', 'icon' => 'bi-grid',     'label' => 'Dashboard', 'active' => $current === '/admin/dashboard' ],
      [ 'href' => '/admin/contacts',  'icon' => 'bi-envelope', 'label' => 'Contacts',   'active' => $current === '/admin/contacts' ],
  ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .admin-shell { min-height: 100vh; }
    .sidebar {
      width: 260px; flex: 0 0 260px;
      background: #0f172a; /* slate-900 */
    }
    .sidebar a { color: rgba(255,255,255,0.85); text-decoration: none; }
    .sidebar a.active, .sidebar a:hover { color: #fff; }
    .brand-gradient { background: linear-gradient(90deg,#4f46e5,#7c3aed); }

    /* On large screens keep sidebar visible; on smaller, hide it (use offcanvas) */
    @media (max-width: 991.98px) {
      .sidebar-desktop { display: none !important; }
    }
    @media (min-width: 992px) {
      .drawer-toggle { display: none !important; }
    }
  </style>
</head>
<body class="bg-light">
  <div class="admin-shell d-flex">

    <!-- Sidebar (desktop) -->
    <aside class="sidebar sidebar-desktop d-flex flex-column">
      <div class="brand-gradient text-white px-3 py-3 d-flex align-items-center gap-2">
        <i class="bi bi-speedometer2"></i>
        <span class="fw-semibold">Portfolio Admin</span>
      </div>
      <nav class="p-3 d-flex flex-column gap-1" aria-label="Primary">
        <?php foreach ($navItems as $item): ?>
          <a href="<?= htmlspecialchars($item['href']) ?>"
             class="px-3 py-2 rounded d-flex align-items-center gap-2 <?= $item['active'] ? 'active bg-primary' : 'text-white-50' ?>">
            <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
            <span><?= htmlspecialchars($item['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="mt-auto p-3">
        <a class="text-white-50 small d-inline-flex align-items-center gap-2" href="/admin/logout">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </aside>

    <!-- Main -->
    <main class="flex-fill d-flex flex-column">

      <!-- Top bar -->
      <header class="bg-white border-bottom">
        <div class="container-fluid py-3 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <!-- Drawer toggle (mobile) -->
            <button class="btn btn-outline-secondary drawer-toggle" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#adminDrawer" aria-controls="adminDrawer"
                    aria-label="Open navigation">
              <i class="bi bi-list"></i>
            </button>
            <h1 class="h5 mb-0"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
          </div>
          <div class="text-muted small">Admin</div>
        </div>
      </header>

      <!-- Content -->
      <section class="container-fluid py-4 flex-fill">
        <?= $content ?? '' ?>
      </section>
    </main>
  </div>

  <!-- Offcanvas Drawer (mobile navigation) -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="adminDrawer" aria-labelledby="adminDrawerLabel">
    <div class="offcanvas-header brand-gradient text-white">
      <h5 class="offcanvas-title d-flex align-items-center gap-2" id="adminDrawerLabel">
        <i class="bi bi-speedometer2"></i> Portfolio Admin
      </h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column" style="background:#0f172a;">
      <nav class="p-3 d-flex flex-column gap-1" aria-label="Primary mobile">
        <?php foreach ($navItems as $item): ?>
          <a href="<?= htmlspecialchars($item['href']) ?>"
             class="px-3 py-2 rounded d-flex align-items-center gap-2 <?= $item['active'] ? 'active bg-primary' : 'text-white-50' ?>"
             data-bs-dismiss="offcanvas" aria-label="Navigate">
            <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
            <span><?= htmlspecialchars($item['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="mt-auto p-3">
        <a class="text-white-50 small d-inline-flex align-items-center gap-2" href="/admin/logout" data-bs-dismiss="offcanvas">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS + Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</body>
</html>
