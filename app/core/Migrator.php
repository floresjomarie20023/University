<?php

namespace Core;

use PDO;

class Migrator
{
    private static bool $ran = false;

    public static function run(PDO $db): void
    {
        if (self::$ran) {
            return;
        }
        self::$ran = true;

        self::createTables($db);
        self::seed($db);
    }

    private static function createTables(PDO $db): void
    {
        $statements = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user',
                is_active INTEGER NOT NULL DEFAULT 1,
                remember_token TEXT,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS students (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_number TEXT NOT NULL UNIQUE,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                phone TEXT,
                gender TEXT NOT NULL DEFAULT 'other',
                date_of_birth TEXT,
                grade_level TEXT NOT NULL,
                section TEXT NOT NULL,
                guardian_name TEXT,
                guardian_phone TEXT,
                address TEXT,
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS teachers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                employee_number TEXT NOT NULL UNIQUE,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                phone TEXT,
                gender TEXT NOT NULL DEFAULT 'other',
                date_of_birth TEXT,
                department TEXT NOT NULL,
                position TEXT NOT NULL,
                hire_date TEXT,
                address TEXT,
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS schedules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                subject TEXT NOT NULL,
                day_of_week TEXT NOT NULL,
                start_time TEXT NOT NULL,
                end_time TEXT NOT NULL,
                room TEXT,
                grade_level TEXT NOT NULL,
                section TEXT NOT NULL,
                teacher_id INTEGER,
                notes TEXT,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
            )",
            "CREATE TABLE IF NOT EXISTS disciplines (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                teacher_id INTEGER,
                incident_date TEXT NOT NULL,
                category TEXT NOT NULL DEFAULT 'minor',
                description TEXT NOT NULL,
                action_taken TEXT,
                status TEXT NOT NULL DEFAULT 'open',
                resolved_at TEXT,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
                FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
            )",
            "CREATE INDEX IF NOT EXISTS students_name_index ON students(last_name, first_name)",
            "CREATE INDEX IF NOT EXISTS schedules_day_index ON schedules(day_of_week)",
            "CREATE INDEX IF NOT EXISTS disciplines_student_index ON disciplines(student_id)",
            "CREATE INDEX IF NOT EXISTS disciplines_status_index ON disciplines(status)",
        ];

        foreach ($statements as $sql) {
            $db->exec($sql);
        }
    }

    private static function seed(PDO $db): void
    {
        $now = date('Y-m-d H:i:s');

        if ((int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute(['System Administrator', 'admin@university.edu', password_hash('password', PASSWORD_BCRYPT), 'admin', 1, $now, $now]);
            $stmt->execute(['Maria Santos', 'teacher@university.edu', password_hash('password', PASSWORD_BCRYPT), 'teacher', 1, $now, $now]);
        }

        if ((int)$db->query('SELECT COUNT(*) FROM teachers')->fetchColumn() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO teachers (id, employee_number, first_name, last_name, email, phone, gender,
                    date_of_birth, department, position, hire_date, address, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $teachers = [
                [1, 'T-1001', 'Maria', 'Santos', 'maria.santos@university.edu', '09170000001', 'female', '1985-04-12', 'Science', 'Senior Teacher', '2012-06-01', '12 Acacia St., Quezon City', 'active'],
                [2, 'T-1002', 'Daniel', 'Reyes', 'daniel.reyes@university.edu', '09170000002', 'male', '1982-11-03', 'Mathematics', 'Teacher III', '2010-06-15', '45 Mabini Ave., Makati', 'active'],
                [3, 'T-1003', 'Angela', 'Cruz', 'angela.cruz@university.edu', '09170000003', 'female', '1990-07-21', 'English', 'Teacher II', '2016-08-01', '8 Sampaguita Lane, Pasig', 'active'],
                [4, 'T-1004', 'Victor', 'Lim', 'victor.lim@university.edu', '09170000004', 'male', '1978-01-30', 'Social Studies', 'Department Head', '2008-06-01', '22 Rizal St., Manila', 'active'],
            ];

            foreach ($teachers as $teacher) {
                $stmt->execute([...$teacher, $now, $now]);
            }
        }

        if ((int)$db->query('SELECT COUNT(*) FROM students')->fetchColumn() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO students (id, student_number, first_name, last_name, email, phone, gender,
                    date_of_birth, grade_level, section, guardian_name, guardian_phone, address, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $students = [
                [1, 'S-2024-001', 'Liam', 'Garcia', 'liam.garcia@student.edu', '09280000001', 'male', '2008-02-14', 'Grade 10', 'A', 'Ramon Garcia', '09180000001', '5 Narra St., Quezon City', 'active'],
                [2, 'S-2024-002', 'Sofia', 'Mendoza', 'sofia.mendoza@student.edu', '09280000002', 'female', '2008-09-08', 'Grade 10', 'A', 'Carla Mendoza', '09180000002', '10 Molave St., Quezon City', 'active'],
                [3, 'S-2024-003', 'Noah', 'Aquino', 'noah.aquino@student.edu', '09280000003', 'male', '2009-05-19', 'Grade 9', 'B', 'Peter Aquino', '09180000003', '77 Katipunan Ave., Quezon City', 'active'],
                [4, 'S-2024-004', 'Isabella', 'Torres', 'isabella.torres@student.edu', '09280000004', 'female', '2007-12-02', 'Grade 11', 'C', 'Liza Torres', '09180000004', '14 Aguinaldo St., Marikina', 'active'],
                [5, 'S-2024-005', 'Ethan', 'Villanueva', 'ethan.villanueva@student.edu', '09280000005', 'male', '2009-03-27', 'Grade 9', 'B', 'Mark Villanueva', '09180000005', '3 Camia St., Pasig', 'inactive'],
                [6, 'S-2024-006', 'Mia', 'Castillo', 'mia.castillo@student.edu', '09280000006', 'female', '2006-08-16', 'Grade 12', 'D', 'Grace Castillo', '09180000006', '9 Ilang-Ilang St., Mandaluyong', 'graduated'],
            ];

            foreach ($students as $student) {
                $stmt->execute([...$student, $now, $now]);
            }
        }

        if ((int)$db->query('SELECT COUNT(*) FROM schedules')->fetchColumn() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO schedules (subject, day_of_week, start_time, end_time, room, grade_level, section, teacher_id, notes, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $schedules = [
                ['Mathematics', 'Monday', '08:00', '09:00', 'Room 201', 'Grade 10', 'A', 2, 'Bring scientific calculator.'],
                ['Science', 'Monday', '09:15', '10:15', 'Lab 1', 'Grade 10', 'A', 1, 'Laboratory gown required.'],
                ['English', 'Monday', '10:30', '11:30', 'Room 105', 'Grade 9', 'B', 3, null],
                ['Social Studies', 'Tuesday', '08:00', '09:00', 'Room 303', 'Grade 11', 'C', 4, null],
                ['Mathematics', 'Tuesday', '09:15', '10:15', 'Room 201', 'Grade 9', 'B', 2, null],
                ['Science', 'Wednesday', '08:00', '09:00', 'Lab 2', 'Grade 12', 'D', 1, 'Practical exam preparation.'],
                ['English', 'Thursday', '10:30', '11:30', 'Room 105', 'Grade 10', 'A', 3, null],
                ['Social Studies', 'Friday', '08:00', '09:00', 'Room 303', 'Grade 9', 'B', 4, null],
            ];

            foreach ($schedules as $schedule) {
                $stmt->execute([...$schedule, $now, $now]);
            }
        }

        if ((int)$db->query('SELECT COUNT(*) FROM disciplines')->fetchColumn() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO disciplines (student_id, teacher_id, incident_date, category, description, action_taken, status, resolved_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $records = [
                [5, 2, '2026-10-02', 'minor', 'Repeatedly talking during class after two warnings.', 'Verbal warning issued; parent notified by email.', 'resolved', '2026-10-03 09:00:00'],
                [3, 3, '2026-10-11', 'major', 'Submitted an assignment copied from another student.', 'Assignment marked zero and student counselled.', 'resolved', '2026-10-12 14:30:00'],
                [1, 1, '2026-10-21', 'minor', 'Arrived 25 minutes late to Science class.', 'Late slip issued.', 'open', null],
                [4, 4, '2026-11-03', 'severe', 'Used inappropriate language toward a classmate.', 'Referred to the discipline committee; guardian meeting scheduled.', 'open', null],
                [2, 2, '2026-11-06', 'minor', 'Forgot to bring required materials for the third time.', 'Warning noted; follow-up with adviser.', 'open', null],
            ];

            foreach ($records as $record) {
                $stmt->execute([...$record, $now, $now]);
            }
        }
    }
}
