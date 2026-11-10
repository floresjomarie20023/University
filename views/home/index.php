<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Overview of students, teachers, schedules and discipline records.</p>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('F j, Y') ?></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Students</div>
                    <div class="h3 mb-0 fw-bold"><?= (int)$totalStudents ?></div>
                </div>
                <div class="avatar-circle bg-primary-subtle text-primary fs-5"><i class="bi bi-people"></i></div>
            </div>
            <a href="/students" class="card-footer bg-white text-decoration-none small text-primary border-0">Manage students <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Teachers</div>
                    <div class="h3 mb-0 fw-bold"><?= (int)$totalTeachers ?></div>
                </div>
                <div class="avatar-circle bg-success-subtle text-success fs-5"><i class="bi bi-person-video3"></i></div>
            </div>
            <a href="/teachers" class="card-footer bg-white text-decoration-none small text-success border-0">Manage teachers <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Class Schedules</div>
                    <div class="h3 mb-0 fw-bold"><?= (int)$totalSchedules ?></div>
                </div>
                <div class="avatar-circle bg-info-subtle text-info fs-5"><i class="bi bi-calendar-week"></i></div>
            </div>
            <a href="/schedules" class="card-footer bg-white text-decoration-none small text-info border-0">View schedule <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Open Cases</div>
                    <div class="h3 mb-0 fw-bold"><?= (int)$openDiscipline ?></div>
                </div>
                <div class="avatar-circle bg-warning-subtle text-warning fs-5"><i class="bi bi-shield-exclamation"></i></div>
            </div>
            <a href="/disciplines" class="card-footer bg-white text-decoration-none small text-warning border-0">Review discipline <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h2 class="h6 mb-0"><i class="bi bi-calendar-day me-1"></i>Today's Classes &middot; <?= htmlspecialchars($today) ?></h2>
                <a href="/schedules" class="btn btn-sm btn-outline-primary">Full schedule</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Room</th>
                        <th>Teacher</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($todaySchedules as $schedule): ?>
                        <tr>
                            <td class="text-nowrap small"><?= htmlspecialchars($schedule->getTimeRange()) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($schedule->subject) ?></td>
                            <td><?= htmlspecialchars($schedule->grade_level . ' - ' . $schedule->section) ?></td>
                            <td><?= htmlspecialchars($schedule->room ?: '—') ?></td>
                            <td>
                                <?php $teacher = $schedule->getTeacher(); ?>
                                <?= $teacher ? htmlspecialchars($teacher->getFullName()) : '<span class="text-muted">Unassigned</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($todaySchedules)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-cup-hot fs-4 d-block mb-1"></i>No classes scheduled for today.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h2 class="h6 mb-0"><i class="bi bi-shield-exclamation me-1"></i>Recent Discipline Records</h2>
                <a href="/disciplines" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentDisciplines as $record): ?>
                        <?php $student = $record->getStudent(); ?>
                        <tr>
                            <td>
                                <a href="/disciplines/<?= (int)$record->id ?>" class="text-decoration-none fw-semibold">
                                    <?= $student ? htmlspecialchars($student->getFullName()) : 'Unknown' ?>
                                </a>
                            </td>
                            <td><?= $record->getCategoryBadge() ?></td>
                            <td><?= $record->getStatusBadge() ?></td>
                            <td class="small text-muted text-nowrap"><?= date('M j, Y', strtotime($record->incident_date)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentDisciplines)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No discipline records yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
