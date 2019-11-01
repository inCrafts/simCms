<?php


namespace app\controllers;


class SearchController extends AppController {

    public function seekAction()  {
        if ($this->isAjax()) {
            $query = !empty(trim($_GET['query'])) ? trim($_GET['query']) : null;
            if ($query) {
                $products = \R::getAll('SELECT id, title FROM product WHERE title LIKE ? LIMIT  11', ["%{$query}%"]);
                echo json_encode($products);
            }
        }
        die;
    }

    public function indexAction() {

        $search = !empty(trim($_GET['search'])) ? trim($_GET['search']) : null;
        if ($search) {
            $products = \R::find('product', 'title LIKE ?', ["%{$search}%"]);
        }
        $this->setMeta('Поиск по: ' . noChars($search));
        $this->set(compact('products', 'search'));
    }
}