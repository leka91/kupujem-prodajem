<?php

class UserLog extends Model
{
    protected static $table = 'user_logs';
    protected static $columns = ['id', 'user_id', 'action', 'log_time'];
}