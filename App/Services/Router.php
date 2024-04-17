<?php
/**
 * BelovTest
 * Router
 */
namespace App\Services;

class Router
{
	// Массив маршрутов
	private static $routes = [];
	// Параметры маршрута
	private static $routeParams;

	// Получение метода HTTP-запроса
	public static function getMethod()
	{
		return strtolower(App::serverData()['REQUEST_METHOD']);
	}

	// Получение URL-адреса запроса
	public static function getUrl()
	{
		$path = App::serverData()['REQUEST_URI'];
		$position = strpos($path, '?');

		$path = $position !== false ? substr($path, 0, $position) : $path;

		return $path;
	}

	// Добавление GET-маршрута
	public static function get(string $url, $callback)
	{
		self::$routes['get'][$url] = $callback;
	}

	// Добавление POST-маршрута
	public static function post(string $url, $callback)
	{
		self::$routes['post'][$url] = $callback;
	}

	// Получение маршрутов для заданного метода
	private static function getRoute($method): array
	{
		return self::$routes[$method] ?? [];
	}

	// Установка параметров маршрута
	private static function setRouteParams($params)
	{
		return self::$routeParams = $params;
	}

	// Получение параметров маршрута
	public static function getRouteParams()
	{
		return self::$routeParams;
	}

	// Получение значения параметра маршрута или значения по умолчанию
	public static function getRouteParam($param, $default = null)
	{
		return self::$routeParams[$param] ?? $default;
	}

	// Получение обратного вызова для маршрута
	private static function getCallback()
	{
		$url = trim(self::getUrl(), '/');
		$routes = self::getRoute(self::getMethod());
		$routeParams = false;

		foreach($routes as $route => $callback)
		{
			$route = trim($route, '/');
			$routeNames = [];

			if(!$route) continue;

			// Поиск именованных параметров маршрута
			if(preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches))
			{
				$routeNames = $matches[1];
			}

			// Генерация регулярного выражения для сравнения URL с маршрутом
			$routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

			// Поиск соответствия между URL и маршрутом
			if(preg_match_all($routeRegex, $url, $valueMatches))
			{
				$values = [];
				for ($i = 1; $i < count($valueMatches); $i++) {
					$values[] = $valueMatches[$i][0];
				}
				$routeParams = array_combine($routeNames, $values);

				self::setRouteParams($routeParams);

				return $callback;
			}
		}
		return false;
	}

	// Запуск маршрутизации
	public static function start()
	{
		$callback = self::$routes[self::getMethod()][self::getUrl()] ?? false;

		if(!$callback)
		{
			$callback = self::getCallback();
			if(!$callback) {
        // Отображение страницы 404, если маршрут не найден
        App::$tpl->setContent('404.html');
        App::$tpl->render();
			}
		}

		if(is_array($callback)) {
			$class = new $callback[0](self::$routeParams ? self::$routeParams : '');
			$method = $callback[1];
			$class->$method();
			die;
		}
    
    // Отображение страницы 404, если маршрут не найден
    App::$tpl->setContent('404.html');
    App::$tpl->render();
		
	}
}
