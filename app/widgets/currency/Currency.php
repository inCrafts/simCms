<?php


namespace app\widgets\currency;


use simFW\App;

class Currency {

    protected $tpl;
    protected $currency;
    protected $currencies;

    public function __construct() {

        echo $this->tpl = __DIR__ . '/currency_tpl/currency.php';
        $this->run();
    }

    public function run() {
        $this->currencies = App::$app->getProperty('currencies');
        $this->currency = App::$app->getProperty('currency');
        echo $this->getHtml();

    }

    public static function getCurrency($currencies) {

        if (isset($_COOKIE['currency']) && array_key_exists($_COOKIE['currency'], $currencies)) {
            $key = $_COOKIE['currency'];
        } else {
            $key = key($currencies);
        }
        $currency = $currencies[$key];
        $currency['code'] = $key;
        return $currency;
    }

    public static function getCurrencies() {

        return \R::getAssoc("SELECT code, title, symbol_left, symbol_right, value, base FROM currency ORDER BY base DESC");
    }

    protected function getHtml() {
        ob_start();
        require_once $this->tpl;
        return ob_get_clean();

    }

}