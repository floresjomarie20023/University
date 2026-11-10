<?php

namespace App\Controllers;

use App\Models\Teacher;
use Core\BaseController;
use Core\ValidationException;
use Core\Validator;

class TeacherController extends BaseController
{
    private const GENDERS = ['male', 'female', 'other'];
    private const STATUSES = ['active', 'inactive', 'on_leave'];

    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim((string)($_GET['search'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));

        $query = Teacher::query();

        if ($search !== '' && $status !== '' && in_array($status, self::STATUSES, true)) {
            $query->where('status', '=', $status)
                ->where('last_name', 'LIKE', "%{$search}%");
        } else {
            if ($search !== '') {
                $query->where('employee_number', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('department', 'LIKE', "%{$search}%");
            }

            if ($status !== '' && in_array($status, self::STATUSES, true)) {
                $query->where('status', '=', $status);
            }
        }

        $result = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10, $page);

        $this->render('teachers/index', [
            'title' => 'Teachers',
            'teachers' => $result['data'],
            'pagination' => $result['pagination'],
            'search' => $search,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('teachers/create', [
            'title' => 'Add Teacher',
            'genders' => self::GENDERS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $data = Validator::make($_POST, [
                'employee_number' => 'required|max:30|unique:teachers,employee_number',
                'first_name' => 'required|min:2|max:80',
                'last_name' => 'required|min:2|max:80',
                'email' => 'required|email|max:150|unique:teachers,email',
                'phone' => 'nullable|max:30',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'department' => 'required|max:80',
                'position' => 'required|max:80',
                'hire_date' => 'nullable|date',
                'address' => 'nullable|max:255',
                'status' => 'required|in:active,inactive,on_leave',
            ])->validate();

            $data = $this->normalize($data);
            Teacher::create($data);

            $this->flash(['success' => 'Teacher added successfully.']);
            $this->redirect('/teachers');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect('/teachers/create');
        }
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $teacher = Teacher::findOrFail($id);

        $this->render('teachers/show', [
            'title' => 'Teacher Details',
            'teacher' => $teacher,
            'schedules' => $teacher->getSchedules(),
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $teacher = Teacher::findOrFail($id);

        $this->render('teachers/edit', [
            'title' => 'Edit Teacher',
            'teacher' => $teacher,
            'genders' => self::GENDERS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        Teacher::findOrFail($id);

        try {
            $data = Validator::make($_POST, [
                'employee_number' => "required|max:30|unique:teachers,employee_number,{$id},id",
                'first_name' => 'required|min:2|max:80',
                'last_name' => 'required|min:2|max:80',
                'email' => "required|email|max:150|unique:teachers,email,{$id},id",
                'phone' => 'nullable|max:30',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'department' => 'required|max:80',
                'position' => 'required|max:80',
                'hire_date' => 'nullable|date',
                'address' => 'nullable|max:255',
                'status' => 'required|in:active,inactive,on_leave',
            ])->validate();

            $data = $this->normalize($data);
            Teacher::update($id, $data);

            $this->flash(['success' => 'Teacher updated successfully.']);
            $this->redirect('/teachers');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect("/teachers/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();

        Teacher::findOrFail($id);
        Teacher::delete($id);

        $this->flash(['success' => 'Teacher deleted successfully.']);
        $this->redirect('/teachers');
    }

    private function normalize(array $data): array
    {
        foreach (['phone', 'date_of_birth', 'hire_date', 'address'] as $field) {
            if (($data[$field] ?? '') === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
