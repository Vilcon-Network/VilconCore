<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;

class AntiCheatTask extends Task{

    private EventListener $listener;
    private Main $plugin;
    private Player $susplayer;
    private $suscps;

    public function __construct(EventListener $listener, Main $plugin){
        $this->listener = $listener;
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p){
            if($this->listener->getCps($p) >= 20){
                $this->susplayer = $p;
                $this->suscps = $this->listener->getCps($this->susplayer);
                if(strtoupper($this->plugin->rank[$p->getName()]) == "OWNER" or strtoupper($this->plugin->rank[$p->getName()]) == "ADMIN"){
                    $p->sendMessage(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . "Detected as AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms". ", " . $this->listener->device[$this->susplayer->getName()] . ", " . $this->listener->control[$this->susplayer->getName()] .")");
                    Server::getInstance()->getLogger()->info(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . "Detected as AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms". ", " . $this->listener->device[$this->susplayer->getName()] . ", " . $this->listener->control[$this->susplayer->getName()] .")");
                }
            }
        }
    }
}
