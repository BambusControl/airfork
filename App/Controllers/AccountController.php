<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Account;
use App\Models\Post;
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
                    isset($_POST['gender']) ? $_POST['gender'] : ''
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
        if (!self::is_logged_in()) {
            $this->redirLogin();
            exit(0);
        }

        if (isset($_GET['uid'])) {
            $uid = $_GET['uid'];
        } else {
            session_start(['read_and_close' => true]);
            $uid = $_SESSION['uid'];
        }



        try {
            $account = Account::getOne($uid);
        } catch (Exception $e) {
            $this->redir_err();
            exit(0);
        }

        return $this->html($account->as_array());
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
            $uid = $_SESSION['uid'];
            try {
                $account = Account::getOne($uid);
            } catch (Exception $e) {
                $this->redir_err();
                exit(0);
            }

            if ( password_verify($_POST['password'], $account->getPassword()) )
            {
                // Get user posts
                try {
                    $posts = Post::getAll('author='. $uid);
                } catch (Exception $e) {
                    $this->redir_err();
                    exit(0);
                }

                // Delete all user posts - images
                if (count($posts) != 0) {
                    foreach ($posts as $post) {
                        try {
                            $post->delete();
                        } catch (Exception $e) {
                            $this->redir_err();
                            exit(0);
                        }
                    }
                }

                // Delete account
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
                // Bad password
                return $this->html(['error_password' => 'Nesprávne heslo']);
            }
        }

        return $this->html();
    }

    private function redir_err(): void // TODO from home
    {
        header('Location: ?c=home&a=errorpage');
    }

    public static function is_logged_in() : bool
    {
        session_start(['read_and_close' => true]);
        return @$_SESSION['logged_in'] === true;
    }

    public function logged_in()
    {
        session_start(['read_and_close' => true]);
        if (!isset($_SESSION['logged_in'])) {
            return $this->json(['logged_in' => false]);
        }
        return $this->json(@$_SESSION);
    }

    private function get_uid()
    {
        if ($this->is_logged_in()) {
            return @$_SESSION['uid'];
        }
        return null;
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
        $errors = [];

        // Input validation
        if(!preg_match('~([A-Z]).+~', $account->getFirstname())) {
            $errors['firstname'] = 'Meno musí začínať veľkým písmenom';
        }

        if(!preg_match('~([A-Z]).+~', $account->getLastname())) {
            $errors['lastname'] = 'Priezvisko musí začínať veľkým písmenom';
        }

        $s = strtotime('+16 years', strtotime($account->getDateOfBirth()));
        $n = time();
        if($s > $n) {
            $errors['date_of_birth'] = 'Pre registráciu musíte mať aspoň 16 rokov!';
        }

        if(empty($account->getGender())) {
            $errors['gender'] = 'Vyberte si pohlavie!';
        }

        if(!preg_match('~^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])[^\n\r]{7,255}$~', $account->getPassword())) {
            $errors['password'] = 'Heslo musí obsahovať aspoň 7 znakov, z toho aspoň jedno veľké písmeno, malé písmeno a číslo';
        }

        // Input validation against database
        try {
            $accounts = Account::getAll();
        } catch (Exception $e) {
            $this->redir_err();
            exit(0);
        }

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

        foreach ($accounts as $a)
        {
            $x = function ($account1, $account2, &$errors) {
                if($account1->getUsername() == $account2->getUsername())
                {
                    $errors['username'] = 'Tento username bol už použitý';
                }

                if($account1->getEmail() == $account2->getEmail())
                {
                    $errors['email'] = 'Tento email bol už registrovaný';
                }
            };
            if (is_null($id_acc)) {
                // User doesn't exist - check validity against all
                $x($a, $account, $errors);
            } elseif ($a->getId() !== $id_acc->getId()) {
                // User already exists - check validity against all other accounts
                $x($a, $account,$errors);
            }

        }

        // Error in inputs
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
                    $_SESSION['is_admin'] = $a->isAdmin() == 1;

                    session_commit();

                    return true;
                }
            }
        }

        // Wrong username or password
        return false;
    }

    private function redirLogin(): void
    {
        header('Location: ?c=account&a=login');
    }

    public function get_user()
    {
        if (!isset($_GET['uid'])) {
            return $this->json(null);
        }

        $uid = @$_GET['uid'];

        try {
            $user = Account::getOne($uid);
            return $this->json($user);
        } catch (Exception $e) {
            $this->json($e);
        }
    }

}