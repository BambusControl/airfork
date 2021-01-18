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

}