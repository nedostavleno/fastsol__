<?php
/**
 * BelovTest
 * Render Restore Page
 */
namespace App\View;

use App\Services\App;

class Restore
{
  // Метод для отрисовки страницы с восстановлением пароля
  public static function render()
  {
    App::$tpl->setContent('apps/view/restore.html');
    App::$tpl->render();
  }
}