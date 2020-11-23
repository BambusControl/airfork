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

    /**
     * Account constructor.
     * @param $id
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param string $date_of_birth
     * @param string $gender
     */
    public function __construct($id = null, $username = '', $email = '', $password = '', $firstname = '', $lastname = '', $date_of_birth = '', $gender = '')
    {
        $this->id = $id;
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
            'gender'
        ];
    }

    static public function setTableName()
    {
        return 'user_accounts';
    }

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getDateOfBirth(): string
    {
        return $this->date_of_birth;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    public function as_array()
    {
        $array_v = array_reverse( array_values((array) $this), false );
        $array = array();

        foreach (self::setDbColumns() as $key)
        {
            $array[$key] = array_pop($array_v);
        }

        return $array;
    }

}