<?php


namespace App\Models;


class Image extends Model
{

    protected $id;
    private string $author;
    private string $path;
    private string $alt;

    public function __construct($id = null, $author = '', $path = '', $alt = '')
    {
        $this->id = $id;
        $this->author = $author;
        $this->path = $path;
        $this->alt = $alt;
    }

    static public function setDbColumns()
    {
        return [
          'id',
          'author',
          'path',
          'alt'
        ];
    }

    static public function setTableName()
    {
        return 'images';
    }
}