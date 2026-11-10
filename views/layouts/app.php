<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isActive = static function (string $path) use ($currentPath): string {
    if ($path === '/') {
        return $currentPath === '/' ? 'active' : '';
    }
    return str_starts_with($currentPath, $path) ? 'active' : '';
};
$flashSuccess = $_SESSION['_flash']['success'] ?? null;
$flashError = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']['success'], $_SESSION['_flash']['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'University Management System') ?> &middot; University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-bg: #111827; --sidebar-accent: #2563eb; }
        body { background: #f3f4f6; }
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; }
        .sidebar .brand { color: #fff; text-decoration: none; letter-spacing: .3px; }
        .sidebar .nav-link { color: #9ca3af; border-radius: .5rem; padding: .65rem .85rem; margin-bottom: .15rem; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); }
        .sidebar .nav-link.active { color: #fff; background: var(--sidebar-accent); }
        .sidebar .nav-link i { width: 1.4rem; display: inline-block; text-align: center; }
        .main-content { min-height: 100vh; }
        .card { border: 0; box-shadow: 0 1px 2px rgba(16,24,40,.06), 0 1px 3px rgba(16,24,40,.1); border-radius: .75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #eef0f3; border-radius: .75rem .75rem 0 0 !important; }
        .table > :not(caption) > * > * { padding: .75rem .75rem; }
        .table thead th { color: #667085; font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; border-bottom-width: 1px; }
        .avatar-circle { width: 38px; height: 38px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: .8rem; }
        .avatar-lg { width: 76px; height: 76px; font-size: 1.5rem; }
        .page-title { font-weight: 700; letter-spacing: -.02em; }
        .btn-action { padding: .2rem .45rem; font-size: .8rem; }
        .sidebar-user { border-top: 1px solid rgba(255,255,255,.1); }
        @media (max-width: 767.98px) { .sidebar { min-height: auto; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 sidebar d-flex flex-column p-3">
            <a href="/" class="brand d-flex align-items-center gap-2 mb-4 px-2">
                <i class="bi bi-mortarboard-fill fs-4"></i>
                <span class="fs-5 fw-bold">University</span>
            </a>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/') ?>" href="/">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/students') ?>" href="/students">
                        <i class="bi bi-people"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/teachers') ?>" href="/teachers">
                        <i class="bi bi-person-video3"></i> Teachers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/schedules') ?>" href="/schedules">
                        <i class="bi bi-calendar-week"></i> Schedule
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive('/disciplines') ?>" href="/disciplines">
                        <i class="bi bi-shield-exclamation"></i> Discipline
                    </a>
                </li>
            </ul>

            <?php if ($authUser ?? null): ?>
                <div class="sidebar-user pt-3 mt-3">
                    <div class="d-flex align-items-center gap-2 px-2 mb-2">
                        <span class="avatar-circle bg-primary text-white"><?= htmlspecialchars(strtoupper(substr($authUser->name, 0, 1))) ?></span>
                        <div class="text-truncate">
                            <div class="text-white small fw-semibold text-truncate"><?= htmlspecialchars($authUser->name) ?></div>
                            <div class="text-secondary small"><?= htmlspecialchars($authUser->getRoleLabel()) ?></div>
                        </div>
                    </div>
                    <form method="POST" action="/logout">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content px-md-4 py-3">
            <?php if ($flashSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($flashSuccess) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($flashError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Please fix the following:</div>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $messages): ?>
                            <?php foreach ($messages as $message): ?>
                                <li><strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $field))) ?>:</strong> <?= htmlspecialchars($message) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach((el) => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        });
    }, 6000);
</script>
</body>
</html>
