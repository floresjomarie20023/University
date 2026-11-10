<?php

namespace Core;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $messages = [];
    protected array $errors = [];
    protected array $validated = [];

    public static function make(array $data, array $rules, array $messages = []): self
    {
        $validator = new self();
        $validator->data = $data;
        $validator->rules = $rules;
        $validator->messages = $messages;
        return $validator;
    }

    public function validate(): array
    {
        $this->errors = [];
        $this->validated = [];

        foreach ($this->rules as $field => $rule) {
            $rulesList = explode('|', $rule);
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $singleRule) {
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                if (!$this->checkRule($field, $value, $ruleName, $ruleParam)) {
                    $this->addError($field, $ruleName, $ruleParam);
                    break;
                }
            }

            if (!isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }

        return $this->validated;
    }

    public function passes(): bool
    {
        try {
            $this->validate();
            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    protected function checkRule(string $field, mixed $value, string $rule, ?string $param): bool
    {
        return match ($rule) {
            'required' => $this->checkRequired($value),
            'required_if' => $this->checkRequiredIf($field, $value, $param),
            'required_unless' => $this->checkRequiredUnless($field, $value, $param),
            'required_with' => $this->checkRequiredWith($field, $value, $param),
            'required_without' => $this->checkRequiredWithout($field, $value, $param),
            'email' => $this->checkEmail($value),
            'min' => $this->checkMin($value, $param),
            'max' => $this->checkMax($value, $param),
            'between' => $this->checkBetween($value, $param),
            'numeric' => $this->checkNumeric($value),
            'integer' => $this->checkInteger($value),
            'boolean' => $this->checkBoolean($value),
            'date' => $this->checkDate($value),
            'date_format' => $this->checkDateFormat($value, $param),
            'after' => $this->checkAfter($value, $param),
            'before' => $this->checkBefore($value, $param),
            'in' => $this->checkIn($value, $param),
            'not_in' => $this->checkNotIn($value, $param),
            'unique' => $this->checkUnique($field, $value, $param),
            'exists' => $this->checkExists($field, $value, $param),
            'confirmed' => $this->checkConfirmed($field, $value),
            'regex' => $this->checkRegex($value, $param),
            'alpha' => $this->checkAlpha($value),
            'alpha_num' => $this->checkAlphaNum($value),
            'alpha_dash' => $this->checkAlphaDash($value),
            'string' => $this->checkString($value),
            'array' => $this->checkArray($value),
            'file' => $this->checkFile($value),
            'image' => $this->checkImage($value),
            'mimes' => $this->checkMimes($value, $param),
            'max_file_size' => $this->checkMaxFileSize($value, $param),
            'nullable' => $this->checkNullable($value),
            'sometimes' => true,
            default => true,
        };
    }

    protected function checkRequired(mixed $value): bool
    {
        if (is_array($value)) {
            return count($value) > 0;
        }
        return $value !== null && $value !== '';
    }

    protected function checkRequiredIf(string $field, mixed $value, ?string $param): bool
    {
        if ($param === null) return true;
        [$otherField, $otherValue] = explode(',', $param);
        return ($this->data[$otherField] ?? null) == $otherValue ? $this->checkRequired($value) : true;
    }

    protected function checkRequiredUnless(string $field, mixed $value, ?string $param): bool
    {
        if ($param === null) return true;
        [$otherField, $otherValue] = explode(',', $param);
        return ($this->data[$otherField] ?? null) != $otherValue ? $this->checkRequired($value) : true;
    }

    protected function checkRequiredWith(string $field, mixed $value, ?string $param): bool
    {
        if ($param === null) return true;
        $fields = explode(',', $param);
        foreach ($fields as $f) {
            if ($this->checkRequired($this->data[$f] ?? null)) {
                return $this->checkRequired($value);
            }
        }
        return true;
    }

    protected function checkRequiredWithout(string $field, mixed $value, ?string $param): bool
    {
        if ($param === null) return true;
        $fields = explode(',', $param);
        foreach ($fields as $f) {
            if (!$this->checkRequired($this->data[$f] ?? null)) {
                return $this->checkRequired($value);
            }
        }
        return true;
    }

    protected function checkEmail(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function checkMin(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        $min = (int)$param;
        if (is_array($value)) return count($value) >= $min;
        if (is_string($value)) return strlen($value) >= $min;
        return (float)$value >= $min;
    }

    protected function checkMax(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        $max = (int)$param;
        if (is_array($value)) return count($value) <= $max;
        if (is_string($value)) return strlen($value) <= $max;
        return (float)$value <= $max;
    }

    protected function checkBetween(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        [$min, $max] = explode(',', $param);
        return $this->checkMin($value, $min) && $this->checkMax($value, $max);
    }

    protected function checkNumeric(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return is_numeric($value);
    }

    protected function checkInteger(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function checkBoolean(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0'], true);
    }

    protected function checkDate(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }

    protected function checkDateFormat(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        $d = \DateTime::createFromFormat($param, $value);
        return $d && $d->format($param) === $value;
    }

    protected function checkAfter(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        $date = new \DateTime($value);
        $compare = new \DateTime($param);
        return $date > $compare;
    }

    protected function checkBefore(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        $date = new \DateTime($value);
        $compare = new \DateTime($param);
        return $date < $compare;
    }

    protected function checkIn(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        return in_array($value, explode(',', $param), true);
    }

    protected function checkNotIn(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        return !in_array($value, explode(',', $param), true);
    }

    protected function checkUnique(string $field, mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;

        [$table, $column, $exceptId, $exceptColumn] = array_pad(explode(',', $param), 4, null);
        $column = $column ?? $field;
        $exceptColumn = $exceptColumn ?? 'id';

        $db = \Core\Database::getInstance();
        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
        $params = [$value];

        if ($exceptId !== null) {
            $sql .= " AND `{$exceptColumn}` != ?";
            $params[] = $exceptId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() === 0;
    }

    protected function checkExists(string $field, mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;

        [$table, $column] = array_pad(explode(',', $param), 2, null);
        $column = $column ?? $field;

        $db = \Core\Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?");
        $stmt->execute([$value]);
        return (int)$stmt->fetchColumn() > 0;
    }

    protected function checkConfirmed(string $field, mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return $value === ($this->data[$field . '_confirmation'] ?? null);
    }

    protected function checkRegex(mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '' || $param === null) return true;
        return preg_match($param, $value) === 1;
    }

    protected function checkAlpha(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return ctype_alpha($value);
    }

    protected function checkAlphaNum(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return ctype_alnum($value);
    }

    protected function checkAlphaDash(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return preg_match('/^[\w-]+$/', $value) === 1;
    }

    protected function checkString(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return is_string($value);
    }

    protected function checkArray(mixed $value): bool
    {
        if ($value === null || $value === '') return true;
        return is_array($value);
    }

    protected function checkFile(mixed $value): bool
    {
        if ($value === null) return true;
        return isset($value['error']) && $value['error'] === UPLOAD_ERR_OK;
    }

    protected function checkImage(mixed $value): bool
    {
        if (!$this->checkFile($value)) return true;
        $mime = mime_content_type($value['tmp_name']);
        return str_starts_with($mime, 'image/');
    }

    protected function checkMimes(mixed $value, ?string $param): bool
    {
        if (!$this->checkFile($value) || $param === null) return true;
        $mime = mime_content_type($value['tmp_name']);
        $allowed = explode(',', $param);
        return in_array($mime, $allowed, true);
    }

    protected function checkMaxFileSize(mixed $value, ?string $param): bool
    {
        if (!$this->checkFile($value) || $param === null) return true;
        $maxBytes = (int)$param * 1024;
        return $value['size'] <= $maxBytes;
    }

    protected function checkNullable(mixed $value): bool
    {
        return true;
    }

    protected function addError(string $field, string $rule, ?string $param): void
    {
        $message = $this->messages["{$field}.{$rule}"] ?? $this->messages[$rule] ?? $this->getDefaultMessage($field, $rule, $param);
        $this->errors[$field][] = $message;
    }

    protected function getDefaultMessage(string $field, string $rule, ?string $param): string
    {
        return match ($rule) {
            'required' => "The {$field} field is required.",
            'required_if' => "The {$field} field is required when {$param} is present.",
            'required_unless' => "The {$field} field is required unless {$param} is present.",
            'required_with' => "The {$field} field is required when {$param} is present.",
            'required_without' => "The {$field} field is required when {$param} is not present.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$param} characters.",
            'max' => "The {$field} must not exceed {$param} characters.",
            'between' => "The {$field} must be between {$param}.",
            'numeric' => "The {$field} must be a number.",
            'integer' => "The {$field} must be an integer.",
            'boolean' => "The {$field} must be true or false.",
            'date' => "The {$field} must be a valid date.",
            'date_format' => "The {$field} does not match the format {$param}.",
            'after' => "The {$field} must be a date after {$param}.",
            'before' => "The {$field} must be a date before {$param}.",
            'in' => "The selected {$field} is invalid.",
            'not_in' => "The selected {$field} is invalid.",
            'unique' => "The {$field} has already been taken.",
            'exists' => "The selected {$field} is invalid.",
            'confirmed' => "The {$field} confirmation does not match.",
            'regex' => "The {$field} format is invalid.",
            'alpha' => "The {$field} must only contain letters.",
            'alpha_num' => "The {$field} must only contain letters and numbers.",
            'alpha_dash' => "The {$field} must only contain letters, numbers, dashes and underscores.",
            'string' => "The {$field} must be a string.",
            'array' => "The {$field} must be an array.",
            'file' => "The {$field} must be a file.",
            'image' => "The {$field} must be an image.",
            'mimes' => "The {$field} must be a file of type: {$param}.",
            'max_file_size' => "The {$field} must not exceed {$param} KB.",
            default => "The {$field} is invalid.",
        };
    }
}
