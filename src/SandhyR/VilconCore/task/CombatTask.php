<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;
use SandhyR\VilconCore\PlayerManager;

class CombatTask extends Task{

    private Player $player1;
    private Player $player2;
    private EventListener $listener;

    public function __construct(Player $player1, Player $player2, EventListener $listener){
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->listener = $listener;
        PlayerManager::$iscombat = true;
        PlayerManager::$iscombat = true;

}

    public function onRun(): void
    {
        $listener = $this->listener;
        if ($this->player1->isOnline() and $this->player2->isOnline()) {
            if ($this->player1->isAlive() and $this->player2->isAlive()) {
                $worldname = $this->player1->getWorld()->getFolderName();
                $listener->timer($this->player1);
                $listener->timer($this->player2);
                if ($listener->getTimer($this->player1) <= 0 and $listener->getTimer($this->player2) <= 0) {
                    $listener->unsetTimer($this->player1);
                    $listener->unsetTimer($this->player2);
                    $listener->unsetDamager($this->player2);
                    $listener->unsetDamager($this->player1);
                    $this->player1->sendMessage(TextFormat::GREEN."You are not in combat now");
                    $this->player2->sendMessage(TextFormat::GREEN."You are not in combat now");
                    $this->getHandler()->cancel();
                }
                if ($worldname !== null) {
                    if ($worldname == Main::getInstance()->getLobby() or $this->player2->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $listener->unsetTimer($this->player1);
                        $listener->unsetTimer($this->player2);
                        $listener->unsetDamager($this->player2);
                        $listener->unsetDamager($this->player1);
                        $this->player1->sendMessage(TextFormat::GREEN."You are not in combat now");
                        $this->player2->sendMessage(TextFormat::GREEN."You are not in combat now");
                        $this->getHandler()->cancel();
                    }
                }
            } else {
                unset($listener->damager[$this->player1->getName()]);
                unset($listener->damager[$this->player2->getName()]);
                unset($listener->timer[$this->player1->getName()]);
                unset($listener->timer[$this->player2->getName()]);
                $this->player1->sendMessage(TextFormat::GREEN."You are not in combat now");
                $this->player2->sendMessage(TextFormat::GREEN."You are not in combat now");
                $this->getHandler()->cancel();
            }
        } else {
            unset($listener->damager[$this->player1->getName()]);
            unset($listener->damager[$this->player2->getName()]);
            unset($listener->timer[$this->player1->getName()]);
            unset($listener->timer[$this->player2->getName()]);
            $this->player1->sendMessage(TextFormat::GREEN."You are not in combat now");
            $this->player2->sendMessage(TextFormat::GREEN."You are not in combat now");
            $this->getHandler()->cancel();
        }
    }

    public function onCancel(): void
    {
       unset(PlayerManager::$playerstatus[$this->player1->getName()]);
       unset(PlayerManager::$playerstatus[$this->player2->getName()]);
    }
}
