<?php
/**
 * BelovTest
 * Render Register Page
 */
namespace App\View;

use App\Services\App;

class Register
{
  // Метод для отрисовки страницы с регистрации
  public static function render()
  {
    App::$tpl->setContent('apps/view/register.html');
    App::$tpl->render();
  }

}