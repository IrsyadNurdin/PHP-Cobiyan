<?php
require __DIR__ . '/vendor/autoload.php';
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
 
// set false for production
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = "9t/WuPM+kx1JPnUlASx+M9FnXxbSiHzB7TnG/nNQohgLN3yTnWJD7RFSpt2BGQKpXHSME3GdgtfbtoHImKL0amQxZQbXugmsDc8GiPqjAFEGuMtn7OAufDtmpd2AMUCbUf+MTHrVBF8r92qXNKUemAdB04t89/1O/w1cDnyilFU=";
$channel_secret = "ebefcc9d9a815204c5ed9ca95e69f755";
 
// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
 
$configs =  [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);
 
// buat route untuk url homepage
$app->get('/', function($req, $res)
{
  echo "Welcome at Slim Framework";
});
 
// buat route untuk webhook
$app->post('/webhook', function ($request, $response) use ($bot, $pass_signature, $httpClient)
{
    // get request body and line signature header
    $body        = file_get_contents('php://input');
    $signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
 
    // log body and signature
    file_put_contents('php://stderr', 'Body: '.$body);
 
    if($pass_signature === false)
    {
        // is LINE_SIGNATURE exists in request header?
        if(empty($signature)){
            return $response->withStatus(400, 'Signature not set');
        }
 
        // is this request comes from LINE?
        if(! SignatureValidator::validateSignature($body, $channel_secret, $signature)){
            return $response->withStatus(400, 'Invalid signature');
        }
    }
 
    // kode aplikasi nanti disini

    $data = json_decode($body, true);
    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if ($event['type'] == 'message')
            {
                if($event['message']['type'] == 'text')
                {
                    $usermessage = ($event['message']['text']);

                    if($usermessage == '/menu') 
                    {

                        $flexTemplate = file_get_contents("ticket.json"); // template flex message
                            $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                'replyToken' => $event['replyToken'],
                                'messages'   => [
                                    [
                                        'type'     => 'flex',
                                        'altText'  => 'Test Flex Message',
                                        'contents' => json_decode($flexTemplate)
                                    ]
                                ],
                            ]);    

                    } 
                    else if($usermessage == '/detail') 
                    {

                        $flexTemplate = file_get_contents("detail_payment.json"); // template flex message
                            $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                'replyToken' => $event['replyToken'],
                                'messages'   => [
                                    [
                                        'type'     => 'flex',
                                        'altText'  => 'Test Flex Message',
                                        'contents' => json_decode($flexTemplate)
                                    ]
                                ],
                            ]);    

                    } 
                    else if($usermessage == '/berita') 
                    {

                        $flexTemplate = file_get_contents("detail_payment.json"); // template flex message
                            $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                'replyToken' => $event['replyToken'],
                                'messages'   => [
                                    [
                                        'type'     => 'flex',
                                        'altText'  => 'Test Flex Message',
                                        'contents' => json_decode($flexTemplate)
                                    ]
                                ],
                            ]);    

                    } 
                    else 
                    {
                        // send same message as reply to user
                        $result = $bot->replyText($event['replyToken'], $event['message']['text']);
         
                        // or we can use replyMessage() instead to send reply message
                        // $textMessageBuilder = new TextMessageBuilder($event['message']['text']);
                        // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
         
                        return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }    
                }
            }
        } 
    }
 
});
 
$app->run();