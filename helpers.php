<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function json_response(array $data = [], int $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    die();
}

function to_decimal($numeric): float
{
    return (float)number_format($numeric, 2);
}
