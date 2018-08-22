<?php

namespace ItemCooldown;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\utils\Config;
use ItemCooldown\CooldownTask;


class Main extends PluginBase implements Listener{

	private $a = [];


	public function onEnable(){
		$this->getLogger()->info(TF::GREEN . "ItemCooldown by CrackerFR");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this, 25), 25);
		$this->saveDefaultConfig();
		$this->cooldown = new Config($this->getDataFolder(). "cooldowns.yml", Config::YAML);
		if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
	}

	public function onDisable(){
		$this->cooldown->save();
	}
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$action = $event->getAction();
		$item = $player->getInventory()->getItemInHand()->getId();
		if($item == $this->getConfig()->get("item")){
			if($action == 1 or $action == 3){
			if(isset($this->a[strtolower($player->getName())])){
				$player->sendMessage(TF::RED. TF::ITALIC. "You will be able to use this item in ". $this->cooldown->get(strtolower($player->getName())). " seconds");
				$event->setCancelled();
		}else{
			$effect4 = Effect::getEffect($this->getConfig()->get("effect"));
            $player->addEffect(new EffectInstance($effect4, $this->getConfig()->get("seconds-effect")*20, $this->getConfig()->get("amplifier"), $this->getConfig()->get("particles-visible")));
			$this->newCooldown($player);
		  }
		}
	  }
	}

	public function newCooldown($player){
		$this->cooldown->set(strtolower($player->getName()), $this->getConfig()->get("seconds-cooldown"));
		$this->a[strtolower($player->getName())] = strtolower($player->getName());
		$this->cooldown->save();
	}

	public function timer(){
		foreach($this->cooldown->getAll() as $player => $time){
			$time--;
			$this->cooldown->set($player, $time);
			$this->cooldown->save();
			if($time == 0){
				$this->cooldown->remove($player);
			unset($this->a[$player]);
				$this->cooldown->save();
			}
		}
	}
}


?>