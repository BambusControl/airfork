<?php


namespace App\Controllers;


use App\Core\AControllerBase;

class AccountController extends AControllerBase
{

    public function index()
    {
        header('Location: ?c=account&a=login');
        exit(0);
    }

    public function login()
    {
        return [
            'page' => __FUNCTION__
        ];
    }

    public function register()
    {
        return [
            'page' => __FUNCTION__
        ];
    }


}