<?php
/**
 * redis命令管理，在国外开源的redis库上二次开发
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Charles Pick
 * @version $Id: PipiRedisCommand.php 8371 2013-04-02 08:34:00Z hexin $ 
 * @package 
 */
class PipiRedisCommand extends CConsoleCommand {

	public function actionSubscribe($args) {

		$redis = $this->getConnection();
		$channel = new PipiRedisChannel(array_shift($args), $redis);
		$channel->onReceiveMessage = 'receiveMessage';
		$channel->subscribe();
	}
	/**
	 * Gets the connection to redis
	 * @return PipiRedisConnection the connection to redis
	 */
	public function getConnection() {
		return Yii::app()->redis;
	}
}

function receiveMessage(CEvent $event) {
	$message = (object) json_decode($event->sender->getLastMessage());
	if (preg_match_all("/in (.*) \((.*)\)/",$message->message,$matches)) {
		foreach($matches[1] as $filename) {
			$line = array(
						$message->time,
						$message->category,
						"M",
						$filename
					);
			echo implode("|",$line)."\n";

		}
	}
}
		