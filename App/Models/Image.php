<?php


namespace App\Models;


use App\Core\Model;

class Image extends Model
{

    protected $id;
    protected $author;
    protected $path;

    public function __construct($id = null, $author = '', $path = '')
    {
        $this->id = $id;
        $this->author = $author;
        $this->path = $path;
    }

    static public function setDbColumns()
    {
        return [
          'id',
          'author',
          'path',
        ];
    }

    static public function setTableName()
    {
        return 'images';
    }

    public function getId()
    {
        return $this->id;
    }
}