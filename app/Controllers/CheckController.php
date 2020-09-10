<?php


namespace App\Controllers;

use Kernel\Controller;

class CheckController extends Controller
{
    protected function get_index()
    {
        return view('home.check');
    }

    protected function post_index()
    {
        $urls = $this->data['post_data']['asghar'];
        $urls = explode("\n", trim($urls));
        $urls = array_map('trim', $urls);

        $result = [];
        foreach ($urls as $url) {
            $result[$url] = $this->get_http_code($url);
        }

        return view('home.check', [
            'akbar' => $result,
            'input' => array_keys($result)
        ]);
    }

    private function get_http_code($url){
        $headers = get_headers($url);
        return $headers[0];//hello
    }
}