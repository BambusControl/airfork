<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Image;
use App\Models\Post;
use App\Models\Vote;
use Exception;

class ContentController extends AControllerBase
{

    public function index()
    {
        return $this->json(['error' => 'Index is not a page']);
    }

    // --- Posts ---

    public function get_posts()
    {
        $request = '';
        if (isset($_GET['type'])) {
            // Start creating SQL request
            $t = @$_GET['type'];
            $request = 'type=\'' . $t . '\'';

            // A request for user posts also requires userid
            if ($t === 'userpost') {

                if (isset($_GET['uid'])) {
                    $request .= ' AND author=' . @$_GET['uid'];
                }
            }
        } else {
            if (isset($_GET['uid'])) {
                $request .= 'author=' . @$_GET['uid'];
            }
        }

        // Retreive posts from DB
        try {
            $data = Post::getAll($request, [], 'id DESC');
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
        }
    }

    public function get_post()
    {
        if (!isset($_GET['pid'])) {
            // not enough parameters
            return $this->json(['error' => 'Missing parameters']);
        }

        $pid = @$_GET['pid'];

        // Retreive posts from DB
        try {
            $data = Post::getOne($pid);
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
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
            $this->json(['error' => 'Form was not submitted']);
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
            $errors['error'] = "User not logged in";
            return $this->json($errors);
        }

        session_start(['read_and_close' => true]);
        $uid = $_SESSION['uid'];
        $is_admin = $_SESSION['is_admin'];

        $errors = [];

        if (!isset($_POST['pid'])) {
            $errors['error'] = "Cannot delete post, post doesn't exist";
            return $this->json($errors);
        }

        $pid = $_POST['pid'];

        // Get the post from DB
        try {
            $post = Post::getOne($pid);
        } catch (Exception $e) {
            $errors['error'] = $e->getMessage();
            return $this->json($errors);
        }

        // Check if uid == post author id or if its an admin
        if (!$is_admin && $post->getAuthor() != $uid) {
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

    // --- Images ---

    public function get_images()
    {
        try {
            $data = Image::getAll();
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
        }
    }

    public function get_image()
    {
        if (isset($_GET['id'])) {
            try {
                $data = Image::getOne($_GET['id']);
                return $this->json($data);
            } catch (Exception $e) {
                return $this->json(['error' => 'Error retreiving data from database']);
            }
        }
        return $this->json(['error' => 'No image ID specified']);
    }

    // --- Votes ---

    public function vote()
    {
        if (!(isset($_POST["pid"]) && isset($_POST["uid"]) && isset($_POST["t"]))) {
            // Missing inputs
            return $this->json(['error' => 'Missing parameters']);
        }

        $pid = @$_POST["pid"];
        $uid = @$_POST["uid"];
        $t = @$_POST["t"];

        // Get the vote from database
        try {
            $vote = Vote::getAll('post=' . $pid . ' AND user=' . $uid);
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
        }

        if (count($vote) === 0) {
            // No vote found in database
            if ($t === '0') {
                return $this->json(['error' => 'No vote to remove']);
            }

            // Create vote
            $vote = new Vote(
                $pid,
                $uid,
                $t
            );

            // Save vote
            try {
                $vote->saveCK(true, 'post', $pid, 'user', $uid);
            } catch (Exception $e) {
                return $this->json(['error' => 'Error saving data to database']);
            }
        } else {
            // Vote found in database
            $vote = $vote[0];

            if ($t === '0') {
                // Remove vote
                try {
                    $vote->deleteCK('post', $pid, 'user', $uid);
                } catch (Exception $e) {
                    return $this->json(['error' => 'Error deleting data from database']);
                }
            } else {
                // Update vote - change upvote <-> downvote
                $vote->setType($t);
                try {
                    $vote->saveCK(false, 'post', $pid, 'user', $uid);
                } catch (Exception $e) {
                    return $this->json(['error' => 'Error saving data to database']);
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
            return $this->json(['error' => 'Error retreiving data from database']);
        }
    }

    public function get_vote()
    {
        if (!(isset($_GET['pid']) && (isset($_GET['uid'])))) {
            // not enough parameters
            return $this->json(['error' => 'Missing parameters']);
        }

        $pid = @$_GET['pid'];
        $uid = @$_GET['uid'];
        $req = 'post=' . $pid . ' AND user=' . $uid;

        // Retreive posts from DB
        try {
            $data = Vote::getAll($req);
            if (count($data) === 0) {
                return $this->json(['error' => 'No such record in database']);
            } else {
                return $this->json($data[0]);
            }
        } catch (Exception $e) {
            return $this->json(['error' => 'Error retreiving data from database']);
        }
    }

}