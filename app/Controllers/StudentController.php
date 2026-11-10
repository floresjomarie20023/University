<?php

namespace App\Controllers;

use App\Models\Student;
use Core\BaseController;
use Core\ValidationException;
use Core\Validator;

class StudentController extends BaseController
{
    private const GENDERS = ['male', 'female', 'other'];
    private const STATUSES = ['active', 'inactive', 'graduated'];

    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim((string)($_GET['search'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));

        $query = Student::query();

        if ($search !== '' && $status !== '' && in_array($status, self::STATUSES, true)) {
            $query->where('status', '=', $status)
                ->where('last_name', 'LIKE', "%{$search}%");
        } else {
            if ($search !== '') {
                $query->where('student_number', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            }

            if ($status !== '' && in_array($status, self::STATUSES, true)) {
                $query->where('status', '=', $status);
            }
        }

        $result = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10, $page);

        $this->render('students/index', [
            'title' => 'Students',
            'students' => $result['data'],
            'pagination' => $result['pagination'],
            'search' => $search,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->render('students/create', [
            'title' => 'Add Student',
            'genders' => self::GENDERS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        try {
            $data = Validator::make($_POST, [
                'student_number' => 'required|max:30|unique:students,student_number',
                'first_name' => 'required|min:2|max:80',
                'last_name' => 'required|min:2|max:80',
                'email' => 'required|email|max:150|unique:students,email',
                'phone' => 'nullable|max:30',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'grade_level' => 'required|max:30',
                'section' => 'required|max:30',
                'guardian_name' => 'nullable|max:120',
                'guardian_phone' => 'nullable|max:30',
                'address' => 'nullable|max:255',
                'status' => 'required|in:active,inactive,graduated',
            ])->validate();

            $data = $this->normalize($data);
            Student::create($data);

            $this->flash(['success' => 'Student added successfully.']);
            $this->redirect('/students');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect('/students/create');
        }
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $student = Student::findOrFail($id);

        $this->render('students/show', [
            'title' => 'Student Details',
            'student' => $student,
            'records' => $student->getDisciplineRecords(),
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $student = Student::findOrFail($id);

        $this->render('students/edit', [
            'title' => 'Edit Student',
            'student' => $student,
            'genders' => self::GENDERS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $student = Student::findOrFail($id);

        try {
            $data = Validator::make($_POST, [
                'student_number' => "required|max:30|unique:students,student_number,{$id},id",
                'first_name' => 'required|min:2|max:80',
                'last_name' => 'required|min:2|max:80',
                'email' => "required|email|max:150|unique:students,email,{$id},id",
                'phone' => 'nullable|max:30',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'grade_level' => 'required|max:30',
                'section' => 'required|max:30',
                'guardian_name' => 'nullable|max:120',
                'guardian_phone' => 'nullable|max:30',
                'address' => 'nullable|max:255',
                'status' => 'required|in:active,inactive,graduated',
            ])->validate();

            $data = $this->normalize($data);
            Student::update($id, $data);

            $this->flash(['success' => 'Student updated successfully.']);
            $this->redirect('/students');
        } catch (ValidationException $e) {
            $this->withErrors($e);
            $this->withOld($_POST);
            $this->redirect("/students/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();

        Student::findOrFail($id);
        Student::delete($id);

        $this->flash(['success' => 'Student deleted successfully.']);
        $this->redirect('/students');
    }

    private function normalize(array $data): array
    {
        foreach (['phone', 'date_of_birth', 'guardian_name', 'guardian_phone', 'address'] as $field) {
            if (($data[$field] ?? '') === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
