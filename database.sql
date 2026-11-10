-- University Management System - MySQL schema
-- Default runtime uses SQLite and auto-migrates (see app/core/Migrator.php).
-- Use this file only when DB_DRIVER=mysql (auto-migrate is off for MySQL).

CREATE DATABASE IF NOT EXISTS `university_crud`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `university_crud`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'user',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `remember_token` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `students` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_number` VARCHAR(30) NOT NULL,
    `first_name` VARCHAR(80) NOT NULL,
    `last_name` VARCHAR(80) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `gender` VARCHAR(10) NOT NULL DEFAULT 'other',
    `date_of_birth` DATE NULL,
    `grade_level` VARCHAR(30) NOT NULL,
    `section` VARCHAR(30) NOT NULL,
    `guardian_name` VARCHAR(120) NULL,
    `guardian_phone` VARCHAR(30) NULL,
    `address` VARCHAR(255) NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_number_unique` (`student_number`),
    UNIQUE KEY `students_email_unique` (`email`),
    KEY `students_name_index` (`last_name`, `first_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `teachers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `employee_number` VARCHAR(30) NOT NULL,
    `first_name` VARCHAR(80) NOT NULL,
    `last_name` VARCHAR(80) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `gender` VARCHAR(10) NOT NULL DEFAULT 'other',
    `date_of_birth` DATE NULL,
    `department` VARCHAR(80) NOT NULL,
    `position` VARCHAR(80) NOT NULL,
    `hire_date` DATE NULL,
    `address` VARCHAR(255) NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `teachers_number_unique` (`employee_number`),
    UNIQUE KEY `teachers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `schedules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `subject` VARCHAR(100) NOT NULL,
    `day_of_week` VARCHAR(15) NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `room` VARCHAR(50) NULL,
    `grade_level` VARCHAR(30) NOT NULL,
    `section` VARCHAR(30) NOT NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `notes` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `schedules_day_index` (`day_of_week`),
    CONSTRAINT `schedules_teacher_fk` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `disciplines` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `incident_date` DATE NOT NULL,
    `category` VARCHAR(20) NOT NULL DEFAULT 'minor',
    `description` TEXT NOT NULL,
    `action_taken` TEXT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'open',
    `resolved_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `disciplines_student_index` (`student_id`),
    KEY `disciplines_status_index` (`status`),
    CONSTRAINT `disciplines_student_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `disciplines_teacher_fk` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo accounts (password: password)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('System Administrator', 'admin@university.edu', '$2y$10$EXMtIypWEKuf3cqzFDp9LeLKwe4UoukaQuQNKMyLFUEsOjbWb.elG', 'admin', 1, NOW(), NOW()),
    ('Maria Santos', 'teacher@university.edu', '$2y$10$EXMtIypWEKuf3cqzFDp9LeLKwe4UoukaQuQNKMyLFUEsOjbWb.elG', 'teacher', 1, NOW(), NOW());
