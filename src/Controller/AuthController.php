<?php

namespace Ainab\Really\Controller;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\Request;
use Ainab\Really\Service\UserService;
use Ainab\Really\Service\JwtService;
use Ainab\Really\Model\User;

class AuthController extends BaseController
{
    public function __construct(private UserService $userService, private JwtService $jwtService)
    {
        parent::__construct();
    }

    public function index()
    {
        $request = new Request();
        $jwt = $request->cookie('jwt');

        if ($this->jwtService->validateToken($jwt)) {
            header('Location: /');
        }

        echo $this->twig->render('pages/login.html.twig');
    }

    public function login()
    {
        $formData = $_POST;
        $email = $formData['email'];
        $password = $formData['password'];
        $request = new Request();
        $redirect = $request->query('redirect');

        $user = $this->userService->getUserByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            $validUntil = time() + 3600;

            $payload = [
                'id' => $user->id,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'isAdmin' => $user->isAdmin ?? false,
                'validUntil' => $validUntil,
            ];

            $jwt = $this->jwtService->generateToken($payload);
            setcookie('jwt', $jwt, time() + 3600, '/', '', false, false);

            if ($redirect) {
                header('Location: ' . $redirect);
            } else {
                header('Location: /');
            }
        } else {
            echo $this->twig->render('pages/login.html.twig', ['error' => 'Invalid email or password']);
        }
    }

    public function logout()
    {
        setcookie('jwt', '', time() - 3600, '/', '', false, false);
        header('Location: /');
    }

    public function register()
    {
        echo $this->twig->render('pages/register.html.twig');
    }

    public function createUser()
    {
        $request = new Request();

        $email = $request->body('email');
        $password = $request->body('password');

        $user = $this->userService->getUserByEmail($email);

        if ($user) {
            echo $this->twig->render('pages/register.html.twig', ['error' => 'Could not create user']);
        } else {
            $user = new User($email, password_hash($password, PASSWORD_DEFAULT));
            $this->userService->createUser($user);

            $validUntil = time() + 3600;

            $payload = [
                'id' => $user->id,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'isAdmin' => $user->isAdmin ?? false,
                'validUntil' => $validUntil,
            ];

            $jwt = $this->jwtService->generateToken($payload);
            setcookie('jwt', $jwt, time() + 3600, '/', '', false, false);

            echo $this->twig->render('pages/register.html.twig', ['success' => 'User created successfully']);
        }
    }
}
