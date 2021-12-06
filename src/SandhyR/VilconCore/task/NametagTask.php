<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;
use SandhyR\VilconCore\RankManager;

class NametagTask extends Task{

    private Player $player;
    private EventListener $listener;
    private $index = 0;

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
                if (isset(EventListener::$device[$player->getName()]) and isset(EventListener::$control[$player->getName()])) {
                    if ($cosmetic["equip"]["tags"] == "default") {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($player->getName() . "\n" . TextFormat::GRAY . EventListener::$device[$player->getName()] . " - " . EventListener::$control[$player->getName()]);
                        } else {
                            $player->setNameTag($player->getName() . " " . "[" . TextFormat::AQUA . round($player->getHealth(), 2) . TextFormat::WHITE . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . EventListener::getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    } else {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n " . /*substr(RankManager::getRankFormat(Main::getInstance()->rank[$player->getName()]), $this->index) . */ $player->getName() . "\n" . TextFormat::GRAY . EventListener::$device[$player->getName()] . " - " . EventListener::$control[$player->getName()]);
//                            ++$this->index;
//                            if($this->index > strlen(RankManager::getRankFormat(Main::getInstance()->rank[$player->getName()]))){
//                                $this->index = 0;
//                            }
                        } else {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . " " . "[" . TextFormat::AQUA . round($player->getHealth(), 2) . TextFormat::WHITE . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . EventListener::getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    }
                } else {
                    if ($cosmetic["equip"]["tags"] == "default") {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($player->getName() . "\n" . TextFormat::GRAY . "Unknown" . " - " . "Unknown");
                        } else {
                            $player->setNameTag($player->getName() . " " . "[" . TextFormat::AQUA . $player->getHealth() . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . EventListener::getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    } else {
                        if (!isset($this->listener->damager[$player->getName()])) {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . "\n" . TextFormat::GRAY . "Unknown" . " - " . "Unknown");
                        } else {
                            $player->setNameTag($cosmetic["equip"]["tags"] . "\n" . $player->getName() . " " . "[" . TextFormat::AQUA . $player->getHealth() . "]" . "\n" . TextFormat::AQUA . "CPS: " . TextFormat::RESET . EventListener::getCps($player) . " " . TextFormat::AQUA . "PING: " . TextFormat::RESET . $player->getNetworkSession()->getPing() . "ms");
                        }
                    }
                }
            }
        } else {
            unset(EventListener::$device[$player->getName()]);
            $this->getHandler()->cancel();
        }
    }
}
