<?php
/**
 * BelovTest
 * Render Posts Page
 */
namespace App\View;

use App\Services\App;

class Posts
{
  private $pageId;

  // Конструктор класса, принимающий параметр pageid
  public function __construct($param)
  {
    $this->pageId = $param['pageid'];
  }

  // Метод для отрисовки страницы с постами
  public function render()
  {
    // Определение текущей страницы
    $currentPage = (!empty($this->pageId) ? $this->pageId : 1);

    // Вычисление общего количества страниц
    $totalPages = ceil(\App\Models\Posts::getTotalPosts() / App::$cfg['App']['limit_posts']);

    // Получение списка постов для текущей страницы
    $posts = \App\Models\Posts::getPosts($currentPage, App::$cfg['App']['limit_posts']);

    // Формирование пагинации
    $pagination = '';
    for ($i = 1; $i <= $totalPages; $i++) {
      $active = ($i == $currentPage) ? "active" : "";
      $pagination .= "<a href='/posts/$i' class='btn btn-sm btn-light m-1 $active'>$i</a>";
    }

    // Формирование контента страницы с постами
    $content = '';
    foreach ($posts as $post) {
      App::$tpl->setTag('ptitle', $post['title']);
      App::$tpl->setTag('description', $post['description']);
      App::$tpl->setTag('author', $post['author']);
      $content .= App::$tpl->setSubContent('apps/view/posts.html');
    }

    // Установка тегов и контента для главного шаблона
    App::$tpl->setTag('posts', $content);
    App::$tpl->setTag('pagination', ($content ? $pagination : ''));

    // Установка главного шаблона и его отрисовка
    App::$tpl->setContent('apps/view/index.html');
    App::$tpl->render();
  }
}
