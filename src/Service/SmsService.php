<?php
// src/Service/SmsService.php
namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private $twilioSid;
    private $twilioAuthToken;
    private $twilioFromNumber;

    public function __construct(string $twilioSid, string $twilioAuthToken, string $twilioFromNumber)
    {
        $this->twilioSid = $twilioSid;
        $this->twilioAuthToken = $twilioAuthToken;
        $this->twilioFromNumber = $twilioFromNumber;
    }

    public function sendSms(string $to, string $message): void
    {
        $twilio = new Client($this->twilioSid, $this->twilioAuthToken);

        $twilio->messages
            ->create($to, // to
                [
                    "from" => $this->twilioFromNumber,
                    "body" => $message,
                ]
            );
    }
}
