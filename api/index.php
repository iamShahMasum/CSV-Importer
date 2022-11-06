<?php

namespace api;
ini_set('auto_detect_line_endings', TRUE);
ini_set("max_execution_time", "3600");

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;


require_once "vendor/autoload.php";
$db = require_once "config/database.php";
$app = new App(require_once "config/settings.php");

$app->any('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    $api_image = '<img height="80px" src="data:image/png;base64,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"/>';
    $html = '<div style="width: 100%; height: 80%; position: relative;" ><div style="height:80%; position: relative; display: flex; justify-content: center; align-items: center;text-align: center;" >' . $api_image . '</div></div>';
    return $response->withStatus(403)->write($html)->write('<p style="text-align: center" >403 Forbidden</p>');

});


$app->post('/csvimport', function (ServerRequestInterface $request, ResponseInterface $response) {
    $reader = new FileReader();
    $csvfile = $_FILES["csvfile"];

    $body = $request->getParsedBody();
    $startFrom = $body['startFrom'];
    $endTo = $body['endTo'];
    try {
        $data = $reader->read($csvfile, $startFrom, $endTo);
    } catch (\Exception $exception) {
        return $response->withStatus(500)
            ->withJson(array("response" => $exception->getMessage(), 'request' => $body));
    } finally {
        return $response->withStatus(200)
            ->withJson(array("response" => $data, 'request' => $body));
    }

});

$app->run();
