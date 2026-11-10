<?php

namespace App\Controllers;

use App\Models\Discipline;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use Core\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $today = date('l');

        $this->render('home/index', [
            'title' => 'Dashboard',
            'totalStudents' => Student::count(),
            'totalTeachers' => Teacher::count(),
            'totalSchedules' => Schedule::count(),
            'openDiscipline' => Discipline::where('status', '=', 'open')->count(),
            'todaySchedules' => Schedule::where('day_of_week', '=', $today)
                ->orderBy('start_time')
                ->get(),
            'recentDisciplines' => Discipline::query()
                ->orderBy('incident_date', 'desc')
                ->limit(5)
                ->get(),
            'today' => $today,
        ]);
    }
}
