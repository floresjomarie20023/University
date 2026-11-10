<?php

namespace App\Controllers;

use App\Models\Discipline;
use App\Models\Student;
use App\Models\Teacher;
use Core\BaseController;
use Core\ValidationException;
use Core\Validator;

class DisciplineController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = trim((string)($_GET['status'] ?? ''));
        $category = trim((string)($_GET['category'] ?? ''));
        $search = trim((string)($_GET['search'] ?? ''));

        $query = Discipline::query();

        if ($status !== '' && in_array($status, Discipline::statuses(), true)) {
            $query->where('status', '=', $status);
        }

        if ($category !== '' && in_array($category, Discipline::categories(), true)) {
            $query->where('category', '=', $category);
        }

        if ($search !== '') {
            $query->where('description', 'LIKE', "%{$search}%");
        }

        $result = $query->orderBy('incident_date', 'desc')
            ->paginate(10, $page);

        $this->render('disciplines/index', [
            'title' => 'Discipline Records',
            'records' => $result['data'],
            'pagination' => $result['pagination'],
            'status' => $status,
            'category' => $category,
            'search' => $search,
            'statuses' => Discipline::statuses(),
            'categories' => Discipline::categories(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('disciplines/create', [
            'title' => 'File Discipline Record',
            'students' => $this->students(),
            'teachers' => $this->teachers(),
            'categories' => Discipline::categories(),
            'statuses' => Discipline::statuses(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $data = $this->validateRecord($_POST);
            Discipline::create($data);

            $this->flash(['success' => 'Discipline record filed successfully.']);
            $this->redirect('/disciplines');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect('/disciplines/create');
        }
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $record = Discipline::findOrFail($id);

        $this->render('disciplines/show', [
            'title' => 'Discipline Record',
            'record' => $record,
            'student' => $record->getStudent(),
            'teacher' => $record->getTeacher(),
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $record = Discipline::findOrFail($id);

        $this->render('disciplines/edit', [
            'title' => 'Edit Discipline Record',
            'record' => $record,
            'students' => $this->students(),
            'teachers' => $this->teachers(),
            'categories' => Discipline::categories(),
            'statuses' => Discipline::statuses(),
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        Discipline::findOrFail($id);

        try {
            $data = $this->validateRecord($_POST);
            Discipline::update($id, $data);

            $this->flash(['success' => 'Discipline record updated successfully.']);
            $this->redirect('/disciplines');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect("/disciplines/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();

        Discipline::findOrFail($id);
        Discipline::delete($id);

        $this->flash(['success' => 'Discipline record deleted successfully.']);
        $this->redirect('/disciplines');
    }

    private function students(): array
    {
        return Student::where('status', '!=', 'graduated')->orderBy('last_name')->get();
    }

    private function teachers(): array
    {
        return Teacher::where('status', '=', 'active')->orderBy('last_name')->get();
    }

    private function validateRecord(array $input): array
    {
        $data = Validator::make($input, [
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'incident_date' => 'required|date',
            'category' => 'required|in:minor,major,severe',
            'description' => 'required|min:5',
            'action_taken' => 'nullable|max:1000',
            'status' => 'required|in:open,resolved,appealed',
        ])->validate();

        if (($data['teacher_id'] ?? '') === '') {
            $data['teacher_id'] = null;
        }

        if (($data['action_taken'] ?? '') === '') {
            $data['action_taken'] = null;
        }

        $data['resolved_at'] = $data['status'] === 'resolved'
            ? date('Y-m-d H:i:s')
            : null;

        return $data;
    }
}
