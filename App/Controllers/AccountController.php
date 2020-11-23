<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Account;

class AccountController extends AControllerBase
{

    private $account_array;

    private function is_logged_in() : bool
    {
        session_start(['read_and_close' => true]);
        return @$_SESSION['logged_in'] === true;
    }

    private function get_account_array()
    {
        if (empty($this->account_array))
        {
            session_start(['read_and_close' => true]);
            $uid = $_SESSION['uid'];
            return Account::getOne($uid)->as_array();
        }
        return $this->account_array;
    }

    private function create_or_update_account(Account $account) : array
    {
        try {
            $accounts = Account::getAll();
        } catch (\Exception $e) {
            header('Location: ?c=home&a=errorpage');
            exit(0);
        }
        $errors = [];

        if ( !is_null($account->getId()) ) {
            try {
                $id_acc = Account::getOne($account->getId());
            } catch (\Exception $e) {
                header('Location: ?c=home&a=errorpage');
                exit(0);
            }
        }

        foreach ($accounts as $a)
        {
            if (is_null($id_acc))
            {
                if ($a->getUsername() == $account->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }

                if ($a->getEmail() == $account->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }
            }
            elseif ($a->getId() !== $id_acc->getId())
            {
                if ($a->getUsername() == $account->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }

                if ($a->getEmail() == $account->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }
            }


            // Error in inputs
            if (!empty($errors))
            {
                return $errors;
            }
        }

        // Inputs are ok
        try {
            $account->save();
        } catch (\Exception $e) {
            header('Location: ?c=home&a=errorpage');
            exit(0);
        }
        return [];
    }

    private function account_login(string $username, string $password): bool
    {
        try {
            $accounts = Account::getAll();
        } catch (\Exception $e) {
            header('Location: ?c=home&a=errorpage');
            exit(0);
        }

        foreach ($accounts as $a) {
            if ($a->getUsername() === $username) {
                if (password_verify($password, $a->getPassword())) {
                    //if (password_needs_rehash($a->getPassword(), PASSWORD_DEFAULT))
                    //{
                    //todo rehash
                    //}

                    // Session creation
                    session_start();

                    $_SESSION['logged_in'] = true;
                    $_SESSION['uid'] = $a->getId();
                    $_SESSION['username'] = $a->getUsername();

                    session_commit();

                    return true;
                }
            }
        }

        // Wrong username or password
        return false;
    }

    public function index()
    {
        header('Location: ?c=account&a=login');
        exit(0);
    }

    public function login()
    {
        if ($this->is_logged_in())
        {
            header('Location: ?c=account&a=profile');
            exit(0);
        }

        if ( isset($_POST['username']) )
        {
            if ( $this->account_login($_POST['username'], $_POST['password']) )
            {
                header('Location: ?c=account&a=profile');
                exit(0);
            }
            else
            {
                return $_POST;
            }
        }

        return [];
    }

    public function register()
    {
        if (!$this->is_logged_in())
        {
            if ( isset($_POST['username']) )
            {
                $account = new Account(
                    null,
                    $_POST['username'],
                    $_POST['email'],
                    password_hash($_POST['password'],PASSWORD_DEFAULT),
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['date_of_birth'],
                    $_POST['gender']
                );
                $_POST['error'] = $this->create_or_update_account($account);

                if ( empty($_POST['error']) )
                {
                    if ($this->account_login($_POST['username'], $_POST['password']))
                    {
                        header('Location: ?c=account&a=profile');
                    }
                    else
                    {
                        header('Location: ?c=home&a=errorpage');
                    }
                    exit(0);
                }

                $_POST['disable_password_checkbox'] = true;
                return $_POST;
            }
        }
        else
        {
            header('Location: ?c=account&a=profile');
            exit(0);
        }

        return ['disable_password_checkbox' => true];
    }

    public function profile()
    {
        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];
        try {
            return Account::getOne($uid)->as_array();
        } catch (\Exception $e) {
            header('Location: ?c=home&a=errorpage');
            exit(0);
        }
    }

    public function logout()
    {
        session_start();
        $_SESSION = array();
        session_destroy();
        $this->account_array = null;

        header('Location: ?c=account&a=login');
        exit(0);
    }

    public function edit_profile()
    {
        if ($this->is_logged_in())
        {
            if (isset($_POST['username']))
            {
                $account = new Account(
                    $_SESSION['uid'],
                    $_POST['username'],
                    $_POST['email'],
                    isset($_POST['password-checkbox']) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : $this->get_account_array()['password'],
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['date_of_birth'],
                    $_POST['gender']
                );

                $_POST['error'] = $this->create_or_update_account($account);
                if (empty($_POST['error']))
                {
                    session_start();
                    $_SESSION['username'] = $_POST['username'];
                    session_commit();
                }
                $_POST['password'] = '';
                $_POST['disable_password_checkbox'] = false;
                return $_POST;
            }
            else
            {
                $a = $this->get_account_array();
                $a['password'] = '';
                $a['disable_password_checkbox'] = false;
                return $a;
            }
        }

        header('Location: ?c=account&a=login');
        exit(0);
    }


}