<?php

namespace App\Http\Controllers;

use Nexmo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NexmoController extends Controller
{
    public function index()
    {
        $api_key = "dc2aa028";
        $secret = "E8rBWGfgBYe3x8xq";
        $client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($api_key, $secret));

        // $client = new Client(new Nexmo\Client\)
    }

    public function get(Request $request, Response $response)
    {
        // $app = new \Slim\App;

        $params = $request->getParsedBody();
// Fall back to query parameters if needed
        if (!count($params)) {
            $params = $request->getQueryParams();
        }
        error_log(print_r($params, true));
        return $response->withStatus(204);

    }
}
