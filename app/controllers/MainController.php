<?php

namespace app\controllers;

use simFW\Cache;

class MainController extends AppController {

    public function indexAction () {
        $brands = \R::find('brand', 'LIMIT 3');
        $hits = \R::find('product', "hit = '1' AND status = '1' LIMIT 8");
        $this->setMeta('Luxury Watches', 'Описание...', 'Ключевые слова...');
        $this->set(compact('brands', 'hits'));
    }
}