<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;

class TwilioController extends Controller
{
    public function index()
    {
        $sid = "ACd40df911a63cea4375b1a8166a443a5e";
        $token = "157757d1c7b057aec6216bfffbdef1fd";
        $twilio = new Client($sid, $token);

        $messages = $twilio->messages
            ->read([], 20);

        $data = [];
        foreach ($messages as $key => $record) {
            array_push($data, [
                "body" => $record->body,
                "direction" => $record->direction,
                "from" => $record->from,
                "status" => $record->status,
                "to" => $record->to,
            ]);
        }
        return $data;

    }
    public function store()
    {
        $sid = "ACd40df911a63cea4375b1a8166a443a5e";
        $token = "157757d1c7b057aec6216bfffbdef1fd";
        $twilio_number = "+12014823396";

        $client = new Client($sid, $token);
        $rs = $client->messages->create(
            '+84335964325',
            array(
                'from' => $twilio_number,
                'body' => 'I sent this message in under 10 minutes!',
            )
        );
    }
}
