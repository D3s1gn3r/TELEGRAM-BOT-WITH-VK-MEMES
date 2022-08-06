<?php 
	
    require "config/config.php";
	require 'rb.php';
	require 'database/db.php';
    require_once "vendor/autoload.php";

	$bot = new \TelegramBot\Api\Client($BOT_TOKEN);

	// vk groups id
    $groupids = R::getAll( 'SELECT * FROM groupids' );

    // telegram chats id
    $telegramchats = R::getAll( 'SELECT * FROM chatids' );

    function sendpictures($url, $telegramchats, $bot){
    	foreach ($telegramchats as $chatid) {
			try{

				$media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
				$media->addItem(new TelegramBot\Api\Types\InputMedia\InputMediaPhoto($url));
				$bot->sendMediaGroup($chatid['chatid'], $media);
		    } catch (Exception $e) {
		       	R::exec('DELETE FROM `chatids` WHERE `chatid` = ?', array(
		            $chatid['chatid']
		        ));
		    }

		}
    }

	foreach ($groupids as $gid) {
		$link = 'https://api.vk.com/method/photos.get?owner_id=' . $gid['groupid'] . '&album_id=wall&count=10&rev=1&access_token=' . VK_TOKEN . '&v=5.92';
		$content = file_get_contents($link);
		$content = json_decode($content, true);

		//https://api.vk.com/method/photos.get?owner_id=-45595714&album_id=wall&count=3&rev=1&access_token=7580f47b7580f47b7580f47b1575ec5e0f775807580f47b28ed9845deec5704000f89bc&v=5.92
		foreach ($content['response']['items'] as $value) {
	    	foreach ($value['sizes'] as $sizes) {
	    		if($sizes['type'] == 'x'){
                    $pieces = explode("/", $sizes['url']);
    			    $books = R::find( 'pictures', ' picurl LIKE ? ', [ $pieces[6] ] );
    			    if(empty($books)){
    			    	$book = R::dispense( 'pictures' );
	    				$book->picurl = $pieces[6];
	    				$id = R::store( $book );
	    				sendpictures($sizes['url'], $telegramchats, $bot);
	    				sleep(6);
    			    }
	    		}
	    	}
		}
	    
	}

?>

