<?php


namespace app\models;

use simFW\App;
class Category extends AppModel {

    public function getIds($id) {

        $cats = App::$app->getProperty('cats');
        $ids = null;
        foreach ($cats as $cat_id => $cat) {
            if ($cat['parent_id'] == $id) {

                $ids .= $cat_id . ',';
                $ids .= $this->getIds($cat_id);
            }
        }
        return $ids;
    }
}