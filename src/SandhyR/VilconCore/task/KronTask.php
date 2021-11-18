<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class KronTask extends Task{

    private Player $player;
    private int $timerkron = 100;

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function onRun(): void
    {
        $player = $this->player;
        if($player->isOnline()) {
            $player->sendMessage("Kron ya bang");
            $player->sendTitle("Kron ya bang");
            $player->sendPopup("Kron ya bang");
            --$this->timerkron;
            if($this->timerkron <= 0){
                $player->kick("Kron");
                $this->getHandler()->cancel();
            }
        } else {
            $this->getHandler()->cancel();
        }
    }
}