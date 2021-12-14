<?php

namespace SandhyR\VilconCore\guild;

use pocketmine\player\Player;

class GuildManager{

    /** @var array */
    public static $guild = [];

    public function getPlayerGuild(Player $player){
        $name = $player->getName();
        foreach (self::$guild as $guild){
            foreach ($guild as $key => $value){
                if($value["playername"] == $name){
                    return $value["guildname"];
                }
            }
        }
    }

    public function getPlayerGuildByName(string $name){
    }
}
