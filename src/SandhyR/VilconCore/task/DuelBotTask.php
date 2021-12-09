<?php

namespace SandhyR\VilconCore\task;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\bot\EasyBot;
use SandhyR\VilconCore\bot\HackerBot;
use SandhyR\VilconCore\bot\HardBot;
use SandhyR\VilconCore\bot\MediumBot;
use SandhyR\VilconCore\PlayerManager;

class DuelBotTask extends Task{

    /** @var Player */
    private $player;

    /** @var int */
    private $id;

    /** @var bool */
    private $status;

    /** @var int  */
    private $timer = 5;

    public function __construct(Player $player, int $id)
    {
        $this->player = $player;
        $this->id = $id;
        $this->status = true;
        Arena::$duelTimer[$player->getName()] = 0;
    }

    public function onRun(): void
    {
        if($this->player->isOnline()){
            if($this->status){
                $this->player->sendTitle(TextFormat::RED . $this->timer);
                --$this->timer;
                if($this->timer <= 0){
                    $location = new Location(Arena::$posduel[1][0],Arena::$posduel[1][1], Arena::$posduel[1][2],$this->player->getWorld(), 0,0);
                    $this->player->sendTitle(TextFormat::GREEN . "FIGHT!");
                    switch ($this->id){
                        case PlayerManager::EASY_BOT:
                            $bot = new EasyBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::MEDIUM_BOT:
                            $bot = new MediumBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::HARD_BOT:
                            $bot = new HardBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::HACKER_BOT:
                            $bot = new HackerBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                    }
                    $this->status = false;
                }
            } else {
                ++Arena::$duelTimer[$this->player->getName()];
            }
        } else {
            $this->getHandler()->cancel();
        }
    }
}