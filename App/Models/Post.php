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

    public function __construct($id = null, $type = '', $author = '', $title = '', $content = '', $image = '', $date = '')
    {
        $this->id = $id;
        $this->type = $type;
        $this->author = $author;
        $this->title = $title;
        $this->content = $content;
        $this->image = $image;
        $this->date = $date;
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
        ];
    }

    static public function setTableName()
    {
        return 'posts';
    }

    /**
     * @param mixed|string $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param mixed|string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @param mixed|string $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed|string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }

}