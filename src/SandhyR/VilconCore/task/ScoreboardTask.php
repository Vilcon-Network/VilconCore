<?php

namespace SandhyR\VilconCore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\Main;
use SandhyR\VilconCore\PlayerManager;
use Scoreboards\Scoreboards;

class ScoreboardTask  extends Task{

    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        $player = $this->player;
        if ($player->isOnline()) {
            $api = Scoreboards::getInstance();
            switch (PlayerManager::$playerstatus[$player->getName()]) {
                case PlayerManager::LOBBY;
            $api->new($player, "Lobby", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
            $api->setLine($player, 1 ,TextFormat::WHITE."Online ".TextFormat::AQUA. count(Server::getInstance()->getOnlinePlayers()));
            $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
            $api->setLine($player, 3 ," ");
            $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
            $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
            $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
            $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
            break;
                case PlayerManager::NODEBUFF_FFA:
                    $api->new($player, "NodebuffFFA", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
                    $api->setLine($player, 1 ,TextFormat::WHITE."Arena: " . TextFormat::AQUA . "Nodebuff");
                    $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
                    $api->setLine($player, 3 ," ");
                    $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
                    $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
                    $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
                    $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
                    break;
                case PlayerManager::FIST_FFA:
                    $api->new($player, "FistFFA", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
                    $api->setLine($player, 1 ,TextFormat::WHITE."Arena: " . TextFormat::AQUA . "Fist");
                    $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
                    $api->setLine($player, 3 ," ");
                    $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
                    $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
                    $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
                    $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
                    break;
                case PlayerManager::SUMO_FFA:
                    $api->new($player, "SumoFFA", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
                    $api->setLine($player, 1 ,TextFormat::WHITE."Arena: " . TextFormat::AQUA . "Sumo");
                    $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
                    $api->setLine($player, 3 ," ");
                    $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
                    $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
                    $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
                    $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
                    break;
                case PlayerManager::RESISTANCE_FFA:
                    $api->new($player, "ResistanceFFA", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
                    $api->setLine($player, 1 ,TextFormat::WHITE."Arena: " . TextFormat::AQUA . "Resistance");
                    $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
                    $api->setLine($player, 3 ," ");
                    $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
                    $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
                    $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
                    $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
                    break;
                case PlayerManager::COMBO_FFA:
                    $api->new($player, "ComboFFA", TextFormat::BOLD . TextFormat::AQUA . "Vilcon");
                    $api->setLine($player, 1 ,TextFormat::WHITE."Arena: " . TextFormat::AQUA . "Combo");
                    $api->setLine($player, 2 ,TextFormat::WHITE."Ping: " . TextFormat::AQUA. $player->getNetworkSession()->getPing());
                    $api->setLine($player, 3 ," ");
                    $api->setLine($player, 4 ,TextFormat::WHITE."Kill: ".TextFormat::AQUA. DatabaseControler::$kill[$player->getName()]);
                    $api->setLine($player, 5 ,TextFormat::WHITE."Death: ".TextFormat::AQUA. DatabaseControler::$death[$player->getName()]);
                    $api->setLine($player, 6 ,TextFormat::WHITE."KDR: ".TextFormat::AQUA. round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()], 2));
                    $api->setLine($player, 7 ,TextFormat::AQUA . "play.vilconmc.net");
                    break;
            }
        } else {
            $this->getHandler()->cancel();
        }
    }

    public static function intToString(int $int): string{
        $mins = floor($int / 60);
        $seconds = floor($int % 60);
        return (($mins < 10 ? "0" : "") . $mins . ":" . ($seconds < 10 ? "0" : "") . $seconds);
    }
}