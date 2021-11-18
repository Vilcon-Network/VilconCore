<?php

namespace SandhyR\VilconCore;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\anticheat\AntiCheatListener;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\ArenaResetter;
use SandhyR\VilconCore\command\SetRankCommand;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\task\AntiCheatTask;
use SandhyR\VilconCore\task\CombatTask;
use SandhyR\VilconCore\arena\KitManager;

class Main extends PluginBase{

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
        @mkdir(Server::getInstance()->getDataPath() . "worldsbackup");
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiCheatListener(), $this);
        $this->initCommand();
        $this->initDatabase();
        try {
            $this->getServer()->getWorldManager()->loadWorld($this->getLobby());
            $this->getServer()->getWorldManager()->loadWorld("nodebuff");
            $this->getServer()->getWorldManager()->loadWorld("combo");
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
        Arena::$unrankqueue["combo"] = [];
        Arena::$unrankqueue["gapple"] = [];
        Arena::$unrankqueue["sumo"] = [];
        Arena::$unrankqueue["resistance"] = [];
        Arena::$rankqueue["nodebuff"] = [];
        Arena::$rankqueue["fist"] = [];
        Arena::$rankqueue["boxing"] = [];
        Arena::$rankqueue["voidfight"] = [];
        Arena::$rankqueue["combo"] = [];
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

    public static function getInstance(){
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

    public function antiCheatTask(EventListener $listener){
        $this->getScheduler()->scheduleRepeatingTask(new AntiCheatTask($listener, $this), 1);
    }

    public function onDisable(): void
    {
        try {
            ArenaResetter::removeWorld("voidfight");
        } catch (\UnexpectedValueException $exception){
        }
    }
}
