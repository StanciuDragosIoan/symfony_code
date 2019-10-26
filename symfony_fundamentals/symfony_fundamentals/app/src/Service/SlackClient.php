<?php
    
namespace App\Service;
use Nexy\Slack\Client;
use App\Helper\LoggerTrait;

class SlackClient
{   

    use LoggerTrait;

    private $slack;



    public function __construct(Client $slack)
    {
        $this->slack = $slack;
    }




    public function sendMessage(string $from, string $msg)
    {       
            $this->logInfo('Beaming a message to slack...', [
                'message' => $msg
            ]);

            $SlackMsg = $this->slack->createMessage()
                ->from($from)
                ->withIcon(':ghost:')
                ->setText($msg)
            ;
            $this->slack->sendMessage($SlackMsg); 
    }
}