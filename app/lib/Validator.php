<?php

class Validator 
{
    private static $errors = [];
    private static $fields = ['email', 'password', 'password2'];

    public static function validate($data)
    {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $data)) {
                responseError("{$field} not found");
            }
        }

        self::validateEmail($data);
        self::validatePassword($data);
        self::validateMaxMind($data);

        return self::$errors;
    }

    private static function validateEmail($data)
    {
        $email = trim($data['email']);

        $pattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,
        })(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x
        23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x
        1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x
        2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x
        5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0
        -9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a
        -z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f
        0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-
        9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*
        [a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-
        9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:
        \.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

        $user = new User;
        $userEmail = $user->select()
                        ->where(["email = {$email}"])
                        ->first();

        if (empty($email)) {
            self::pushError('email', 'Email empty');
        } elseif ($userEmail) {
            self::pushError('email', 'Email already used');
        } elseif (!preg_match($pattern, $email)) {
            self::pushError('email', 'Invalid Email format');
        }
    }

    private static function validatePassword($data)
    {
        $password = $data['password'];
        $password2 = $data['password2'];

        if (empty($password) || mb_strlen($password) < 8) {
            self::pushError('password', 'Invalid password');
        } elseif (empty($password2) || mb_strlen($password) < 8) {
            self::pushError('password2', 'Invalid password2');
        } elseif ($password != $password2) {
            self::pushError('password', 'Password mismatch');
        }
    }

    private static function validateMaxMind($data)
    {
        $email = trim($data['email']);
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    private static function pushError($key, $value)
    {
        self::$errors[$key] = $value;
    }
}