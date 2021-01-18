<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Image;
use App\Models\Post;
use App\Models\Vote;
use Exception;

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

    public function add_article()
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

        // Image
        if ( isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK ) {

            $tmpPath = $_FILES['image']['tmp_name'];
            $filename = explode(".", $_FILES['image']['name']);
            $extension = strtolower(end($filename));

            // TODO hash img
            md5_file($tmpPath);

            // 'Random' filename
            $filename = md5($filename . time()) . '.' . $extension;
            $destPath = 'public/visuals/images/' . $filename;

            if(move_uploaded_file($tmpPath, $destPath))
            {
                // Create DB entry
                $image = new Image(
                    null,
                    $uid,
                    $destPath
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
        } else {
            $errors['image'] = 'Image could not be uploaded';
        }

        // If any errors occurred
        if (count($errors) !== 0) {
            $_POST['error'] = $errors;
            return $this->html($_POST);
        }

        // Inputs are ok - create the post
        $post = new Post(
            null,
            'article',
            $_SESSION['uid'],
            $_POST['title'],
            $_POST['text'],
            $imageId,
            date("Y-m-d"),
        );

        try {
            $post->save();
        } catch (Exception $e) {
            $_POST['error'] = $errors;
            $errors['image'] = 'Image could not be uploaded';
        }

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

    public function get_images()
    {
        try {
            return $this->json(Image::getAll());
        } catch (Exception $e) {
            return null;
        }
    }

    public function get_post()
    {
        $id = $_GET["id"];

        try {
            return $this->json(Post::getOne($id));
        } catch (Exception $e) {
            return null;
        }
    }

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
        try {
            $votes = Vote::getAll();
            return $this->json($votes);
        } catch (Exception $e) {
            return null;
        }
    }

}