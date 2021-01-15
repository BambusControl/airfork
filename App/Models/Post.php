<?php

namespace App\Models;


class Post extends Model
{

    protected $id;
    protected $type;
    protected $author;
    protected $content;
    protected $image;
    protected $date;
    protected $upvotes;
    protected $downvotes;

    public function __construct($id = null, $type = '', $author = '', $content = '', $image = '', $date = '', $upvotes = '', $downvotes = '')
    {
        $this->id = $id;
        $this->type = $type;
        $this->author = $author;
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
}