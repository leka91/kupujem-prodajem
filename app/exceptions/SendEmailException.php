<?php

class SendEmailException extends Exception 
{
    public function errorMessage() 
    {
        return $this->getMessage();
    }
}