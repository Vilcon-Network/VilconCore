<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;

class AntiCheatTask extends Task{
    
    private Main $plugin;
    private Player $susplayer;
    private $suscps;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        $list = [];
            foreach (Server::getInstance()->getOnlinePlayers() as $p) {
                $list[] = $p;
                if (EventListener::getCps($p) >= 20) {
                    $this->susplayer = $p;
                    $this->suscps = EventListener::getCps($this->susplayer);
                    foreach ($list as $staff) {
                        if (strtoupper($this->plugin->rank[$p->getName()]) == "OWNER" or strtoupper($this->plugin->rank[$p->getName()]) == "ADMIN") {
                            $staff->sendMessage(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . " Detected as AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms" . ", " . EventListener::$device[$this->susplayer->getName()] . ", " . EventListener::$control
                                [$this->susplayer->getName()] . ")");
                            Server::getInstance()->getLogger()->info(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . " Detected as AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms" . ", " . EventListener::$device[$this->susplayer->getName()] . ", " . EventListener::$control
                                [$this->susplayer->getName()] . ")");
                        }
                    }
                    if (EventListener::getCps($p) >= 40) {
                        $this->susplayer = $p;
                        $this->suscps = EventListener::getCps($this->susplayer);
                        if (strtoupper($this->plugin->rank[$p->getName()]) == "OWNER" or strtoupper($this->plugin->rank[$p->getName()]) == "ADMIN") {
                            $staff->sendMessage(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . " Kicked by bot with reason AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms" . ", " . EventListener::$device[$this->susplayer->getName()] . ", " . EventListener::$control
                                [$this->susplayer->getName()] . ")");
                            Server::getInstance()->getLogger()->info(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $this->susplayer->getName() . " Kicked by bot with reason AutoClicker CPS: " . $this->suscps . " " . TextFormat::GREEN . "(" . $this->susplayer->getNetworkSession()->getPing() . "ms" . ", " . EventListener::$device[$this->susplayer->getName()] . ", " . EventListener::$control
                                [$this->susplayer->getName()] . ")");
                        }
                        $this->susplayer->kick("AutoClicker is not allowed");
                    }

                }
            }
    }
}
