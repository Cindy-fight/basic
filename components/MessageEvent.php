<?php

namespace app\components;

use yii\base\Component;
use yii\base\Event;
use Codeception\Lib\Console\Message;

class MessageEvent extends Event
{
	public $message;
	
}

class Mailer extends Component
{
	const EVENT_MESSAGE_SENT = 'messageSent';
	
	public function send($message){
		
		$event = new MessageEvent();
		$event->message = $message;
		$this->trigger(self::EVENT_MESSAGE_SENT, $event);
	}
}