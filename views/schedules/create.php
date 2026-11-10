<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-calendar-plus me-1"></i>Add Schedule</h1>
        <p class="text-muted mb-0">Create a new class schedule entry.</p>
    </div>
    <a href="/schedules" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Schedule</a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <form method="POST" action="/schedules" novalidate>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="card mb-4">
                <div class="card-header py-3"><h2 class="h6 mb-0">Schedule Information</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" id="subject" name="subject" value="<?= htmlspecialchars((string)$old('subject')) ?>" placeholder="Mathematics" required>
                            <?php if (isset($errors['subject'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['subject'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="day_of_week" class="form-label">Day <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['day_of_week']) ? 'is-invalid' : '' ?>" id="day_of_week" name="day_of_week" required>
                                <option value="">Select day</option>
                                <?php foreach ($days as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('day_of_week') === $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['day_of_week'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['day_of_week'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>" id="start_time" name="start_time" value="<?= htmlspecialchars((string)$old('start_time')) ?>" required>
                            <?php if (isset($errors['start_time'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['start_time'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>" id="end_time" name="end_time" value="<?= htmlspecialchars((string)$old('end_time')) ?>" required>
                            <?php if (isset($errors['end_time'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['end_time'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="room" class="form-label">Room</label>
                            <input type="text" class="form-control <?= isset($errors['room']) ? 'is-invalid' : '' ?>" id="room" name="room" value="<?= htmlspecialchars((string)$old('room')) ?>" placeholder="Room 201">
                            <?php if (isset($errors['room'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['room'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="teacher_id" class="form-label">Teacher</label>
                            <select class="form-select <?= isset($errors['teacher_id']) ? 'is-invalid' : '' ?>" id="teacher_id" name="teacher_id">
                                <option value="">Unassigned</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= (int)$teacher->id ?>" <?= (string)$old('teacher_id') === (string)$teacher->id ? 'selected' : '' ?>><?= htmlspecialchars($teacher->getFullName()) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['teacher_id'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['teacher_id'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="grade_level" class="form-label">Grade Level <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['grade_level']) ? 'is-invalid' : '' ?>" id="grade_level" name="grade_level" value="<?= htmlspecialchars((string)$old('grade_level')) ?>" placeholder="Grade 10" required>
                            <?php if (isset($errors['grade_level'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['grade_level'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['section']) ? 'is-invalid' : '' ?>" id="section" name="section" value="<?= htmlspecialchars((string)$old('section')) ?>" placeholder="A" required>
                            <?php if (isset($errors['section'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['section'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"><?= htmlspecialchars((string)$old('notes')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Save Schedule</button>
                <a href="/schedules" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
