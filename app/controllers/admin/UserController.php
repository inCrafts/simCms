<?php


namespace app\controllers\admin;

use app\models\User;

class UserController extends AppController {

    public function loginAdminAction() {

        $this->layout = 'login';
        if (!empty($_POST)) {
            $user = new User();
            if ($user->login(true)) {
                $_SESSION['success'] = 'ВЫ успешно авторизованы!';
            } else {
                $_SESSION['error'] = 'Ошибка ввода данных!';
            }
            if (User::isAdmin()) {
                redirect(ADMIN);
            } else {
                redirect();
            }
        }
    }

}