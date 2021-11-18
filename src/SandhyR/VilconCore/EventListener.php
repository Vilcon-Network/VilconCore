<?php

namespace SandhyR\VilconCore;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\SplashPotion;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\KitManager;
use SandhyR\VilconCore\database\Database;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\task\KronTask;
use SandhyR\VilconCore\task\NametagTask;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use SandhyR\VilconCore\task\ScoreboardTask;

class EventListener implements Listener
{

    public $damager = [];
    public $timer = [];
    private $delay = [];
    private $clicks = [];
    public $device = [];
    public $control = [];
    public $plugin;
    private $lastchat = [];
    private $chatdelay = [];
    public static $autogg = [];
    public static $autoez = [];
    public static $cpspopup = [];

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $kron = ["largeseconds", "krivgalaxy", "teh kotak1846", "justintanjaya", "necurine", "ipqtan", "kynxh", "ryankebo", "lorddaichi1", "idxchi", "generatoryt12", "generatorsyt12", "hifzaly", "akupatan123"];
        $player = $event->getPlayer();
        if (in_array(strtolower($player->getName()), $kron)) {
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("kron")->getSafeSpawn());
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KronTask($player), 0);
            Server::getInstance()->broadcastMessage("Kron datang!!");
        } else {
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        }
        $player->getInventory()->clearAll();
        self::sendItem($player);
        $this->clicks[$event->getPlayer()->getName()] = [];
        $this->initJoin($player);
        if (count(Server::getInstance()->getOnlinePlayers()) - 1 == 0) {
            Main::getInstance()->antiCheatTask($this);
        }
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new NametagTask($event->getPlayer(), $this), 20);
        PlayerManager::$playerstatus[$event->getPlayer()->getName()] = PlayerManager::LOBBY;
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask($event->getPlayer()), 20);
    }

    public function onAttack(EntityDamageByEntityEvent $event)
    {
        $manager = new KitManager();
        if ($event->getEntity() instanceof Player && $event->getDamager() instanceof Player) {
            $player = $event->getEntity();
            $killer = $event->getDamager();
            if ($player instanceof Player and $killer instanceof Player) {
                $kron = ["largeseconds", "krivgalaxy", "teh kotak1846", "justintanjaya", "necurine", "ipqtan", "kynxh", "ryankebo", "lorddaichi1", "idxchi"];
                if (in_array(strtolower($killer->getName()), $kron)) {
                    $event->setBaseDamage(0.0);
                    $event->setKnockBack(0.0);
                }
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby() or $killer->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    $event->cancel();
                }
                if (!isset($this->damager[$player->getName()]) and !isset($this->damager[$killer->getName()]) and !$event->isCancelled()) {
                    $this->setEnemy($player, $killer);
                    $this->setTimer($player, $killer);
                    $player->sendMessage("You are in combat now");
                    $killer->sendMessage("You are in combat now");
                    Main::getInstance()->antiInterruptTask($player, $killer, $this);
                } elseif (isset($this->damager[$player->getName()]) and !isset($this->damager[$killer->getName()])) {
                    $event->cancel();
                    $killer->sendMessage("Interrupting is not allowed");
                } elseif (!isset($this->damager[$player->getName()]) and isset($this->damager[$killer->getName()])) {
                    $event->cancel();
                    $killer->sendMessage("Your enemy is " . $this->damager[$killer->getName()]);
                } elseif (isset($this->damager[$player->getName()])) {
                    if ($killer->getName() !== $this->damager[$player->getName()]) {
                        $event->cancel();
                        $killer->sendMessage("Interrupting is not allowed!");
                    } else {
                        $this->setTimer($player, $killer);
                    }
                }
                if ($event->getBaseDamage() >= $player->getHealth() - 1.0) {
                    if(!Arena::isMatch($player) and !Arena::isMatch($killer)){
                        $this->teleportLobby($player);
                        $worldname = $killer->getWorld()->getFolderName();
                        $finalhealth = $killer->getHealth();
                        $weapon = $killer->getInventory()->getItemInHand()->getName();
                        $playername = $player->getDisplayName();
                        $killername = $killer->getDisplayName();
                        $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                        $potsA = 0;
                        $potsB = 0;
                        foreach ($player->getInventory()->getContents() as $pots) {
                            if ($pots instanceof SplashPotion) ++$potsA;
                        }
                        foreach ($killer->getInventory()->getContents() as $pots) {
                            if ($pots instanceof SplashPotion) ++$potsB;
                        }
                        if ($killer->getWorld()->getFolderName() == "nodebuff" or $killer->getWorld()->getFolderName() == "nodebuff-low" or $killer->getWorld()->getFolderName() == "nodebuff-java") {
                            $dm = $player->getDisplayName() . " §6[" . $potsA . " Pots] §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getDisplayName() . " §6[" . $potsB . " Pots - " . $finalhealth . " HP]";
                        } else {
                            $dm = $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getDisplayName() . " §6[" . $finalhealth . " HP]";
                        }
                        switch ($worldname) {
                            case "nodebuff":
                                $manager->sendKit($killer, PlayerManager::NODEBUFF_FFA);
                                break;
                            case "fist":
//                                $event->setKnockBack();
                                $manager->sendKit($killer, PlayerManager::FIST_FFA);
                                break;
                            case "combo":
                                $manager->sendKit($killer, PlayerManager::COMBO_FFA);
                                break;
                            case "sumo":

                        }
                        if(self::$autoez[$killer->getName()] == 1){
                            $killer->chat("ez");
                        }
                        if(self::$autogg[$killer->getName()] == 1){
                            $killer->chat("gg");
                        }
                        Server::getInstance()->broadcastMessage($dm);
                        ++DatabaseControler::$kill[$killer->getName()];
                        ++DatabaseControler::$death[$player->getName()];
                        LevelManager::addExp($killer, mt_rand(20, 50));
                        Utils::addSound($killer);
                    } else {
                        if ($event->getBaseDamage() >= $player->getHealth() - 1.0) {
                            if (Arena::isRankDuel($player) and Arena::isRankDuel($killer)) {
                                $this->addEloToProperty($killer, mt_rand(10, 25));
                            }
                            $this->teleportLobby($player);
                            $killer->sendTitle("VICTORY");
                            $killer->sendMessage("Winner: " . $killer->getName() . "\n" . "Loser: " . $player->getName());
                            $player->sendMessage("Winner: " . $killer->getName() . "\n" . "Loser: " . $player->getName());
                            $this->teleportLobby($killer);
                            Arena::unsetMatch($player);
                        }
                    }
                }
            }
            $player->sendMessage("Your healt:" . $player->getHealth());
            $player->sendMessage("Base Damage:" . $event->getBaseDamage());
            $killer->sendMessage("Your healt:" . $killer->getHealth());
            $killer->sendMessage("Base Damage:" . $event->getBaseDamage());
        }
    }
    public static function sendItem(Player $player)
    {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $item = new ItemFactory();
        $player->getInventory()->setItem(0, $item->get(ItemIds::DIAMOND_SWORD)->setCustomName("FFA"));
        $player->getInventory()->setItem(1, $item->get(ItemIds::IRON_SWORD)->setCustomName("Duels"));
        $player->getInventory()->setItem(2, $item->get(ItemIds::GOLD_SWORD)->setCustomName("Self Practice"));
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        switch ($id) {
            case ItemIds::DIAMOND_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if(!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->ffaForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }

                }
                break;
            case ItemIds::IRON_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if(!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->duelsForm($player);
                        $this->delay[$player->getName()] = time();
                    }  else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::GOLD_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if(!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->botForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::REDSTONE:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    Arena::unsetQueue($player);
                    $player->getInventory()->clearAll();
                    self::sendItem($player);
                }
        }
    }

    public function unsetDamager(Player $player){
        unset($this->damager[$player->getName()]);
    }

    public function getTimer(Player $player){
        return $this->timer[$player->getName()];
    }

    public function timer(Player $player){
        --$this->timer[$player->getName()];
    }

    public function unsetTimer(Player $player){
        unset($this->timer[$player->getName()]);
    }

    public function setEnemy(Player $player, Player $enemy){
        $this->damager[$player->getName()] = $enemy->getName();
        $this->damager[$enemy->getName()] = $player->getName();
    }

    public function setTimer(Player $player, Player $enemy){
        $this->timer[$player->getName()] = 15;
        $this->timer[$enemy->getName()] = 15;
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        unset($this->clicks[$event->getPlayer()->getName()]);
        $event->getPlayer()->getInventory()->clearAll();
        if(isset($this->damager[$event->getPlayer()->getName()])){
            $damager = Server::getInstance()->getPlayerExact($this->damager[$event->getPlayer()->getName()]);
            if($damager->isOnline()) {
                ++DatabaseControler::$kill[$damager->getName()];
                $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                Server::getInstance()->broadcastMessage($player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $damager->getName() . " §6[" . $damager->getHealth() . " HP]");
            } else {
                ++DatabaseControler::$kill[$this->damager[$player->getName()]];
            }
            ++DatabaseControler::$death[$player->getName()];
            LevelManager::addExp($damager, mt_rand(20, 50));
        }
        $nodebuffkit = KitManager::$kit["nodebuff"][$player->getName()];
        $combokit = KitManager::$kit["combo"][$player->getName()];
        $builduhc = KitManager::$kit["builduhc"][$player->getName()];
        $voidfight = KitManager::$kit["voidfight"][$player->getName()];
        $blockin = KitManager::$kit["blockin"][$player->getName()];
        $playername = $player->getName();
        Database::getDatabase()->query("UPDATE playerkit SET nodebuffkit='$nodebuffkit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET combokit='$combokit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET builduhckit='$builduhc' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET voidfightkit='$voidfight' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET blockinkit='$blockin' WHERE username='$playername'");
        unset(LevelManager::$level[$player->getName()]);
        unset(PlayerManager::$playerstatus[$player->getName()]);
        unset($this->plugin->rank[$player->getName()]);
        if(Arena::isMatch($player)){
            foreach (Arena::$match as $index => $matchs){
                foreach ($matchs as $indeks => $match){
                    if($indeks == $player->getName()){
                        $enemy = Server::getInstance()->getPlayerExact($match);
                        if($enemy->isOnline()){
                            $enemy->sendTitle("VICTORY!");
                        }
                    }
                }
            }
        }
        Arena::unsetQueue($player);
        Arena::unsetMatch($player);
        DatabaseControler::setKill($player, DatabaseControler::$kill[$player->getName()]);
        DatabaseControler::setDeath($player, DatabaseControler::$death[$player->getName()]);
        DatabaseControler::setElo($player, DatabaseControler::$elo[$player->getName()]);
        unset(PlayerManager::$playerstatus[$player->getName()]);
    }

    public function addClick(Player $player){
        array_unshift($this->clicks[$player->getName()], microtime(true));
        if(count($this->clicks[$player->getName()]) >= 100){
            array_pop($this->clicks[$player->getName()]);
        }
        if(self::$cpspopup[$player->getName()] == 1) {
            $player->sendTip(TextFormat::AQUA . "CPS: " . TextFormat::RESET . $this->getCps($player));
        }
    }

    public function onPacketReceive(DataPacketReceiveEvent $event){
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if($packet instanceof InventoryTransactionPacket){
            if($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->addClick($event->getOrigin()->getPlayer());
            }
        }
        if($packet instanceof LevelSoundEventPacket and $packet->sound == 42){
                $this->addClick($player);
        }
    }

    public function getCps(Player $player, float $deltaTime=1.0, int $roundPrecision=1):float{
        if(empty($this->clicks[$player->getName()])){
            return 0.0;
        }
        $mt=microtime(true);
        return round(count(array_filter($this->clicks[$player->getName()], static function(float $t) use ($deltaTime, $mt):bool{
                return ($mt - $t) <= $deltaTime;
            })) / $deltaTime, $roundPrecision);
    }

    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
         if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
        }
         if($event->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
             if($entity instanceof Player) {
                 if ($event->getEntity()->getWorld()->getFolderName() == "combo" or Arena::isCombo($entity)) {
                     $event->uncancel();
                 }
             }
    }
    }

    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        if($player->getPosition()->asVector3()->getY() <= 2){
            if($player->getWorld()->getFolderName() == "sumo") {
                if (isset($this->damager[$player->getName()])) {
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                    $killer = $this->damager[$player->getName()];
                    Server::getInstance()->broadcastMessage($player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer . " §6[" . "20" . " HP]");
                    $this->teleportLobby($player);
                    if(Server::getInstance()->getPlayerExact($killer)->isOnline()) {
                        $killer = Server::getInstance()->getPlayerExact($this->damager[$player->getName()]);
                        ++DatabaseControler::$kill[$killer->getName()];
                    } else {
                        if (isset($this->damager[$player->getName()])) {
                            ++DatabaseControler::$kill[$this->damager[$player->getName()]];
                        }
                    }
                    ++DatabaseControler::$death[$player->getName()];
                    LevelManager::addExp($killer, mt_rand(20, 50));
                } else {
                    Server::getInstance()->broadcastMessage($player->getName() . " fell into void");
                    $this->teleportLobby($player);
                }
            }
        }
    }

    public function teleportLobby(Player $player){
        $player->setHealth(20);
        $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        $player->getInventory()->clearAll();
        self::sendItem($player);
    }

    public function initJoin(Player $player){
        $rank = DatabaseControler::getRanks($player);
        $this->plugin->rank[$player->getName()] = $rank;
        LevelManager::$level[$player->getName()] = DatabaseControler::getLevel($player);
        $extradata = $player->getNetworkSession()->getPlayerInfo()->getExtraData();
        $os = ["Unknown", "Android", "iOS", "macOS", "FireOS", "GearVR", "HoloLens", "Win10", "Windows", "Dedicated", "Orbis", "PS4", "Nintendo Switch", "Xbox One"];
        $control = ["Unknown", "Mouse", "Touch", "Controller"];
        try {
            $this->control[$player->getName()] = $control[$extradata["CurrentInputMode"]];
        } catch (\ErrorException $exception){

        }
        $this->device[$player->getName()] = $os[$extradata["DeviceOS"]];
    }

    public function onChat(PlayerChatEvent $event)
    {

        $player = $event->getPlayer();
        if(isset(KitManager::$setupkit[$player->getName()])){
            if(strtolower($event->getMessage()) == "y"){
                KitManager::saveKit($player, KitManager::$setupkit[$player->getName()]);
                $event->cancel();
                return;
            }
            if(strtolower($event->getMessage()) == "n"){
                $player->getInventory()->clearAll();
                self::sendItem($player);
                $event->cancel();
            }
        }
        if(isset($this->lastchat[$player->getName()])) {
            if($event->getMessage() == $this->lastchat[$player->getName()]){
                $event->cancel();
                $player->sendMessage("Spam is not allowed");
            }
            $this->lastchat[$player->getName()] = $event->getMessage();
        } else {
            $this->lastchat[$player->getName()] = $event->getMessage();
        }
        if(strtoupper($this->plugin->rank[$player->getName()]) == "DEFAULT") {
            if (isset($this->chatdelay[$player->getName()])) {
                if ($this->chatdelay[$player->getName()] + 3 < time()) {
                    $this->chatdelay[$player->getName()] = time();
                } else {
                    if (!$event->isCancelled()) {
                        $event->cancel();
                        $player->sendMessage("Please wait " . $this->chatdelay[$player->getName()] + 3 - time() . " Second");
                    }
                }
            } else {
                $this->chatdelay[$player->getName()] = time();
            }
        } else {
            if (isset($this->chatdelay[$player->getName()])) {
                if ($this->chatdelay[$player->getName()] + 1 < time()) {
                    $this->chatdelay[$player->getName()] = time();
                } else {
                    if (!$event->isCancelled()) {
                        $event->cancel();
                        $player->sendMessage("Please wait " . $this->chatdelay[$player->getName()] + 1 - time() . " Second");
                    }
                }
            } else {
                $this->chatdelay[$player->getName()] = time();
            }
        }
        if (strtoupper($this->plugin->rank[$player->getName()]) !== "DEFAULT") {
            $event->setFormat(LevelManager::getLevelFormat(LevelManager::$level[$player->getName()]) . " " . RankManager::getRankFormat($this->plugin->rank[$event->getPlayer()->getName()]) . " " . $player->getName() . ": " . TextFormat::RESET . $event->getMessage());
        } else {
            $event->setFormat(LevelManager::getLevelFormat(LevelManager::$level[$player->getName()]) . " " . TextFormat::GRAY . $player->getName() . ": " . TextFormat::RESET . $event->getMessage());
        }
    }

    public function onExhaust(PlayerExhaustEvent $event){
        $event->cancel();
    }

    public function onRegen(EntityRegainHealthEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof Player){
            if($event->getRegainReason() == 4) {
                $event->cancel();
            }
        }
    }
    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        $event->setXpDropAmount(0);
        $event->setDrops([]);
        $event->setDeathMessage("");
    }

    public function onRespawn(PlayerRespawnEvent $event){
        $player = $event->getPlayer();
        $this->teleportLobby($player);
    }
    
    public function onBreak(BlockBreakEvent $event){
        $event->cancel();
    }
    
    public function onPlace(BlockPlaceEvent $event){
        $event->cancel();
    }

    public function addEloToProperty(Player $player, int $value){
        $player->sendMessage("ELO CHANGES " . DatabaseControler::$elo[$player->getName()] . " +$value");
        DatabaseControler::$elo[$player->getName()] += $value;

    }

    public function onLogin(PlayerLoginEvent $event){
        $player = $event->getPlayer();
        $player->getInventory()->clearAll();
        $this->teleportLobby($player);
        DatabaseControler::registerPlayer($player);
    }
}