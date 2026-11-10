<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-person-badge me-1"></i>Teacher Details</h1>
        <p class="text-muted mb-0"><?= htmlspecialchars($teacher->employee_number) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/teachers/<?= (int)$teacher->id ?>/edit" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="/teachers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <span class="avatar-circle avatar-lg bg-success-subtle text-success mx-auto mb-3"><?= htmlspecialchars($teacher->getInitials()) ?></span>
                <h2 class="h5 mb-1"><?= htmlspecialchars($teacher->getFullName()) ?></h2>
                <p class="text-muted mb-2"><?= htmlspecialchars($teacher->position) ?></p>
                <?= $teacher->getStatusBadge() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header py-3"><h2 class="h6 mb-0">Employment Information</h2></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Employee Number</dt><dd class="col-sm-8"><?= htmlspecialchars($teacher->employee_number) ?></dd>
                    <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8"><a href="mailto:<?= htmlspecialchars($teacher->email) ?>"><?= htmlspecialchars($teacher->email) ?></a></dd>
                    <dt class="col-sm-4 text-muted">Phone</dt><dd class="col-sm-8"><?= htmlspecialchars($teacher->phone ?: '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Department</dt><dd class="col-sm-8"><?= htmlspecialchars($teacher->department) ?></dd>
                    <dt class="col-sm-4 text-muted">Position</dt><dd class="col-sm-8"><?= htmlspecialchars($teacher->position) ?></dd>
                    <dt class="col-sm-4 text-muted">Gender</dt><dd class="col-sm-8"><?= htmlspecialchars(ucfirst($teacher->gender)) ?></dd>
                    <dt class="col-sm-4 text-muted">Date of Birth</dt>
                    <dd class="col-sm-8"><?= $teacher->date_of_birth ? htmlspecialchars(date('M d, Y', strtotime($teacher->date_of_birth))) : '—' ?></dd>
                    <dt class="col-sm-4 text-muted">Hire Date</dt>
                    <dd class="col-sm-8"><?= $teacher->hire_date ? htmlspecialchars(date('M d, Y', strtotime($teacher->hire_date))) : '—' ?></dd>
                    <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8"><?= htmlspecialchars($teacher->address ?: '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h2 class="h6 mb-0"><i class="bi bi-calendar3 me-1"></i>Assigned Schedule</h2>
        <a href="/schedules/create" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Add Schedule</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr><th>Subject</th><th>Day</th><th>Time</th><th>Room</th><th>Class</th></tr>
            </thead>
            <tbody>
            <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($schedule->subject) ?></td>
                    <td><?= $schedule->getDayBadge() ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($schedule->getTimeRange()) ?></td>
                    <td><?= htmlspecialchars($schedule->room ?: '—') ?></td>
                    <td><?= htmlspecialchars($schedule->grade_level . ' - ' . $schedule->section) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($schedules)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No schedule assigned yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
