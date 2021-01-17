<?php

namespace App\Models;


use App\Core\Model;

class Post extends Model
{

    protected $id;
    protected $type;
    protected $author;
    protected $title;
    protected $content;
    protected $image;
    protected $date;
    protected $upvotes;
    protected $downvotes;

    public function __construct($id = null, $type = '', $author = '', $title = '', $content = '', $image = '', $date = '', $upvotes = '', $downvotes = '')
    {
        $this->id = $id;
        $this->type = $type;
        $this->author = $author;
        $this->title = $title;
        $this->content = $content;
        $this->image = $image;
        $this->date = $date;
        $this->upvotes = $upvotes;
        $this->downvotes = $downvotes;
    }

    static public function setDbColumns()
    {
        return [
            'id',
            'type',
            'author',
            'title',
            'content',
            'image',
            'date',
            'upvotes',
            'downvotes'
        ];
    }

    static public function setTableName()
    {
        return 'posts';
    }

    /**
     * @return mixed|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed|string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed|string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed|string $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed|string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed|string $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed|string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed|string $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed|string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed|string $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function upvote()
    {
        $this->upvotes++;
    }

    public function downvote()
    {
        $this->downvotes++;
    }

    public function removeUpvote()
    {
        $this->upvotes--;
    }

    public function removeDownvote()
    {
        $this->downvotes--;
    }
}