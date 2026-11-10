<?php
$flashSuccess = $_SESSION['_flash']['success'] ?? null;
$flashError = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']['success'], $_SESSION['_flash']['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Sign In') ?> &middot; University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top left, #1d4ed8 0%, #111827 45%, #030712 100%);
            padding: 1.5rem;
        }
        .auth-card { width: 100%; max-width: 420px; border: 0; border-radius: 1rem; box-shadow: 0 25px 60px rgba(0,0,0,.45); }
        .brand-mark { width: 56px; height: 56px; border-radius: 16px; background: #2563eb; color: #fff; display: inline-flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="card auth-card">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="brand-mark mb-3"><i class="bi bi-mortarboard-fill fs-3"></i></div>
                <h1 class="h4 fw-bold mb-1">University Management</h1>
                <p class="text-muted mb-0">Sign in to continue</p>
            </div>

            <?php if ($flashSuccess): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($flashSuccess) ?></div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($flashError) ?></div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
