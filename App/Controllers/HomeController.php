<?php


namespace App\Controllers;


use App\Core\AControllerBase;
use App\Models\Image;
use App\Models\Post;
use Exception;

class HomeController extends AControllerBase
{

    public function index()
    {
        // Novinky

        // Nacitaj clanky z databazy
        // Spracuj nacitane clanky
        // Vykresli clanky

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
        // check if logged in ?
        if (isset($_POST['submit'])) {
            session_start(['read_and_close' => true]);

            $dest_path = null;
            // Image file upload
            if ( isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
                // Spracuj image
                // TODO Get alt
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $newFileName = md5($fileName . time()) . '.' . $fileExtension;
                $dest_path = 'public/visuals/images/' . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path))
                {
                    $message ='File is successfully uploaded.';
                    $image = new Image(
                        null,
                        $_SESSION['uid'],
                        $dest_path,
                        'TODO ALT'
                    );
                    // Update database
                    $imageID = $image->save();
                }

            }

            // Check all inputs
            $post = new Post(
                null,
                'NEWS',
                $_SESSION['uid'],
                $_POST['title'],
                $_POST['text'],
                $imageID,
                date("Y-m-d"),
                0,
                0
            );
            $post->save();
        }

        return$this->html();
    }

    public function errorpage()
    {
        return $this->html();
    }

    public function news()
    {
        try {
            return $this->json(Post::getAll("type='NEWS'", [], "id DESC"));
        } catch (Exception $e) {
            return null;
        }
    }

    public function images()
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
        $id = $_GET["id"];
        $t = $_GET["t"];

        try {
            $post = Post::getOne($id);
        } catch (Exception $e) {
            return null;
        }

        switch ($t) {
            case "u":
                $post->upvote();
                break;
            case "d":
                $post->downvote();
                break;
            case "ru":
                $post->removeUpvote();
                break;
            case "rd":
                $post->removeDownvote();
                break;
            default:
                return null;
        }

        try {
            $post->save();
        } catch (Exception $e) {
            return null;
        }

        return $this->json($post);
    }

}