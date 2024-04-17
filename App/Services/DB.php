<?php
/**
 * BelovTest
 * Db Connection
 */
namespace App\Services;

use App\Services\App;

class DB
{
	public static $safedArray = [];
	private static $query = false;
	public static $connect = false;
	public static $construct = null;

	// Статический метод для инициализации класса
	public static function start($sub = null)
	{
		return self::$construct == null ? self::$construct = new DB($sub) : self::$construct;
	}

	// Приватный конструктор
	private function __construct($sub = null)
	{
		$this->connect($sub !== NULL ? $sub : '');
	}

	// Метод для подключения к базе данных
	private function connect($sub = [])
	{
    self::$connect = $sub && is_array($sub) 
      ? @mysqli_connect($sub['host'], $sub['user'], $sub['pass'], $sub['name'], $sub['port'])
        : @mysqli_connect(App::$cfg['Db']['host'], App::$cfg['Db']['user'], App::$cfg['Db']['pass'], App::$cfg['Db']['name'], App::$cfg['Db']['port']);

		// Проверка успешного подключения
		!self::$connect ? die('Database connection error! Check the data in the file: ' . CONFIGS_DIR . '/Db.php') : '';

		$this->query("SET NAMES 'utf8'");
		return true;
	}

	// Метод для выполнения SQL-запросов
	public static function query($query)
	{
		$query = preg_replace("/{prefix}/", App::$cfg['Db']['prefix'], $query);
		self::$query = @mysqli_query(self::$connect, $query);
		return self::$query;
	}

	// Метод для выполнения SQL-запросов с возвратом ассоциативного массива
	public static function assoc($sql, $bindings = [], $bindings2 = [])
	{
    $sqlCompile = self::compileSql($sql, $bindings, $bindings2);
    self::query($sqlCompile);
		$data = @mysqli_fetch_assoc(self::$query);
		@mysqli_free_result(self::$query);		
		return $data;
	}

	// Метод для выполнения SQL-запросов без возврата данных
	public static function unassoc($sql, $bindings = [], $bindings2 = [])
	{
    $sqlCompile = self::compileSql($sql, $bindings, $bindings2);
		return self::query($sqlCompile);
	}

	// Метод для получения числа строк в результате запроса
	public static function num_rows()
	{
		return @mysqli_num_rows(self::$query);
	}

  // Метод для получения ID последней вставленной строки
	public static function insert_id()
	{
		return @mysqli_insert_id(self::$connect);
	}

	// Метод для получения строки результата запроса
	public static function get_row($query)
	{
		return @mysqli_fetch_assoc($query);
	}

	// Приватный метод для безопасной обработки SQL-запросов
	private static function safesql($source)
	{
		return @mysqli_real_escape_string(self::$connect, $source);
	}

	// Метод для безопасной обработки массива данных
	public static function safedArray($source)
	{
		if(count($source) != 0)
		{
			foreach($source as $key => $value)
			{
				self::$safedArray[$key] = self::safesql($value);
			}
		}
		return self::$safedArray;
	}

	// Метод для закрытия соединения с базой данных
	public static function close()
	{
		@mysqli_close(self::$connect);
		self::$connect = false;
	}

	// Приватный метод для компиляции SQL-запроса с учетом подстановки данных
	private static function compileSql($sql, $bindings = [], $bindings2 = [])
	{
    if(!empty($bindings) && is_array($bindings))
    {
      $sqlCompile = '';
			foreach($bindings as $key => $value)
			{
				$sqlCompile .= " `" . $key . "` = '" . $value . "', ";
			}
      $sqlCompile = str_replace('?u', rtrim($sqlCompile, ', '), $sql);
    } else $sqlCompile = $sql;

    if(!empty($bindings2) && is_array($bindings2))
    {
      $sqlCompile2 = '';
			foreach($bindings2 as $key => $value)
			{
				$sqlCompile2 .= " `" . $key . "` = '" . $value . "' AND ";
			}
      $sqlCompile2 = str_replace('?w', rtrim($sqlCompile2, 'AND '), $sqlCompile);
    } else $sqlCompile2 = $sqlCompile;

    if(empty($bindings) && empty($bindings2)) $sqlCompile2 = $sql;
    
    return $sqlCompile2;
	}
}

?>
