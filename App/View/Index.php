<?php
/**
 * BelovTest
 * Render Index Page
 */
namespace App\View;

use App\Services\App;

class Index
{
  // Метод для отрисовки главной страницы 
  public static function render()
  {
    App::$tpl->setTag('posts', '');
    App::$tpl->setTag('pagination', '');
    App::$tpl->setContent('apps/view/index.html');
    App::$tpl->render();
  }
}