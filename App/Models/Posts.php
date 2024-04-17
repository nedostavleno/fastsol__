<?php
namespace App\Models;

use App\Services\App;
use App\Services\DB;

class Posts
{
  // Метод для получения общего числа записей
  public static function getTotalPosts()
  {
    $posts = DB::assoc("SELECT COUNT(*) AS total FROM `{prefix}_posts`");
    return $posts['total'];
  }

  // Метод для получения списка записей с пагинацией
  public static function getPosts($page, $limit)
  {
    // Расчет смещения для текущей страницы
    $offset = ($page - 1) * $limit;

    $posts = DB::unassoc("SELECT * FROM `{prefix}_posts` LIMIT $limit OFFSET $offset");
    
    // Формирование массива результатов
    $result = [];
    foreach($posts as $post) {
      $result[] = $post;
    }
    return $result;
  }
}
?>