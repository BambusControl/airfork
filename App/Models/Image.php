<?php


namespace App\Models;


use App\Core\Model;

class Image extends Model
{

    protected $id;
    protected $author;
    protected $path;
    protected $alt;

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

    /**
     * @return mixed|null
     */
    public function getId(): ?mixed
    {
        return $this->id;
    }
}