<?php

namespace SandhyR\VilconCore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\PlayerManager;

class HubCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "")
    {
        parent::__construct($name, $description);
        parent::setAliases(["hub"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $combat = PlayerManager::$iscombat[$sender->getName()] ?? false;
            if(!$combat){
                EventListener::teleportLobby($sender);
            } else {
                $sender->sendMessage(TextFormat::RED . "You cant go to lobby when combat!");
            }
        }
    }
}