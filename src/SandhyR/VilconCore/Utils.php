<?php

namespace SandhyR\VilconCore;

use pocketmine\player\Player;
use pocketmine\world\sound\AnvilFallSound;

class Utils{

    public static function addSound(Player $player){
        $player->getWorld()->addSound($player->getPosition()->asVector3(), new AnvilFallSound());
    }
}