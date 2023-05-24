<?php

class UserLog extends Model
{
    protected static $table = 'user_logs';
    protected static $columns = ['user_id', 'action', 'log_time'];
}