<?php

namespace Core;

abstract class BaseController
{
    protected array $data = [];
    protected string $viewPath = '';

    public function __construct()
    {
        $this->viewPath = dirname(__DIR__, 2) . '/views/';
    }

    protected function view(string $view, array $data = []): string
    {
        $this->data = array_merge($this->data, $data);

        $errors = $this->errors();
        $oldInput = $_SESSION['_old_input'] ?? [];
        $old = function (string $key, mixed $default = '') use ($oldInput): mixed {
            return $oldInput[$key] ?? $default;
        };
        $csrfToken = $this->csrfToken();
        $authUser = Auth::user();

        extract($this->data);

        $viewFile = $this->viewPath . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        $layout = $this->data['layout'] ?? 'layouts/app';
        $layoutFile = $this->viewPath . $layout . '.php';

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }

        ob_start();
        include $layoutFile;
        $html = ob_get_clean();

        unset($_SESSION['_old_input']);

        return $html;
    }

    protected function render(string $view, array $data = []): void
    {
        echo $this->view($view, $data);
    }

    protected function redirect(string $url, int $statusCode = 302): never
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    protected function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function abort(int $statusCode, string $message = ''): never
    {
        http_response_code($statusCode);
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];
        $defaultMessage = $messages[$statusCode] ?? 'Error';
        echo $message ?: $defaultMessage;
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->flash(['error' => 'Please log in to continue.']);
            $this->redirect('/login');
        }
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        $validated = [];

        foreach ($rules as $field => $rule) {
            $rulesList = explode('|', $rule);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $singleRule) {
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $error = $this->validateRule($field, $value, $ruleName, $ruleParam, $data);
                if ($error !== null) {
                    $errors[$field][] = $error;
                    break;
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $value;
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $validated;
    }

    protected function validateRule(string $field, mixed $value, string $rule, ?string $param, array $data): ?string
    {
        return match ($rule) {
            'required' => $value === null || $value === '' ? "The {$field} field is required." : null,
            'email' => $value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL) ? "The {$field} must be a valid email address." : null,
            'min' => $value !== null && strlen((string)$value) < (int)$param ? "The {$field} must be at least {$param} characters." : null,
            'max' => $value !== null && strlen((string)$value) > (int)$param ? "The {$field} must not exceed {$param} characters." : null,
            'numeric' => $value !== null && !is_numeric($value) ? "The {$field} must be a number." : null,
            'integer' => $value !== null && !filter_var($value, FILTER_VALIDATE_INT) ? "The {$field} must be an integer." : null,
            'boolean' => $value !== null && !in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0'], true) ? "The {$field} must be a boolean." : null,
            'date' => $value !== null && !$this->isValidDate($value) ? "The {$field} must be a valid date." : null,
            'in' => $value !== null && !in_array($value, explode(',', $param), true) ? "The selected {$field} is invalid." : null,
            'unique' => $this->checkUnique($field, $value, $param) ? "The {$field} has already been taken." : null,
            'confirmed' => $value !== ($data[$field . '_confirmation'] ?? null) ? "The {$field} confirmation does not match." : null,
            'regex' => $value !== null && !preg_match($param, $value) ? "The {$field} format is invalid." : null,
            default => null,
        };
    }

    protected function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    protected function checkUnique(string $field, mixed $value, ?string $param): bool
    {
        if ($param === null || $value === null) {
            return false;
        }

        [$table, $column] = explode(',', $param) + [$field, $field];
        $column = $column ?: $field;

        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?");
        $stmt->execute([$value]);
        return (int)$stmt->fetchColumn() > 0;
    }

    protected function old(string $field, mixed $default = ''): mixed
    {
        return $_SESSION['_old_input'][$field] ?? $default;
    }

    protected function flash(array $data): void
    {
        foreach ($data as $key => $value) {
            $_SESSION['_flash'][$key] = $value;
        }
    }

    protected function withOld(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    protected function withErrors(ValidationException $exception): void
    {
        $_SESSION['_errors'] = $exception->errors();
    }

    protected function errors(): array
    {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return $errors;
    }

    protected function hasErrors(): bool
    {
        return !empty($_SESSION['_errors'] ?? []);
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    protected function verifyCsrfToken(string $token): bool
    {
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }
}
