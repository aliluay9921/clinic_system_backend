<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait SendWhatsappMessage
{

    public function sendChatMessage($client, $contact, $message, $referenceId = "", $instance = null)
    {
        try {
            //uwm_instance_id
            $contact = str_replace(" ", "", $contact);
            //remove first zero
            $contact = ltrim($contact, '0');
            // error_log("Sending message to $contact ");
            $response = Http::get("https://api.textmebot.com/send.php", [
                "recipient" => "+964" . $contact,
                "apikey" => $client,
                "text" => $message,
                "json" => "yes"
            ]);
            error_log($response->body());
            return $response->body();
            $status = $response->json("status");
            if ($status == "success") {
                return true;
            } else {
                return false;
            }
            // error_log("Message sent to $contact with referenceId: ".$response->body());
        } catch (\Exception $e) {
            return false;
        }
    }
}
