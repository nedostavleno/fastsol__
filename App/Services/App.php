<?php
/**
 * BelovTest
 * App
 */
namespace App\Services;

class App
{
	// Переменные для работы с шаблонами, конфигурацией, данными запросов и информацией о пользователе
	public static $tpl;
	public static $cfg;
	private static $get;
	private static $post;
	private static $cookie;
	private static $server;

	// Информация о текущем пользователе и состоянии авторизации
	public static $info = [ 'user' => [], 'auth' => false ];
	public static $construct = null;

	// Инициализация приложения
	public static function start()
	{
		return self::$construct == null ? self::$construct = new App() : self::$construct;
	}

	// Приватный конструктор для инициализации компонентов приложения
	private function __construct()
	{
		// Загрузка конфигурации для базы данных и приложения
		$this->config('Db');
		$this->config('App');
    
		// Запуск подключения к базе данных
		DB::start();

		// Безопасное получение данных GET, POST, COOKIE и SERVER
		self::$get = DB::safedArray($_GET);
		self::$post = DB::safedArray($_POST);
		self::$cookie = DB::safedArray($_COOKIE);
		self::$server = DB::safedArray($_SERVER);

		// Инициализация шаблонизатора
		self::$tpl = Template::start();
    
		// Если установлены куки с userId и userToken, автоматически выполняется вход пользователя
		if(!empty(self::$cookie['userId']) && !empty(self::$cookie['userToken'])) {
			self::nowLoginUser(self::$cookie['userId'], self::$cookie['userToken']);
		}
	}

	// Получение данных GET
	public static function getData() {
		return self::$get;
	}

	// Получение данных POST
	public static function postData() {
		return self::$post;
	}

	// Получение данных COOKIE
	public static function cookieData() {
		return self::$cookie;
	}

	// Получение данных SERVER
	public static function serverData() {
		return self::$server;
	}

	// Проверка, авторизован ли пользователь
	public static function isAuth()
	{
		return self::$info['auth']; 
	}

	// Загрузка конфигурационных файлов
	private function config($cfg)
	{
		if(file_exists(CONFIGS_DIR . '/'. $cfg . '.php'))
		{
			return self::$cfg[$cfg] = require_once CONFIGS_DIR . '/'. $cfg . '.php';
		} else die('Error file: App/Configs/' . $cfg . '.php');
	}

	// Автоматический вход пользователя
	public static function nowLoginUser($userId, $userToken)
	{
		if($userId && $userToken)
		{
      self::$info['user'] = DB::assoc("SELECT * FROM `{prefix}_users` WHERE ?w", '', [
        'id' => $userId,
        'token' => $userToken
      ]);

			self::$info['user']['id'] && $userToken == self::$info['user']['token']
				? self::$info['auth'] = true
					: self::$info['auth'] = false;
		}

		!self::$info['auth'] ? self::nowLayoutUser() : '';

	}

	// Выход пользователя из аккаунта
	public static function nowLayoutUser()
	{
		self::$info = [ 'user' => [], 'auth' => false ];
		Utils::setCookie('userId', '', 0);
		Utils::setCookie('userToken', '', 0);
	}
}
