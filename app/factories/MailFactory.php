<?php

class MailFactory
{
    public static function create($to, $subject, $message, $headers = [])
    {
        return new Mail($to, $subject, $message, $headers);
    }
}