<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Account;
use Exception;

class AccountController extends AControllerBase
{

    private ?array $account_array;

    public function index()
    {
        if ($this->is_logged_in())
        {
            header('Location: ?c=account&a=profile');
        }
        else
        {
            header('Location: ?c=account&a=login');
        }

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
                // bad login
                $_POST['error_credentials'] = 'Nesprávne prihlasovacie údaje';
                return $this->html($_POST);
            }
        }

        return $this->html();
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
                        $this->redir_err();
                    }
                    exit(0);
                }

                $_POST['disable_password_checkbox'] = true;
                return $this->html($_POST);
            }
        }
        else
        {
            header('Location: ?c=account&a=profile');
            exit(0);
        }

        return $this->html(['disable_password_checkbox' => true]);
    }

    public function profile()
    {
        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];

        try {
            return $this->html(Account::getOne($uid)->as_array());
        } catch (Exception $e) {
            $this->redir_err();
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
                return $this->html($_POST);
            }
            else
            {
                $a = $this->get_account_array();
                $a['password'] = '';
                $a['disable_password_checkbox'] = false;
                return $this->html($a);
            }
        }

        header('Location: ?c=account&a=login');
        exit(0);
    }

    public function delete_account()
    {
        if (isset($_POST['password']))
        {
            session_start(['read_and_close' => true]);
            try {
                $account = Account::getOne($_SESSION['uid']);
            } catch (Exception $e) {
                $this->redir_err();
                exit(0);
            }

            if ( password_verify($_POST['password'], $account->getPassword()) )
            {
                try {
                    $account->delete();
                } catch (Exception $e) {
                    $this->redir_err();
                    exit(0);
                }
                $this->logout();
            }
            else
            {
                return $this->html(['error_password' => 'Nesprávne heslo']);
            }
        }

        return $this->html();
    }

    private function redir_err(): void
    {
        header('Location: ?c=home&a=errorpage');
    }

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
            try {
                return Account::getOne($uid)->as_array();
            } catch (Exception $e) {
                $this->redir_err();
                exit(0);
            }
        }
        return $this->account_array;
    }

    private function create_or_update_account(Account $account) : array
    {
        try {
            $accounts = Account::getAll();
        } catch (Exception $e) {
            $this->redir_err();
            exit(0);
        }
        $errors = [];

        $id_acc = null;
        if ( !is_null($account->getId()) ) {
            // User is already in database
            try {
                $id_acc = Account::getOne($account->getId());
            } catch (Exception $e) {
                $this->redir_err();
                exit(0);
            }
        }

        // Input validation
        foreach ($accounts as $a)
        {
            if (is_null($id_acc)) {
                // User doesn't exist - check validity against all
                if ($a->getUsername() == $account->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }

                if ($a->getEmail() == $account->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }
            } elseif ($a->getId() !== $id_acc->getId()) {
                // User already exists - check validity against all other accounts
                if ($a->getUsername() == $account->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }

                if ($a->getEmail() == $account->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }
            }

        }

        // Error in inputs  TODO better input checking also check all input fields
        if (!empty($errors))
        {
            return $errors;
        }

        // Inputs are ok
        try {
            $account->save();
        } catch (Exception $e) {
            $this->redir_err();
            exit(0);
        }

        return [];
    }

    private function account_login(string $username, string $password): bool
    {
        try {
            $accounts = Account::getAll();
        } catch (Exception $e) {
            $this->redir_err();
            exit(0);
        }

        foreach ($accounts as $a)
        {
            if ($a->getUsername() === $username)
            {
                if (password_verify($password, $a->getPassword()))
                {
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

}