<?php


namespace App\Controllers;


use App\Core\AControllerBase;

class HomeController extends AControllerBase
{

    public function index()
    {
        // Novinky
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

    public static function redirError ()
    {
        header('Location: ?c=home&a=errorpage');
    }

}