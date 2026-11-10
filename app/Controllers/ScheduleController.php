<?php

namespace App\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use Core\BaseController;
use Core\ValidationException;
use Core\Validator;

class ScheduleController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $day = trim((string)($_GET['day'] ?? ''));
        $search = trim((string)($_GET['search'] ?? ''));

        $query = Schedule::query();

        if ($day !== '' && in_array($day, Schedule::days(), true)) {
            $query->where('day_of_week', '=', $day);
        }

        if ($search !== '') {
            $query->where('subject', 'LIKE', "%{$search}%");
        }

        $result = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(12, $page);

        $this->render('schedules/index', [
            'title' => 'Class Schedule',
            'schedules' => $result['data'],
            'pagination' => $result['pagination'],
            'day' => $day,
            'search' => $search,
            'days' => Schedule::days(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('schedules/create', [
            'title' => 'Add Schedule',
            'days' => Schedule::days(),
            'teachers' => $this->teachers(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $data = $this->validateSchedule($_POST);
            Schedule::create($data);

            $this->flash(['success' => 'Schedule created successfully.']);
            $this->redirect('/schedules');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect('/schedules/create');
        }
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $schedule = Schedule::findOrFail($id);

        $this->render('schedules/edit', [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'days' => Schedule::days(),
            'teachers' => $this->teachers(),
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        Schedule::findOrFail($id);

        try {
            $data = $this->validateSchedule($_POST);
            Schedule::update($id, $data);

            $this->flash(['success' => 'Schedule updated successfully.']);
            $this->redirect('/schedules');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect("/schedules/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();

        Schedule::findOrFail($id);
        Schedule::delete($id);

        $this->flash(['success' => 'Schedule deleted successfully.']);
        $this->redirect('/schedules');
    }

    private function teachers(): array
    {
        return Teacher::where('status', '=', 'active')->orderBy('last_name')->get();
    }

    private function validateSchedule(array $input): array
    {
        $days = implode(',', Schedule::days());

        $data = Validator::make($input, [
            'subject' => 'required|min:2|max:100',
            'day_of_week' => "required|in:{$days}",
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'nullable|max:50',
            'grade_level' => 'required|max:30',
            'section' => 'required|max:30',
            'teacher_id' => 'nullable|exists:teachers,id',
            'notes' => 'nullable|max:500',
        ])->validate();

        $errors = [];

        foreach (['start_time', 'end_time'] as $field) {
            if (!preg_match('/^\d{2}:\d{2}$/', (string)$data[$field])) {
                $errors[$field][] = 'Time must use the HH:MM format.';
            }
        }

        if (empty($errors) && $data['end_time'] <= $data['start_time']) {
            $errors['end_time'][] = 'End time must be after the start time.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (($data['teacher_id'] ?? '') === '') {
            $data['teacher_id'] = null;
        }

        if (($data['room'] ?? '') === '') {
            $data['room'] = null;
        }

        if (($data['notes'] ?? '') === '') {
            $data['notes'] = null;
        }

        return $data;
    }
}
