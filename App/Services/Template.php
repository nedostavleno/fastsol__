<?php
/**
 * BelovTest
 * Template
 */
namespace App\Services;

use App\Services\App;
use App\Services\Utils;

class Template {

	private $design;
	private $template;
	private $templateDir;
	private $templateTags = [];
	public static $construct = null;

	// Статический метод для инициализации класса
	public static function start()
	{
		return self::$construct == null ? self::$construct = new Template : self::$construct;
	}

	// Приватный конструктор
	private function __construct()
	{
		// Установка пути к файлам дизайна
		$this->design = '/Design/' . App::$cfg['App']['design'];
		$this->templateDir = DESIGN_DIR . '/'. App::$cfg['App']['design'] . '/';

		// Проверка существования основного шаблона
		$this->compileDir = $this->templateDir . 'template.html';
		if(!file_exists($this->compileDir)) die("Error loading template file -> " . $this->compileDir);

		// Загрузка основного шаблона
		$this->template = file_get_contents($this->compileDir);

		// Установка общих тегов шаблона
		$this->setTags([
			'Design' => $this->design,
			'realTime' => time(),
			'title' => App::$cfg['App']['domain'],
			'charset' => App::$cfg['App']['charset'],
			'keywords' => App::$cfg['App']['keywords'],
			'viewport' => App::$cfg['App']['viewport']
		]);
	}

	// Установка тега шаблона
	public function setTag($key, $value)
	{
		$this->templateTags[$key] = $value;
	}

	// Установка нескольких тегов шаблона
	public function setTags($data)
	{
		foreach($data as $key => $value)
		{
			$this->templateTags[$key] = $value;
		}
	}

	// Установка подшаблона в основной контент
	public function setSubContent($template)
	{
		$this->compileDir = $this->templateDir . $template;
		if(!file_exists($this->compileDir)) return "Error loading template file -> " .$this->compileDir;

		$template = file_get_contents($this->compileDir);
		$template = $this->replaceTemplateTags($template);
		return $template;
	}
	
	// Установка контента в основной контент
	public function setContent($template)
	{
		$this->compileDir = $this->templateDir . $template;
		if(!file_exists($this->compileDir)) return "Error loading template file -> " .$this->compileDir;

		$template = file_get_contents($this->compileDir);
		$template = $this->replaceTemplateTags($template);
		return $this->setTag('content', $template);
	}
	
	// Замена тегов шаблона
	private function replaceTemplateTags($template)
	{
		foreach($this->templateTags as $key => $value)
		{
				$template = preg_replace("/{{ $key }}/", $value, $template);
		}
		return $template;
	}

	// Отображение шаблона
	public function render()
	{
		$this->template = $this->replaceTemplateTags($this->template);

		if(!$this->isAjaxRequest())
		{
			// Если это не AJAX-запрос, просто выводим шаблон
			die($this->checkAccess($this->template));
		} else {
			// Если это AJAX-запрос, возвращаем контент в формате JSON
			die(json_encode(['app' => $this->checkAccess($this->templateTags['content'])]));
		}
	}

	// Проверка AJAX-запроса
	private function isAjaxRequest()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	// Проверка доступа к содержимому шаблона
	public function checkAccess($matches)
  {
		$regex = '/\{(not-auth|auth|recaptcha)\}((?>(?R)|.)*?)\{\/\1\}/is';

		if (is_array($matches)) {
			if(!empty($matches[1]) && $matches[1] == 'not-auth') {
				$matches = App::isAuth() === false ?  $matches[2] : '';
			}

			if(!empty($matches[1]) && $matches[1] == 'auth') {
				$matches = App::isAuth() ? $matches[2] : '';
			}

		}
		return preg_replace_callback($regex, array( &$this, 'checkAccess'), $matches);
	}

}
?>
