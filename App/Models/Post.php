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

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function delete()
    {
        if ($this->image != null) {
            $i = Image::getOne($this->image);
            $i->delete();
        }

        parent::delete();
    }

}