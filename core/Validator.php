<?php
/**
 * Server-Side Input Validator
 * Core File
 */

require_once __DIR__ . '/Database.php';

class Validator {
    private $errors = [];

    /**
     * Validate fields based on rules array
     * Example rules: ['username' => 'required|min:5|unique:users,username']
     */
    public function validate($data, $rules) {
        $db = Database::getInstance()->getConnection();
        
        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? trim($data[$field]) : '';
            $ruleList = explode('|', $fieldRules);
            
            foreach ($ruleList as $rule) {
                // Parse rule options, e.g., min:5 or unique:table,column,except_id
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $paramStr) = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }
                
                switch ($ruleName) {
                    case 'required':
                        if ($value === '') {
                            $this->addError($field, "Kolom ini wajib diisi.");
                        }
                        break;
                        
                    case 'email':
                        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Format email tidak valid.");
                        }
                        break;
                        
                    case 'numeric':
                        if ($value !== '' && !is_numeric($value)) {
                            $this->addError($field, "Kolom ini harus berupa angka.");
                        }
                        break;
                        
                    case 'min':
                        $min = (int)$params[0];
                        if ($value !== '' && strlen($value) < $min) {
                            $this->addError($field, "Panjang kolom minimal {$min} karakter.");
                        }
                        break;

                    case 'max':
                        $max = (int)$params[0];
                        if ($value !== '' && strlen($value) > $max) {
                            $this->addError($field, "Panjang kolom maksimal {$max} karakter.");
                        }
                        break;
                        
                    case 'unique':
                        if ($value !== '') {
                            $table = $params[0];
                            $column = $params[1];
                            $exceptField = $params[2] ?? null;
                            $exceptValue = $params[3] ?? null;
                            
                            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :val";
                            $binds = ['val' => $value];
                            
                            if ($exceptField && $exceptValue) {
                                $sql .= " AND {$exceptField} != :except_val";
                                $binds['except_val'] = $exceptValue;
                            }
                            
                            $stmt = $db->prepare($sql);
                            $stmt->execute($binds);
                            if ($stmt->fetchColumn() > 0) {
                                $this->addError($field, "Data sudah digunakan.");
                            }
                        }
                        break;

                    case 'alphanumeric':
                        if ($value !== '' && !preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
                            $this->addError($field, "Hanya diperbolehkan karakter alfanumerik (huruf, angka, underscore).");
                        }
                        break;
                }
            }
        }
        
        return empty($this->errors);
    }

    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function getErrors() {
        return $this->errors;
    }
}
