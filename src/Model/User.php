<?php

namespace Ainab\Really\Model;
use Ramsey\Uuid\Uuid;

class User {
    public $id;
    public $uuid;
    public $email;
    public $password;
    public $firstName;
    public $lastName;
    public $avatar;
    public $isAdmin = false;
    public $createdAt;
    public $updatedAt;


    public function __construct($email, $password, $firstName = null, $lastName = null, $avatar = null, $isAdmin = false) {
        $this->id = uniqid();
        $this->uuid = Uuid::uuid4()->toString();
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->avatar = $avatar;
        $this->isAdmin = $isAdmin;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = $this->createdAt;
    }

    public static function fromArray($data = []) {

        // throw error if email is not set
        if (!isset($data['email'])) {
            throw new \Exception('Email is required');
        }

        // throw error if password is not set
        if (!isset($data['password'])) {
            throw new \Exception('Password is required');
        }

        $isAdmin = false;

        if (isset($data['isAdmin'])) {
            $isAdmin = $data['isAdmin'];
        }

        $user = new User($data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['avatar'], $isAdmin);
        $user->id = $data['id'];
        $user->uuid = $data['uuid'];
        $user->createdAt = $data['createdAt'];
        $user->updatedAt = $data['updatedAt'];
        return $user;
    }
}
