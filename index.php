<?php

use App\Supermetrics\Service;
use GuzzleHttp\Exception\GuzzleException;

require_once 'vendor/autoload.php';
require_once 'helpers.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['CLIENT_ID', 'EMAIL', 'NAME'])->notEmpty();
$dotenv->required('MAX_PAGES')->isInteger();

try {
    $posts = (new Service())->postMultiplePages();
} catch (GuzzleException|JsonException $e) {
    json_response(['message' => 'Something went wrong, please try again later'], 500);
}

json_response([
    'average_message_len' => $posts->averageMessageLenPerMonth(),
    'longest_message' => $posts->longestForMonth(),
    'posts_per_week' => $posts->postsPerWeek(),
    'average_per_user_per_month' => $posts->userAveragePerMonth()
]);
