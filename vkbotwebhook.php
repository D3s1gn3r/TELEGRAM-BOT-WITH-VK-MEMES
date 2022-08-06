<?php

    require "config/config.php";
    require 'rb.php';
    require 'database/db.php';
    require_once "vendor/autoload.php";
    
    $bot = new \TelegramBot\Api\Client(BOT_TOKEN);

    $bot->command('start', function ($message) use ($bot) {
            $books = R::find( 'allowedchatids', ' allowedchatid LIKE ? ', [ $message->getChat()->getId() ] );
		    if(!empty($books)){
                if(empty(R::find( 'chatids', ' chatid LIKE ? ', [ $message->getChat()->getId() ] ))){
                    $telid = $message->getChat()->getId();
                    $book = R::dispense( 'chatids' );
                    $book->chatid = $telid;
                    $id = R::store( $book );
                }
            }
    });
  
    $bot->run();  
?>
