<?php


namespace App\Models;


use App\Core\Model;

class Account extends Model
{

    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $firstname;
    protected $lastname;
    protected $date_of_birth;
    protected $gender;
    protected $created_at;

    /**
     * Account constructor.
     * @param $username
     * @param $email
     * @param $password
     * @param $firstname
     * @param $lastname
     * @param $date_of_birth
     * @param $gender
     */
    public function __construct($username = null, $email = null, $password = null, $firstname = null, $lastname = null, $date_of_birth = null, $gender = null)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->date_of_birth = $date_of_birth;
        $this->gender = $gender;
    }

    static public function setDbColumns()
    {
        return[
            'id',
            'username',
            'email',
            'password',
            'firstname',
            'lastname',
            'date_of_birth',
            'gender',
            'created_at'
        ];
    }

    static public function setTableName()
    {
        return 'user_accounts';
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

}