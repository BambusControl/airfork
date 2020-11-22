<?php


namespace App\Controllers;


use App\Core\AControllerBase;

class HomeController extends AControllerBase
{

    public function index()
    {
        if ( !isset($_GET['c']) || !isset($_GET['a']) )
        {
            header('Location: ?c=home&a=index');
        }
        return [
        ];
    }

    public function airplanes()
    {
        return [
        ];
    }

    public function airfields()
    {
        return [
        ];
    }
}