<?php

namespace Ainab\Really\Service;

use Ainab\Really\Model\User;

class UserService
{
    private $filePath = '../db/users/users.json';

    public function __construct()
    {
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    public function createUser(User $user)
    {
        $users = $this->getAllUsers();
        $users[] = get_object_vars($user);
        file_put_contents($this->filePath, json_encode($users));
    }

    public function updateUser($uuid, $newData)
    {
        $users = $this->getAllUsers();
        foreach ($users as &$user) {
            if ($user['uuid'] === $uuid) {
                foreach ($newData as $key => $value) {
                    if (isset($user[$key])) {
                        $user[$key] = $value;
                    }
                }
                $user['updatedAt'] = date('Y-m-d H:i:s');
                file_put_contents($this->filePath, json_encode($users));
                return;
            }
        }
    }

    public function deleteUser($uuid)
    {
        $users = $this->getAllUsers();
        foreach ($users as $key => $user) {
            if ($user['uuid'] === $uuid) {
                unset($users[$key]);
                file_put_contents($this->filePath, json_encode(array_values($users)));
                return;
            }
        }
    }

    public function getUser($uuid)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['uuid'] === $uuid) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByEmail($email)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return User::fromArray($user);
            }
        }
        return null;
    }

    private function getAllUsers()
    {
        $directory = dirname($this->filePath); // Get the directory path

        // Check if the directory exists, if not, create it
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // Creates the directory recursively
        }

        // Now that the directory is ensured to exist, check for the file
        if (file_exists($this->filePath)) {
            $json = file_get_contents($this->filePath);
            return json_decode($json, true);
        } else {
            file_put_contents($this->filePath, json_encode([])); // This should now work without error
            return [];
        }
    }
}
