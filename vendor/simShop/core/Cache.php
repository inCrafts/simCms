<?php


namespace simFW;


class Cache {

    use TSingletone;

    public function set($key, $data, $time = 3600) {
        if ($time) {
            $content['data'] = $data;
            $content['expire'] = time() + $time;
            if (file_put_contents(CACHE . '/' . md5($key) . '.txt', serialize($content))) {
                return true;
            }
        }
        return false;
    }

    public function get($key) {

        $file = CACHE . '/' . md5($key). '.txt';
        if (file_exists($file)) {
            $content = unserialize(file_get_contents($file));
            if (time() <= $content['expire']) {
                return $content['data'];
            } else {

            }
            unlink($file);
        }
        return false;
    }

    public function delete($key) {
        $file = CACHE . '/' . md5($key). '.txt';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}