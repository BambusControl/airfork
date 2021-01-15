<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Image;
use App\Models\Post;
use Exception;

class HomeController extends AControllerBase
{

    public function index()
    {
        // Novinky

        // Nacitaj clanky z databazy
        // Spracuj nacitane clanky
        // Vykresli clanky

        return $this->html();
    }

    public function airplanes()
    {
        return $this->html();
    }

    public function airfields()
    {
        return $this->html();
    }

    public function errorpage()
    {
        return $this->html();
    }

    public function news()
    {
        try {
            $posts = Post::getAll();
        } catch (Exception $e) {
            return null;
        }

        $news = [];
        $i = 0;
        foreach ($posts as $post) {
            if ($post->getType() == 'NEWS') {
                $news[$i++] = $post;
            }
        }

        return $this->json($news);
    }

    public function images()
    {
        try {
            return $this->json(Image::getAll());
        } catch (Exception $e) {
            return null;
        }
    }

}