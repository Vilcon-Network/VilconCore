<?php

namespace SandhyR\VilconCore\anticheat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\Main;

class AntiCheatListener implements Listener{


    public function onLogin(PlayerLoginEvent $event){
        $player = $event->getPlayer();
        $extradata = $player->getPlayerInfo()->getExtraData();
        $os = (int)$extradata["DeviceOS"];
        $model = (string)$extradata["DeviceModel"];
        if($os == 1){
            $devicemodel = explode(" ", $model);
            if(isset($devicemodel[0])){
                $model = strtoupper($devicemodel[0]);
                if($model !== $devicemodel[0]){
                    foreach (Server::getInstance()->getOnlinePlayers() as $p) {
                        if(strtoupper(Main::getInstance()->rank[$p->getName()]) == "OWNER" or strtoupper(Main::getInstance()->rank[$p->getName()]) == "ADMIN"){
                            $p->sendMessage(TextFormat::RED . "STAFF > " . TextFormat::WHITE . $player->getName() . " Detected as Toolbox");
                        }
                        $event->getPlayer()->kick("Toolbox is not allowed");
                    }
                }

            }
        }
    }
}
