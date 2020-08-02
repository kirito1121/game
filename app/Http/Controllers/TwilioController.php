<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;

class TwilioController extends Controller
{
    public function index()
    {
        $sid = "ACd40df911a63cea4375b1a8166a443a5e";
        $token = "737f5ead1dff6551f9d5ba6efc625a47";

        $twilio = new Client($sid, $token);

        $messages = $twilio->messages
            ->read([], 20);

        $data = [];
        foreach ($messages as $key => $record) {
            // DB::connection('mysqluserDB')->table('sms')->insert([
            //     "data" => $record,
            // ]);
            array_push($data, [
                "body" => $record->body,
                "direction" => $record->direction,
                "dateUpdated" => $record->dateUpdated,
                "dateSent" => $record->dateSent,
                "dateCreated" => $record->dateCreated,
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
