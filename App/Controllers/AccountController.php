<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Account;
use App\Models\Image;
use App\Models\Post;
use App\Models\Vote;
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
            header('Location: ?c=account&a=login&rc=account&ra=profile');
        }

        exit(0);
    }

    public function login()
    {
        if ($this->is_logged_in())
        {
            $req = $req = 'Location: ';
            if (isset($_GET['rc'])) {
                $req .= '?c=' . $_GET['rc'] . '&a=' . $_GET['ra'];
            } else {
                $req .= '?c=account&a=profile';
            }
            header($req);
            exit(0);
        }

        if ( isset($_POST['username']) )
        {
            if ( $this->account_login($_POST['username'], $_POST['password']) )
            {
                $req = $req = 'Location: ';
                if (isset($_GET['rc'])) {
                    $req .= '?c=' . $_GET['rc'] . '&a=' . $_GET['ra'];
                } else {
                    $req .= '?c=account&a=profile';
                }
                header($req);
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
                        HomeController::redirError();
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
            header('Location: ?c=account&a=login&rc=account&ra=profile');
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
            HomeController::redirError();
            exit(0);
        }

        return $this->html($account->as_array());
    }

    public function logout()
    {
        // Check if a user is logged in
        if (!AccountController::is_logged_in()) {
            exit(0);
        }

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

        header('Location: ?c=account&a=login&rc=account&ra=edit_profile');
        exit(0);
    }

    public function delete_account()
    {
        // Check if a user is logged in
        if (!AccountController::is_logged_in()) {
            header('Location: ?c=account&a=login&rc=account&ra=edit_profile');
            exit(0);
        }

        if (isset($_POST['password']))
        {
            session_start(['read_and_close' => true]);
            $uid = $_SESSION['uid'];
            try {
                $account = Account::getOne($uid);
            } catch (Exception $e) {
                HomeController::redirError();
                exit(0);
            }

            if ( password_verify($_POST['password'], $account->getPassword()) )
            {
                // Get user posts
                try {
                    $posts = Post::getAll('author='. $uid);
                } catch (Exception $e) {
                    HomeController::redirError();
                    exit(0);
                }

                // Delete all user posts - images
                if (count($posts) != 0) {
                    foreach ($posts as $post) {
                        try {
                            $post->delete();
                        } catch (Exception $e) {
                            HomeController::redirError();
                            exit(0);
                        }
                    }
                }

                // Delete account
                try {
                    $account->delete();
                } catch (Exception $e) {
                    HomeController::redirError();
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

    public function add_post()
    {
        // Check if a user is logged in
        if (!AccountController::is_logged_in()) {
            header('Location: ?c=account&a=login&rc=account&ra=add_post');
            exit(0);
        }

        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];

        // Check if user submitted a form
        if (!isset($_POST['submit'])) {
            return $this->html();
        }

        // Inputs
        $title = $_POST['title'];
        $text = $_POST['text'];
        $isArticle = isset($_POST['article_switch']);

        // Check all inputs
        $errors = [];

        if (empty($title)) {
            $errors['title'] = "Title cannot be empty";
        }

        if (empty($text)) {
            $errors['text'] = "Text field cannot be empty";
        }

        // If any errors occurred
        if (count($errors) !== 0) {
            $_POST['error'] = $errors;
            return $this->html($_POST);
        }

        $imageId = null;
        if ( isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {

            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $filename = $_FILES['image']['name'];
                $nameArr = explode(".", $filename);
                $extension = strtolower(end($nameArr));

                // Check if file is an image
                if(getimagesize($tmpPath) === false) {
                    $errors['image'] = 'File is not an image';
                }

                // Check if the image is not already in the database
                $filehash = md5_file($tmpPath);
                $i = null;
                try {
                    $i = Image::getOne($filehash);
                } catch (Exception $e) {
                    // code 1192021 is for no record found
                    if ($e->getCode() != 1192021) {
                        $errors['image'] = 'Image could not be processed';
                        $_POST['error'] = $errors;
                        return $this->html($_POST);
                    }
                }

                if ($i != null) {
                    // Image already in DB
                    $imageId = $i->getId();
                    try {
                        $i->addReference();
                    } catch (Exception $e) {
                        $errors['image'] = 'Image could not be processed';
                    }
                } else {
                    // Move image, add it to DB
                    $filename = md5($filename . time()) . '.' . $extension;
                    $destPath = 'public/visuals/images/' . $filename;

                    if(move_uploaded_file($tmpPath, $destPath))
                    {
                        // Create DB entry
                        $image = new Image(
                            $filehash,
                            $uid,
                            $destPath,
                            1
                        );

                        // Update database
                        try {
                            $imageId = $image->save();
                        } catch (Exception $e) {
                            $errors['image'] = 'Image could not be uploaded';
                        }
                    } else {
                        $errors['image'] = 'Image could not be uploaded';
                    }
                }
            } else {
                $errors['image'] = 'Image could not be uploaded';
            }
        }

        // If any errors occurred
        if (count($errors) !== 0) {
            $_POST['error'] = $errors;
            return $this->html($_POST);
        }

        // Inputs are ok - create the post
        $post = new Post(
            null,
            $isArticle ? 'article' : 'userpost',
            $_SESSION['uid'],
            $_POST['title'],
            $_POST['text'],
            $imageId,
            date("Y-m-d"),
        );

        try {
            $pid = $post->save();
        } catch (Exception $e) {
            $errors['image'] = 'Post could not be saved';
            $_POST['error'] = $errors;
            return $this->json($_POST);
        }

        // Upvote the post
        $vote = new Vote(
            $pid,
            $uid,
            1
        );

        try {
            $vote->saveCK(true,'post', $pid, 'user', $uid);
        } catch (Exception $e) {
            return $this->json(null);
        }

        header('Location: ?c=account&a=profile');
        return $this->html();
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
        return $this->json(['error' => 'User is not logged in']);
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
                HomeController::redirError();
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
            HomeController::redirError();
            exit(0);
        }

        $id_acc = null;
        if ( !is_null($account->getId()) ) {
            // User is already in database
            try {
                $id_acc = Account::getOne($account->getId());
            } catch (Exception $e) {
                HomeController::redirError();
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
            HomeController::redirError();
            exit(0);
        }

        return [];
    }

    private function account_login(string $username, string $password): bool
    {
        try {
            $accounts = Account::getAll();
        } catch (Exception $e) {
            HomeController::redirError();
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

    public function get_user()
    {
        if (!isset($_GET['uid'])) {
            return $this->json(null);
        }

        $uid = @$_GET['uid'];

        try {
            $user = Account::getOne($uid);
            return $this->json($user->getData());
        } catch (Exception $e) {
            return $this->json(null);
        }
    }

    public function get_users()
    {
        try {
            $users = Account::getAll();
            $data = [];
            $i = 0;
            foreach ($users as $user) {
                $data[$i++] = $user->getData();
            }
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
        }
    }

}