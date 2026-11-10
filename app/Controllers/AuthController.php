<?php

namespace App\Controllers;

use Core\Auth;
use Core\BaseController;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title' => 'Sign In',
            'layout' => 'layouts/auth',
        ]);
    }

    public function login(): void
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->flash(['error' => 'Email and password are required.']);
            $this->withOld(['email' => $email]);
            $this->redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            $this->flash(['error' => 'Invalid email or password, or the account is inactive.']);
            $this->withOld(['email' => $email]);
            $this->redirect('/login');
        }

        $this->flash(['success' => 'Welcome back, ' . Auth::user()->name . '!']);
        $this->redirect('/');
    }

    public function logout(): void
    {
        $name = Auth::user()?->name;

        Auth::logout();

        $this->flash(['success' => 'You have been signed out' . ($name ? ', ' . $name : '') . '.']);
        $this->redirect('/login');
    }
}
