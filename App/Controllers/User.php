<?php
namespace App\Controllers;

use App\Services\App;
use App\Services\DB;
use App\Services\Utils;
use App\Services\Validator;
use App\Models\User as UserModel;

class User
{
  private $rules;

  // Метод для валидации данных согласно указанным правилам
  private function validate(array $rules)
  {
    $validator = new Validator(App::postData(), $rules);
    $validator->validate();
  }

  // Метод для обработки запроса на авторизацию пользователя
  public function login()
  {
    // Правила валидации
    $this->rules = [
      'login' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
      'password' => 'required|regex:/^[a-zA-Z0-9_]+$/i'
    ];

    $this->validate($this->rules);

    // Проверка, авторизован ли уже пользователь
    if(App::isAuth()) {
      Utils::jsonResponse('Перенаправление..', 'success', [ 'uri' => '/', 'response' => 'location' ]);
    }

    // Поиск пользователя по логину
    $this->user = UserModel::findByLogin(App::postData()['login']);

    // Проверка корректности логина и пароля
    if(!$this->user['name'] || App::postData()['password'] !== Utils::decrypt($this->user['name'], $this->user['password']))
    {
      Utils::jsonResponse('Введён некорректный логин и/или пароль', 'error', ['response' => 'alert']);
    }

    // Генерация нового токена для пользователя
    $this->token = Utils::genToken();

    // Обновление токена в базе данных
    DB::unassoc("UPDATE `{prefix}_users` SET ?u WHERE ?w", [
      'token' => $this->token
    ], [
      'id' => $this->user['id']
    ]);

    // Установка куки
    Utils::setCookie('userId', $this->user['id'], 1);
    Utils::setCookie('userToken', $this->token, 1);

    // Авторизация пользователя в приложении
    App::nowLoginUser($this->user['id'], $this->token);

    // Запись действия
    Utils::setLogs('Авторизация в аккаунт');

    // Возвращение успешного результата
    Utils::jsonResponse('Вы успешно авторизовались!', 'success', [ 'uri' => '/', 'response' => 'location' ]);
  }

  // Метод для выхода из аккаунта пользователя
  public function layout()
  {
    // Запись действия
    Utils::setLogs('Выход из аккаунта');

    // Очистка данных пользователя
    App::nowLayoutUser();

    // Возвращение успешного результата
    Utils::jsonResponse(null, 'success', [ 'uri' => '/', 'response' => 'location' ]);
  }

  // Метод для восстановления пароля пользователя
  public function restore()
  {
    // Валидация данных
    $this->validate(['login' => 'required|regex:/^[a-zA-Z0-9_]+$/i']);

    // Проверка, авторизован ли уже пользователь
    if(App::isAuth()) {
      Utils::jsonResponse('Перенаправление..', 'success', [ 'uri' => '/', 'response' => 'location' ]);
    }

    // Поиск пользователя по логину
    $this->user = UserModel::findByLogin(App::postData()['login']);

    // Проверка наличия пользователя с указанным логином
    if(!$this->user['name'])
    {
      Utils::jsonResponse('Пользователь не найден!', 'error', ['response' => 'alert']);
    }

    // Отправка пароля пользователю
    Utils::jsonResponse('Ваш пароль: ' . Utils::decrypt($this->user['name'], $this->user['password']), 'warning', ['response' => 'alert']);
  }

  // Метод для регистрации нового пользователя
  public function register()
  {
    // Правила валидации
    $this->rules = [
      'login' => 'required|min:4|max:16|not_numeric|regex:/^[a-zA-Z0-9_]+$/i|unique:name',
      'password' => 'required|min:6|max:24|not_numeric|regex:/^[a-zA-Z0-9_]+$/i'
    ];

    $this->validate($this->rules);

    // Проверка, авторизован ли уже пользователь
    if(App::isAuth()) {
      Utils::jsonResponse('Перенаправление..', 'success', [ 'uri' => '/', 'response' => 'location' ]);
    }

    // Генерация нового токена для пользователя
    $this->token = Utils::genToken();

    // Создание нового пользователя
    $this->user = UserModel::create(App::postData()['login'], App::postData()['password'], $this->token);

    // Установка куки
    Utils::setCookie('userId', $this->user, 1);
    Utils::setCookie('userToken', $this->token, 1);

    // Авторизация пользователя в приложении
    App::nowLoginUser($this->user, $this->token);

    // Запись действия
    Utils::setLogs('Регистрация аккаунта');

    // Возвращение успешного результата
    Utils::jsonResponse('Вы успешно зарегистрировались!', 'success', [ 'uri' => '/', 'response' => 'location' ]);
  }
}
?>
