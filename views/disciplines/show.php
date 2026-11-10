<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-file-earmark-text me-1"></i>Discipline Record</h1>
        <p class="text-muted mb-0">Incident #<?= (int)$record->id ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/disciplines/<?= (int)$record->id ?>/edit" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="/disciplines" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Incident Details</h2>
                <div class="d-flex gap-2"><?= $record->getCategoryBadge() ?><?= $record->getStatusBadge() ?></div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">Date</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars(date('F d, Y', strtotime($record->incident_date))) ?></dd>
                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($record->description)) ?></dd>
                    <dt class="col-sm-3 text-muted">Action Taken</dt>
                    <dd class="col-sm-9"><?= $record->action_taken ? nl2br(htmlspecialchars($record->action_taken)) : '<span class="text-muted">None recorded</span>' ?></dd>
                    <dt class="col-sm-3 text-muted">Reported By</dt>
                    <dd class="col-sm-9"><?= $teacher ? htmlspecialchars($teacher->getFullName()) : '<span class="text-muted">—</span>' ?></dd>
                    <dt class="col-sm-3 text-muted">Filed On</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars(date('M d, Y g:i A', strtotime($record->created_at))) ?></dd>
                    <dt class="col-sm-3 text-muted">Resolved On</dt>
                    <dd class="col-sm-9 mb-0"><?= $record->resolved_at ? htmlspecialchars(date('M d, Y g:i A', strtotime($record->resolved_at))) : '<span class="text-muted">—</span>' ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><h2 class="h6 mb-0">Student</h2></div>
            <div class="card-body">
                <?php if ($student): ?>
                    <div class="text-center">
                        <span class="avatar-circle avatar-lg bg-primary-subtle text-primary mx-auto mb-3"><?= htmlspecialchars($student->getInitials()) ?></span>
                        <h3 class="h6 mb-1"><a href="/students/<?= (int)$student->id ?>" class="text-decoration-none"><?= htmlspecialchars($student->getFullName()) ?></a></h3>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($student->student_number) ?></p>
                        <p class="small mb-1"><?= htmlspecialchars($student->grade_level . ' - ' . $student->section) ?></p>
                        <?= $student->getStatusBadge() ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Student record no longer exists.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
