<?php
/**
 * BelovTest
 * Validator
 */
namespace App\Services;

use App\Services\Utils;

class Validator
{
  protected $data;
  protected $rules;
  protected $fieldNames = [
    'login' => 'Логин',
    'password' => 'Пароль',
];

  // Конструктор класса, принимающий данные и правила валидации
  public function __construct($data, $rules)
  {
    $this->data = $data;
    $this->rules = $rules;
  }

  // Метод для выполнения валидации
  public function validate()
  {
    foreach ($this->rules as $field => $rules) {
        $fieldRules = explode('|', $rules);
        foreach ($fieldRules as $rule) {
            $this->applyRule($field, $rule);
        }
    }
  }

  // Применение правила к полю
  protected function applyRule($field, $rule)
  {
    $params = explode(':', $rule);
    $ruleName = $params[0];

    switch($ruleName) {
      case 'required':
        if(!isset($this->data[$field]) || empty($this->data[$field])) {
          Utils::jsonResponse($this->fieldNames[$field] . ' - обязательно для заполнения', 'error');
        }
        break;
      case 'email':
        if(!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
          Utils::jsonResponse("Проверьте корректность почты", 'error');
        }
        break;
      case 'numeric':
        if(!is_numeric($this->data[$field])) {
          Utils::jsonResponse($this->fieldNames[$field] . ' - должно быть числовым', 'error');
        }
        break;
      case 'not_numeric':
        if(is_numeric($this->data[$field])) {
          Utils::jsonResponse($this->fieldNames[$field] . ' не может быть числом', 'error');
        }
        break;
      case 'regex':
        $pattern = $params[1];
        if(!preg_match($pattern, $this->data[$field])) {
          Utils::jsonResponse($this->fieldNames[$field] . ' не соответствует формату', 'error');
        }
        break;
      case 'min':
        if(strlen($this->data[$field]) < $params[1]) {
          Utils::jsonResponse($this->fieldNames[$field] . " должен содержать минимум {$params[1]}", 'error');
        }
        break;
      case 'max':
        if(strlen($this->data[$field]) > $params[1]) {
          Utils::jsonResponse($this->fieldNames[$field] . " должен содержать максимум {$params[1]}", 'error');
        }
        break;
      case 'unique':
        if(\App\Models\User::findUser($this->data[$field], $params[1])) {
          Utils::jsonResponse("Пользователь с таким данными уже зарегистрирован", 'error');
        }
        break;
    }
  }
}

?>
