<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Image;
use App\Models\Post;
use App\Models\Vote;
use Exception;
use http\Message;

class HomeController extends AControllerBase
{

    public function index()
    {
        // Novinky
//        Image::deleteUnlinkedFiles(); TODO
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

    public function add_article()   // TODO rename
    {
        // Check if a user is logged in
        if (!AccountController::is_logged_in()) {
            header('Location: ?c=account&a=login');
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

        // Image TODO image file type check
        $imageId = null;
        if ( isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {

            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $filename = $_FILES['image']['name'];
                $nameArr = explode(".", $filename);
                $extension = strtolower(end($nameArr));

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
                    // 'Random' filename TODO
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

    public function errorpage()
    {
        return $this->html();
    }

    public function get_all_posts()
    {
        if (!isset($_GET['type'])) {
            return $this->json(null);
        }

        // Start creating SQL request
        $t = @$_GET['type'];
        $req = 'type=\'' . $t . '\'';

        // A request for user posts also requires userid
        if ($t === 'userpost') {

            if (isset($_GET['uid'])) {
                $req .= ' AND author=' . @$_GET['uid'];
            }
        }

        // Retreive posts from DB
        try {
            $data = Post::getAll($req, [], 'id DESC');
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(null);
        }
    }

    public function get_post()
    {
        if (!isset($_GET['pid'])) {
            // not enough parameters
            return $this->json(null);
        }

        $pid = @$_GET['pid'];

        // Retreive posts from DB
        try {
            $data = Post::getOne($pid);
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(null);
        }
    }

    public function get_images()
    {
        try {
            $data = Image::getAll();
            return $this->json($data);
        } catch (Exception $e) {
            return null;
        }
    }

    public function get_image()
    {
        if (isset($_GET['id'])) {
            try {
                $data = Image::getOne($_GET['id']);
                return $this->json($data);
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    /*public function get_post()
    {
        $id = $_GET["id"];

        try {
            return $this->json(Post::getOne($id));
        } catch (Exception $e) {
            return null;
        }
    }*/

    public function vote()
    {
        $pid = @$_GET["pid"];
        $uid = @$_GET["uid"];
        $t = @$_GET["t"];

        $vote = [];

        try {
            $vote = Vote::getAll('post=' . $pid . ' AND user=' . $uid);
        } catch (Exception $e) {
            return null;
        }

        if (count($vote) === 0) {

            if ($t === '0') {
                return $this->json($vote);
            }

            $vote = new Vote(
                $pid,
                $uid,
                $t
            );

            try {
                $vote->saveCK(true, 'post', $pid, 'user', $uid);
            } catch (Exception $e) {
                return null;
            }
        } else {
            $vote = $vote[0];

            if ($t === '0') {
                try {
                    $vote->deleteCK('post', $pid, 'user', $uid);
                } catch (Exception $e) {
                    return null;
                }
            } else {
                $vote->setType($t);
                try {
                    $vote->saveCK(false, 'post', $pid, 'user', $uid);
                } catch (Exception $e) {
                    return null;
                }
            }
        }

        return $this->json($vote);
    }

    public function get_votes()
    {
        $req = '';
        if (isset($_GET['pid'])) {
            $pid = @$_GET['pid'];
            $req = 'post=' . $pid;
        } elseif (isset($_GET['uid'])) {
            $uid = @$_GET['uid'];
            $req = 'user=' . $uid;
        }

        try {
            $votes = Vote::getAll($req);
            return $this->json($votes);
        } catch (Exception $e) {
            return null;
        }
    }

    public function get_vote()
    {
        if (!(isset($_GET['pid']) && (isset($_GET['uid'])))) {
            // not enough parameters
            return $this->json(null);
        }

        $pid = @$_GET['pid'];
        $uid = @$_GET['uid'];
        $req = 'post=' . $pid . ' AND user=' . $uid;

        // Retreive posts from DB
        try {
            $data = Vote::getAll($req);
            if (count($data) === 0) {
                return $this->json(null);
            } else {
                return $this->json($data[0]);
            }
        } catch (Exception $e) {
            return $this->json(null);
        }
    }

    public function modify_post()
    {
        if (!AccountController::is_logged_in()) {
            header('Location: ?c=account&a=login');
            exit(0);
        }

        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];

        // Check if user submitted a form
        if (!isset($_POST['id'])) {
            return $this->json(null);
        }

        // Inputs
        $id = $_POST['id'];
        $title = $_POST['title'];
        $text = $_POST['text'];

        // Check all inputs
        $errors = [];

        if (empty($title)) {
            $errors['error'] = "Titulok nemôže byť prázdny!";
        }

        if (empty($text)) {
            $errors['error'] = "Textové pole nemôže byť prázdne!";
        }

        // If any errors occurred
        if (count($errors) !== 0) {
            return $this->json($errors);
        }

        // Get the post from DB
        try {
            $post = Post::getOne($id);
        } catch (Exception $e) {
            $errors['error'] = $e->getMessage();
            return $this->json($errors);
        }

        // Check if uid == post author id
        if ($post->getAuthor() != $uid) {
            $errors['error'] = "Can't modify post, because user " . $uid . " is not author of this post";
            return $this->json($errors);
        }

        // No data changed
        if ($post->getContent() === $text && $post->getTitle() === $title) {
            return $this->json($post);
        }

        // Inputs are ok - update the post
        $post->setTitle($title);
        $post->setContent($text);

        try {
            $post->save();
        } catch (Exception $e) {
            $_POST['error'] = $e->getMessage();
            return $this->json($errors);
        }

        return $this->json($post);
    }

    public function remove_post() {
        if (!AccountController::is_logged_in()) {
            header('Location: ?c=account&a=login');
            exit(0);
        }

        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];
        $is_admin = $_SESSION['is_admin'];

        $errors = [];

        if (!isset($_GET['pid'])) {
            $errors['error'] = "Cannot delete post, post doesn't exist";
            return $this->json($errors);
        }

        $pid = $_GET['pid'];

        // Get the post from DB
        try {
            $post = Post::getOne($pid);
        } catch (Exception $e) {
            $errors['error'] = $e->getMessage();
            return $this->json($errors);
        }

        // Check if uid == post author id or if its an admin
        if ($is_admin || $post->getAuthor() != $uid) {
            $errors['error'] = "Can't modify post, because user " . $uid . " is not author of this post";
            return $this->json($errors);
        }

        // Delete the post from DB
        try {
            $post->delete();
        } catch (Exception $e) {
            $errors['error'] = $e->getMessage();
            return $this->json($errors);
        }

        return $this->json($errors);

    }

    public static function redirError ()
    {
        header('Location: ?c=home&a=errorpage');
    }

}