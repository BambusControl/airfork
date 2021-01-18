<?php


namespace App\Models;


use App\Core\Model;

class Vote extends Model
{

    protected $post;
    protected $user;
    protected $type;

    public function __construct($post = null, $user = null, $type = '')
    {
        $this->post = $post;
        $this->user = $user;
        $this->type = $type;
    }

    static public function setDbColumns()
    {
        return [
            'post',
            'user',
            'type'
        ];
    }

    static public function setTableName()
    {
        return 'votes';
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}