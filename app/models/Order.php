<?php


namespace app\models;
use simFW\App;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;


class Order extends AppModel {

    public static function saveOrder($data) {

        $order = \R::dispense('order');
        $order->user_id = $data['user_id'];
        $order->note = $data['note'];
        $order->currency = $_SESSION['cart.currency']['code'];
        $order_id = \R::store($order);
        self::saveOrderProduct($order_id);
        return $order_id;
    }

    public static function saveOrderProduct($order_id) {

        $sql_part = '';
        foreach ($_SESSION['cart'] as $product_id => $product) {

            $product_id = (int)$product_id;
            $sql_part .= "($order_id, $product_id, {$product['qty']}, '{$product['title']}', {$product['price']}),";
        }
        $sql_part = rtrim($sql_part, ',');
        \R::exec("INSERT INTO order_product (order_id, product_id, qty, title, price) VALUES $sql_part");
    }

    public static function mailOrder($order_id, $user_email) {

        // Transport
        $transport = (new \Swift_SmtpTransport(App::$app->getProperty('smtp_host'), App::$app->getProperty('smtp_port'), App::$app->getProperty('smtp_protocol')))
            ->setUsername(App::$app->getProperty('smtp_login'))
            ->setPassword(App::$app->getProperty('smtp_password'));

        // Mailer
        $mailer = new \Swift_Mailer($transport);

        // Message
        ob_start();
        require_once APP . '/views/Mail/orderMail.php';
        $mail = ob_get_clean();

        $messageToUser = (new \Swift_Message("Ваш заказ в машазине " . App::$app->getProperty('shop_name')))
        ->setFrom([App::$app->getProperty('smtp_login') => App::$app->getProperty('shop_name')])
            ->setTo($user_email)
            ->setBody($mail, 'text/html');

        $messageToManager = (new \Swift_Message("Сделан Заказ №{$order_id}"))
            ->setFrom([App::$app->getProperty('smtp_login') => App::$app->getProperty('shop_name')])
            ->setTo(App::$app->getProperty('admin_email'))
            ->setBody($mail, 'text/html');

        // sending message
        $res = $mailer->send($messageToManager);
        $res = $mailer->send($messageToUser);
        unset($_SESSION['cart']);
        unset($_SESSION['cart.qty']);
        unset($_SESSION['cart.sum']);
        unset($_SESSION['cart.currency']);
        $_SESSION['success'] = 'Благодарим за заказ! В течениии 10 минут Вам перезвонит наш менеджер';
    }
}