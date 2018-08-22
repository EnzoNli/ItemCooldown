<?php
declare(strict_types=1);

namespace ItemCooldown;

use ItemCooldown\Main;
use pocketmine\scheduler\Task;

class CooldownTask extends Task{

	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onRun(int $tick){
		$this->plugin->timer();
	}
}