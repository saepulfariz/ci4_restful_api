<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Login extends BaseController
{
    use ResponseTrait;

    private $jwtAlg;
    private $accessTokenKey;
    private $refreshTokenKey;
    private $accessTokenExpired;
    private $refreshTokenExpired;

    public function __construct()
    {
        $this->jwtAlg = ($algo = getenv('JWT_ALG')) ? $algo : 'HS256';
        $this->accessTokenKey = getenv('ACCESS_TOKEN_KEY');
        $this->refreshTokenKey = getenv('REFRESH_TOKEN_KEY');
        $this->accessTokenExpired = getenv('ACCESS_TOKEN_EXPIRY');
        $this->refreshTokenExpired = getenv('REFRESH_TOKEN_EXPIRY');
    }

    public function index()
    {
        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->orWhere('username', $email)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid username.'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Invalid password.'], 401);
        }

        $key = $this->accessTokenKey;
        $iat = time(); // current timestamp value
        $exp = $iat + $this->accessTokenExpired;

        $payload = array(
            "iss" => "Issuer of the JWT",
            "aud" => "Audience that the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
            ]
        );

        $accessToken = JWT::encode($payload, $key, $this->jwtAlg);

        $expirationTime = $iat + $this->refreshTokenExpired;
        $payload = [
            'iat' => $iat,
            'exp' => $expirationTime,
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
            ]
        ];

        $refreshToken = JWT::encode($payload, $this->refreshTokenKey, $this->jwtAlg);

        $response = [
            'message' => 'Login Succesful',
            'data' => [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ]
        ];

        return $this->respond($response, 200);
    }
}
