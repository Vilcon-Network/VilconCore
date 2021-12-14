<?php

namespace SandhyR\VilconCore;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use SandhyR\VilconCore\anticheat\AntiCheatListener;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\ArenaResetter;
use SandhyR\VilconCore\command\SetRankCommand;
use SandhyR\VilconCore\database\Database;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\task\AntiCheatTask;
use SandhyR\VilconCore\task\AsyncLoadGuildTask;
use SandhyR\VilconCore\task\CombatTask;
use SandhyR\VilconCore\arena\KitManager;

class Main extends PluginBase{


    /** @var Main $instance */
    public static $instance;
    public $config;
    public $rank = [];

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "capes");
        $this->checkRequirement();
        @mkdir(Server::getInstance()->getDataPath() . "worldsbackup");
        $this->saveDefaultConfig();
        $this->getScheduler()->scheduleRepeatingTask(new AntiCheatTask($this), 20);
        $capes = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if(is_array($capes->get("standard_capes"))) {
            foreach ($capes->get("standard_capes") as $cape) {
                $this->saveResource("$cape.png");
            }
            $capes->set("standard_capes", "done");
            $capes->save();
        }
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiCheatListener(), $this);
        $this->getServer()->getAsyncPool()->submitTask(new AsyncLoadGuildTask($this));
        $this->initCommand();
        $this->initDatabase();
        try {
            $this->getServer()->getWorldManager()->loadWorld($this->getLobby());
            $this->getServer()->getWorldManager()->loadWorld("nodebuff");
//            $this->getServer()->getWorldManager()->loadWorld("combo");
            $this->getServer()->getWorldManager()->loadWorld("fist");
            $this->getServer()->getWorldManager()->loadWorld("sumo");
            $this->getServer()->getWorldManager()->loadWorld("gapple");
            $this->getServer()->getWorldManager()->loadWorld("resistance");
            $this->getServer()->getWorldManager()->loadWorld("kron");
            $this->getServer()->getWorldManager()->loadWorld("duel");
        } catch (\InvalidArgumentException $exception){
            Server::getInstance()->getLogger()->critical($exception->getMessage());
        }
        Arena::$unrankqueue["nodebuff"] = [];
        Arena::$unrankqueue["fist"] = [];
        Arena::$unrankqueue["boxing"] = [];
        Arena::$unrankqueue["voidfight"] = [];
        Arena::$unrankqueue["gapple"] = [];
        Arena::$unrankqueue["sumo"] = [];
        Arena::$unrankqueue["resistance"] = [];
        Arena::$rankqueue["nodebuff"] = [];
        Arena::$rankqueue["fist"] = [];
        Arena::$rankqueue["boxing"] = [];
        Arena::$rankqueue["voidfight"] = [];
        Arena::$rankqueue["gapple"] = [];
        Arena::$rankqueue["sumo"] = [];
        Arena::$rankqueue["resistance"] = [];
        Arena::$match["rank"] = [];
        Arena::$match["unrank"] = [];
        KitManager::$kit["nodebuff"] = [];
        KitManager::$kit["combo"] = [];
        KitManager::$kit["builduhc"] = [];
        KitManager::$kit["voidfight"] = [];
        KitManager::$kit["blockin"] = []; 
    }

    /** @return Main */
    public static function getInstance(): Main{
        return self::$instance;
    }

    public function initDatabase(){
        DatabaseControler::init();
    }

    public function initCommand()
    {
        $this->getServer()->getCommandMap()->register("setRank", new SetRankCommand("setrank", "Setrank Player", $this));
    }

    public function getLobby(){
        return $this->config->get("lobbyname");
    }

    public function antiInterruptTask(Player $player, Player $player2, EventListener $listener){
        $this->getScheduler()->scheduleRepeatingTask(new CombatTask($player, $player2, $listener), 20);
    }

    public function onDisable(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $playername = $player->getName();
                $nodebuffkit = KitManager::$kit["nodebuff"][$player->getName()];
            $combokit = KitManager::$kit["combo"][$player->getName()];
            $builduhc = KitManager::$kit["builduhc"][$player->getName()];
            $voidfight = KitManager::$kit["voidfight"][$player->getName()];
            $blockin = KitManager::$kit["blockin"][$player->getName()];
            Database::getDatabase()->query("UPDATE playerkit SET nodebuffkit='$nodebuffkit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET combokit='$combokit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET builduhckit='$builduhc' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET voidfightkit='$voidfight' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET blockinkit='$blockin' WHERE username='$playername'");
            DatabaseControler::setKill($player, DatabaseControler::$kill[$player->getName()]);
            DatabaseControler::setDeath($player, DatabaseControler::$death[$player->getName()]);
            DatabaseControler::setElo($player, DatabaseControler::$elo[$player->getName()]);
            DatabaseControler::setCoin($player, DatabaseControler::$coins[$player->getName()]);
            DatabaseControler::setCosmetic($player, DatabaseControler::$cosmetic[$player->getName()]);
            }
        try {
            ArenaResetter::$index["voidfight"] = 1;
            foreach (range(1, ArenaResetter::$index["voidfight"]) as $item) {
                ArenaResetter::removeWorld("voidfight" . $item);
            }
        } catch (\ErrorException|\UnexpectedValueException $exception) {

        }
    }

    public function checkRequirement()
    {
        if (!file_exists(Main::getInstance()->getDataFolder() . "steve.png") || !file_exists(Main::getInstance()->getDataFolder() . "steve.json") || !file_exists(Main::getInstance()->getDataFolder() . "config.yml")) {
            if (file_exists(str_replace("config.yml", "", Main::getInstance()->getResources()["config.yml"]))) {
                $var = new SkinManager();
                $var->recurse_copy(str_replace("config.yml", "", Main::getInstance()->getResources()["config.yml"]), Main::getInstance()->getDataFolder());
            }
        }
    }
}
