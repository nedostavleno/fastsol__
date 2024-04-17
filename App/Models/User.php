<?php
namespace App\Models;

use App\Services\App;
use App\Services\DB;
use App\Services\Utils;

class User
{
  // Метод для получения IP-адреса пользователя
  public static function getIp()
  {
    // Проверка и использование IP-адреса из различных источников
    if($_SERVER['HTTP_CF_CONNECTING_IP']) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if($_SERVER['HTTP_X_REAL_IP']) return $_SERVER['HTTP_X_REAL_IP'];
    if($_SERVER['REMOTE_ADDR']) return $_SERVER['REMOTE_ADDR'];

    return $_SERVER['REMOTE_ADDR'];
  }

  // Метод для поиска пользователя по указанному полю и значению
  public static function findUser($like, $where)
  {
    return DB::assoc("SELECT * FROM `{prefix}_users` WHERE ?w", '', [
      $where => $like
    ]);
  }

  // Метод для поиска пользователя по логину
  public static function findByLogin($login)
  {
    return DB::assoc("SELECT * FROM `{prefix}_users` WHERE ?w", '', [
      'name' => md5($login)
    ]);
  }

  // Метод для создания нового пользователя
  public static function create($login, $password, $token)
  {
    // Вставка новой записи о пользователе в базу данных
    DB::unassoc("INSERT INTO `{prefix}_users` SET ?u", [
      'name' => md5($login),
      'password' => Utils::crypt(md5($login), $password),
      'token' => $token,
      'ip' => self::getIp(),
      'date' => time()
    ]);

    // Возвращение идентификатора вставленной записи
    return DB::insert_id();
  }
}

?>
