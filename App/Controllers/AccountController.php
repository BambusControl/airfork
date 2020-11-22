<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Account;

class AccountController extends AControllerBase
{

    public function index()
    {
        header('Location: ?c=account&a=login');
        exit(0);
    }

    public function login()
    {
        return [];
    }

    public function register()
    {
        if ( isset($_POST['username']) )
        {
            $account = new Account(
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                $_POST['firstname'],
                $_POST['lastname'],
                $_POST['date_of_birth'],
                $_POST['gender']
            );


            $errors = [];
            $accounts = Account::getAll();
            foreach ($accounts as $a)
            {
                if ($a->getUsername() == $account->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }
                if ($a->getEmail() == $account->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }

                if (!empty($errors)) {
                    break;
                }

            }

            $_POST['error'] = $errors;

            if (empty($errors))
            {
                $account->save();
            }

            return $_POST;
        }

        return [];
    }


}