<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;

class NametagTask extends Task{

    private Player $player;
    private EventListener $listener;

        public function __construct(Player $player, EventListener $listener){
        $this->player = $player;
        $this->listener = $listener;
    }

    public function onRun(): void
    {
        $player = $this->player;

        if ($player->isOnline()) {
            if(isset(DatabaseControler::$cosmetic[$player->getName()])) {
                $cosmetic = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                if (isset($this->listener->device[$player->getName()]) and isset($this->listener->control[$player->getName()])) {
                    if ($cosmetic["equip"]["tags"] == "default") {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($player->getName() . "\n" . TextFormat::GRAY . $this->listener->device[$player->getName()] . " - " . $this->listener->control[$player->getName()]);
                        } else {
                            $player->setNameTag($player->getName() . " " . "[" . TextFormat::AQUA . round($player->getHealth(), 2) . TextFormat::WHITE . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . $this->listener->getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    } else {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n " . $player->getName() . "\n" . TextFormat::GRAY . $this->listener->device[$player->getName()] . " - " . $this->listener->control[$player->getName()]);
                        } else {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . " " . "[" . TextFormat::AQUA . round($player->getHealth(), 2) . TextFormat::WHITE . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . $this->listener->getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    }
                } else {
                    if ($cosmetic["equip"]["tags"] == "default") {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($player->getName() . "\n" . TextFormat::GRAY . "Unknown" . " - " . "Unknown");
                        } else {
                            $player->setNameTag($player->getName() . " " . "[" . TextFormat::AQUA . $player->getHealth() . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . $this->listener->getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    } else {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . "\n" . TextFormat::GRAY . "Unknown" . " - " . "Unknown");
                        } else {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . " " . "[" . TextFormat::AQUA . $player->getHealth() . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . $this->listener->getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    }
                }
            }
        } else {
            unset($this->listener->device[$player->getName()]);
            $this->getHandler()->cancel();
        }
    }
}
