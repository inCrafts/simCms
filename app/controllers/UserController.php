<?php


namespace app\controllers;


use app\models\User;

class UserController extends AppController {

    public function signupAction() {

        if (!empty($_POST)) {
            $user = new User();
            $data = $_POST;
            $user->load($data);
            if (!$user->validate($data) || !$user->checkUnique()) {
                $user->getErrors();
                $_SESSION['formData'] = $data;
            } else {
                $user->attributes['password'] = password_hash($user->attributes['password'], PASSWORD_DEFAULT);
                if ($user->save('user')) {
                    foreach ($user->attributes as $attribute => $val) {
                        $attribute !== 'password' ? $_SESSION['user'][$attribute] = $val : $_SESSION['user'][$attribute] = '';
                    }
                    $_SESSION['success'] = 'Пользователь успешно зарегистрирован!';
                } else {
                    $_SESSION['error'] = 'Ошиибка регистрации.';
                }
            }
            redirect();
        }
        $this->setMeta('Регистрация');
    }

    public function loginAction() {

        if (!empty($_POST)) {
            $user = new User();
            if ($user->login()) {
                $_SESSION['success'] = 'Вы успешно авторизованы!';
            } else {
                $_SESSION['error'] = 'Логин или пароль введены неверно.';
            }
            redirect();
        }
        $this->setMeta('Вход');
    }

    public function logoutAction() {

        if (isset($_SESSION['user'])) unset( $_SESSION['user']);
        redirect();
    }
}