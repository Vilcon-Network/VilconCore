<?php

namespace SandhyR\VilconCore\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\Main;

class SetRankCommand extends Command{

    private Main $plugin;

    public function __construct(string $name, string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            if (count($args) >= 2) {
                if (strtoupper($args[1]) == "VIP" or strtoupper($args[1]) == "VIP+" or strtoupper($args[1]) == "MVP" or strtoupper($args[1]) == "MVP+" or strtoupper($args[1]) == "MVP++" or strtoupper($args[1]) == "OWNER" or strtoupper($args[1]) == "ADMIN" or strtoupper($args[1]) == "DEFAULT") {
                    DatabaseControler::setRanks($args[0], strtoupper($args[1]));
                    $sender->sendMessage("Succesfuly set rank $args[0]");
                    if (isset($this->plugin->rank[$args[0]])) {
                        $this->plugin->rank[$args[0]] = $args[1];
                    }
                } else {
                    $sender->sendMessage("Usage /setrank <playername> <rank>");
                }
            }
        }
}
}
