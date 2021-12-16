<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class RespawnTask extends Task{

    /** @var Player */
    private $player;

    /** @var int  */
    private $timer = 5;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        $player = $this->player;
        $player->sendTitle(TextFormat::RED . "YOU DIED!", TextFormat::YELLOW . "You will respawn in " . TextFormat::RED . $this->timer . TextFormat::YELLOW . " seconds!");
        if($this->timer <= 0){
            $player->sendTitle(TextFormat::BOLD . TextFormat::GREEN . "RESPAWNED");
            // TODO RESPAWN
            $this->getHandler()->cancel();
        }
        --$this->timer;
    }

}
