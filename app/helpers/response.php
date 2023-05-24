<?php

function responseSuccess($data)
{
    echo json_encode([
        'success' => true,
        'data'    => $data
    ]);
}

function responseError($error)
{
    echo json_encode([
        'success' => false,
        'error'   => $error
    ]);

    exit;
}