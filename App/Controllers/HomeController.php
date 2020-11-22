<?php


namespace App\Controllers;


use App\Core\AControllerBase;

class HomeController extends AControllerBase
{

    public function index()
    {
        if ( !isset($_GET['c']) )
        {
            header('Location: ?c=home&a=index');
        }
        return [
            'page' => __FUNCTION__
        ];
    }

    public function airplanes()
    {
        return [
            'page' => __FUNCTION__
        ];
    }

    public function airfields()
    {
        return [
            'page' => __FUNCTION__
        ];
    }
}