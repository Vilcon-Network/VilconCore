<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SandhyR\VilconCore\arena\Arena;

class DuelTask extends Task{

    private Player $player1;
    private Player $player2;
    private bool $status;
    private int $timer;

    public function __construct(Player $player1, Player $player2){
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->status = true;
        $this->timer = 5;
        Arena::$duelTimer[$player1->getName()] = 0;
        Arena::$duelTimer[$player2->getName()] = 0;
    }

    public function onRun(): void
    {
        $player1 = $this->player1;
        $player2 = $this->player2;
        if($this->status){
            $player1->sendTitle($this->timer);
            $player2->sendTitle($this->timer);
            --$this->timer;
            if($this->timer <= 0){
                $player1->sendTitle("FIGHT!");
                $player2->sendTitle("FIGHT!");
                $this->status = false;
            }
        } else {
            ++Arena::$duelTimer[$player1->getName()];
            ++Arena::$duelTimer[$player2->getName()];
        }
    }
}