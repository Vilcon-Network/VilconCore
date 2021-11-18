<?php

namespace SandhyR\VilconCore\anticheat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class AntiCheatListener implements Listener{


    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $extradata = $player->getPlayerInfo()->getExtraData();
        $os = (int)$extradata["DeviceOS"];
        $model = (string)$extradata["DeviceModel"];
        if($os == 1){
            $devicemodel = explode(" ", $model);
            if(isset($devicemodel[0])){
                $model = strtoupper($devicemodel[0]);
                if($model !== $devicemodel[0]){
                    $event->getPlayer()->kick("Toolbox is not allowed");
                }

            }
        }
    }
}
