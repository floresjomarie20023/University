<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-pencil-square me-1"></i>Edit Student</h1>
        <p class="text-muted mb-0">Update <?= htmlspecialchars($student->getFullName()) ?>'s record.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/students/<?= (int)$student->id ?>" class="btn btn-outline-primary"><i class="bi bi-eye me-1"></i>View</a>
        <a href="/students" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <form method="POST" action="/students/<?= (int)$student->id ?>" novalidate>
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="card mb-4">
                <div class="card-header py-3"><h2 class="h6 mb-0">Student Information</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="student_number" class="form-label">Student Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['student_number']) ? 'is-invalid' : '' ?>" id="student_number" name="student_number" value="<?= htmlspecialchars((string)$old('student_number', $student->student_number)) ?>" required>
                            <?php if (isset($errors['student_number'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['student_number'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= htmlspecialchars((string)$old('first_name', $student->first_name)) ?>" required>
                            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['first_name'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= htmlspecialchars((string)$old('last_name', $student->last_name)) ?>" required>
                            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['last_name'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars((string)$old('email', $student->email)) ?>" required>
                            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars((string)$old('phone', $student->phone)) ?>">
                            <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['phone'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" id="gender" name="gender" required>
                                <?php foreach ($genders as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('gender', $student->gender) === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['gender'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['gender'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control <?= isset($errors['date_of_birth']) ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars((string)$old('date_of_birth', $student->date_of_birth)) ?>">
                            <?php if (isset($errors['date_of_birth'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['date_of_birth'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="grade_level" class="form-label">Grade Level <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['grade_level']) ? 'is-invalid' : '' ?>" id="grade_level" name="grade_level" value="<?= htmlspecialchars((string)$old('grade_level', $student->grade_level)) ?>" required>
                            <?php if (isset($errors['grade_level'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['grade_level'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['section']) ? 'is-invalid' : '' ?>" id="section" name="section" value="<?= htmlspecialchars((string)$old('section', $student->section)) ?>" required>
                            <?php if (isset($errors['section'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['section'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="guardian_name" class="form-label">Guardian Name</label>
                            <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars((string)$old('guardian_name', $student->guardian_name)) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="guardian_phone" class="form-label">Guardian Phone</label>
                            <input type="text" class="form-control" id="guardian_phone" name="guardian_phone" value="<?= htmlspecialchars((string)$old('guardian_phone', $student->guardian_phone)) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <?php foreach ($statuses as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('status', $student->status) === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['status'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars((string)$old('address', $student->address)) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Update Student</button>
                <a href="/students" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
