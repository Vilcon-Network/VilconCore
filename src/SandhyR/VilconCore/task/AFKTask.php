<?php

namespace SandhyR\VilconCore\task;

use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\EventListener;

class AFKTask extends Task{

    private Player $player;

    public function __construct(Player $player){
        $this->player = $player;
        EventListener::$movementsession[$player->getName()] = 120;
    }

    public function onRun(): void
    {
        $player = $this->player;
        if (!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            if (EventListener::$movementsession[$player->getName()] <= 20) {
                $player->sendMessage(TextFormat::RED . "You will kicked if you not move in " . EventListener::$movementsession[$player->getName()] . " second");
            }
            if (EventListener::$movementsession[$player->getName()] <= 0) {
                $player->kick("AFK is not allowed");
            }
            --EventListener::$movementsession[$player->getName()];
        }
    }
}
