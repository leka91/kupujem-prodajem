<?php

class UserController
{
    private $userModel;
    private $userLogModel;

    public function __construct() {
        $this->userModel = new User;
        $this->userLogModel = new UserLog;
    }

    public function register()
    {
        $email = 'test@test.com';

        $errors = Validator::validate([
            'email' => $email,
            'password' => 'pass123',
            'password2' => 'pass123'
        ]);

        if (count($errors)) {
            responseError($errors);
        }
        
        try {
            $userId = $this->userModel->insert([
                'email' => $email,
                'password' => 'pass123'
            ]);

            $subject = 'Dobro došli';
            $message = 'Dobro dosli na nas sajt. Potrebno je samo da potvrdite
            email adresu ...';
            $headers = [
                'From: adm@kupujemprodajem.com'
            ];

            $mail = MailFactory::create($email, $subject, $message, $headers);
            $mail->sendEmail();

            $this->userLogModel->insert([
                'user_id' => $userId,
                'action' => 'register',
                'log_time' => date("Y-m-d H:i:s")
            ]);

            sessionStart();
            sessionSet('user_id', $userId);

            responseSuccess($userId);
        } catch (Exception $e) {
            responseError($e->getMessage());
        } catch (SendEmailException $e) {
            responseError($e->errorMessage());
        }
    }
}