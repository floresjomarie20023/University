<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-person-badge me-1"></i>Student Details</h1>
        <p class="text-muted mb-0"><?= htmlspecialchars($student->student_number) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/students/<?= (int)$student->id ?>/edit" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="/students" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <span class="avatar-circle avatar-lg bg-primary-subtle text-primary mx-auto mb-3"><?= htmlspecialchars($student->getInitials()) ?></span>
                <h2 class="h5 mb-1"><?= htmlspecialchars($student->getFullName()) ?></h2>
                <p class="text-muted mb-2"><?= htmlspecialchars($student->grade_level . ' - ' . $student->section) ?></p>
                <?= $student->getStatusBadge() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header py-3"><h2 class="h6 mb-0">Personal Information</h2></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Student Number</dt><dd class="col-sm-8"><?= htmlspecialchars($student->student_number) ?></dd>
                    <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8"><a href="mailto:<?= htmlspecialchars($student->email) ?>"><?= htmlspecialchars($student->email) ?></a></dd>
                    <dt class="col-sm-4 text-muted">Phone</dt><dd class="col-sm-8"><?= htmlspecialchars($student->phone ?: '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Gender</dt><dd class="col-sm-8"><?= htmlspecialchars(ucfirst($student->gender)) ?></dd>
                    <dt class="col-sm-4 text-muted">Date of Birth</dt>
                    <dd class="col-sm-8"><?= $student->date_of_birth ? htmlspecialchars(date('M d, Y', strtotime($student->date_of_birth))) : '—' ?></dd>
                    <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8"><?= htmlspecialchars($student->address ?: '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Guardian</dt><dd class="col-sm-8"><?= htmlspecialchars($student->guardian_name ?: '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Guardian Phone</dt><dd class="col-sm-8"><?= htmlspecialchars($student->guardian_phone ?: '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h2 class="h6 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Discipline History</h2>
        <a href="/disciplines/create?student_id=<?= (int)$student->id ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-plus-lg me-1"></i>File Record</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr><th>Date</th><th>Category</th><th>Description</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td class="text-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($record->incident_date))) ?></td>
                    <td><?= $record->getCategoryBadge() ?></td>
                    <td class="text-truncate" style="max-width: 320px;"><?= htmlspecialchars($record->description) ?></td>
                    <td><?= $record->getStatusBadge() ?></td>
                    <td class="text-end"><a href="/disciplines/<?= (int)$record->id ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($records)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No discipline records.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
