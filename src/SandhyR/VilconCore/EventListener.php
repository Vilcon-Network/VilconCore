<?php

namespace SandhyR\VilconCore;

use pocketmine\block\Bed;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\SplashPotion;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\EndermanTeleportSound;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\KitManager;
use SandhyR\VilconCore\database\Database;
use SandhyR\VilconCore\database\DatabaseControler;
use SandhyR\VilconCore\game\Game;
use SandhyR\VilconCore\task\AFKTask;
use SandhyR\VilconCore\task\KronTask;
use SandhyR\VilconCore\task\NametagTask;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use SandhyR\VilconCore\task\RespawnTask;
use SandhyR\VilconCore\task\ScoreboardTask;

class EventListener implements Listener
{

    public $damager = [];
    public $timer = [];
    private $delay = [];
    private static $clicks = [];
    public static $device = [];
    public static $control = [];
    public $plugin;
    private $lastchat = [];
    private $chatdelay = [];
    public static $autogg = [];
    public static $autoez = [];
    public static $cpspopup = [];
    public static $movementsession = [];

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
        } else {
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        }
        $player->getInventory()->clearAll();
        self::sendItem($player);
        $event->setJoinMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "]" . TextFormat::GREEN . " " . $player->getName());
//        $this->initJoin($player);
        if ($this->plugin->rank[$player->getName()] !== "DEFAULT") {
            $player->setAllowFlight(true);
        }
        PlayerManager::$playerstatus[$event->getPlayer()->getName()] = PlayerManager::LOBBY;
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask($event->getPlayer()), 20);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new AFKTask($event->getPlayer()), 20);
    }

    public function onAttack(EntityDamageByEntityEvent $event)
    {
        $manager = new KitManager();
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
                $killer->sendMessage(TextFormat::RED . "Interrupting is not allowed!");
            } elseif (!isset($this->damager[$player->getName()]) and isset($this->damager[$killer->getName()])) {
                $event->cancel();
                $killer->sendMessage(TextFormat::RED . "Your enemy is " . $this->damager[$killer->getName()]);
            } elseif (isset($this->damager[$player->getName()])) {
                if ($killer->getName() !== $this->damager[$player->getName()]) {
                    $event->cancel();
                    $killer->sendMessage(TextFormat::RED . "Interrupting is not allowed!");
                } else {
                    $this->setTimer($player, $killer);
                }
            }
            if ($player->getHealth() <= $event->getFinalDamage()) {
                if (!Arena::isMatch($player) and !Arena::isMatch($killer)) {
                    $event->cancel();
                    self::teleportLobby($player);
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
                    Server::getInstance()->broadcastMessage($dm);
                    $manager->sendKit($player, PlayerManager::$playerstatus[$player->getName()]);
                } else {
                    if(Arena::isVoidFight($player) and Arena::isVoidFight($player)){
                        if(isset(Game::$bed[$player->getName()]) and isset(Game::$bed[$killer->getName()])){
                            if(Game::$bed[$player->getName()]) {
                                $player->setGamemode(GameMode::SPECTATOR());
                                Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RespawnTask($player), 20);
                                return;
                            } else {
                                // TODO END MATCH
                            }
                        }
                    }
                    if(Arena::isRankDuel($player) and Arena::isRankDuel($killer)){
                        $this->addEloToProperty($killer, mt_rand(10, 20));
                    }
                    self::teleportLobby($player);
                    $killer->sendTitle(TextFormat::GOLD . "VICTORY");
                    $killer->sendMessage(TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName());
                    $player->sendMessage(TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName());
                    self::teleportLobby($killer);
                    --Arena::$duelindex;
                    Arena::unsetMatch($player);
                    Arena::unsetMatch($killer);
                }
                if (self::$autoez[$killer->getName()] == 1) {
                    $killer->chat("ez");
                }
                if (self::$autogg[$killer->getName()] == 1) {
                    $killer->chat("gg");
                }
                ++DatabaseControler::$kill[$killer->getName()];
                ++DatabaseControler::$death[$player->getName()];
                LevelManager::addExp($killer, mt_rand(20, 50));
                DatabaseControler::$coins[$killer->getName()] += mt_rand(20, 50);
                Utils::addSound($killer);
            }
        } elseif ($killer instanceof Human and $player instanceof Player) {
            if ($player->getHealth() <= $event->getFinalDamage()) {
                $player->sendMessage(TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName());
                self::teleportLobby($player);
                $event->cancel();
            }
        }
    }

    public static function sendItem(Player $player)
    {
        $player->getEffects()->clear();
        $player->setGamemode(GameMode::ADVENTURE());
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $item = new ItemFactory();
        if (Arena::isQueue($player)) {
            $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
        } else {
            $player->getInventory()->setItem(0, $item->get(ItemIds::DIAMOND_SWORD)->setCustomName("FFA"));
            $player->getInventory()->setItem(1, $item->get(ItemIds::IRON_SWORD)->setCustomName("Duels"));
            $player->getInventory()->setItem(2, $item->get(ItemIds::GOLD_SWORD)->setCustomName("Self Practice"));
            $player->getInventory()->setItem(4, $item->get(ItemIds::COMPASS)->setCustomName("Cosmetic Shop"));
            $player->getInventory()->setItem(5, $item->get(ItemIds::RED_FLOWER)->setCustomName("Cosmetic"));
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        switch ($id) {
            case ItemIds::DIAMOND_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
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
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->duelsForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::GOLD_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
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
                break;
            case ItemIds::COMPASS:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->cosmeticshop($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::RED_FLOWER:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->usecosmeticform($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
        }
    }

    public function unsetDamager(Player $player)
    {
        unset($this->damager[$player->getName()]);
    }

    public function getTimer(Player $player)
    {
        return $this->timer[$player->getName()];
    }

    public function timer(Player $player)
    {
        --$this->timer[$player->getName()];
    }

    public function unsetTimer(Player $player)
    {
        unset($this->timer[$player->getName()]);
    }

    public function setEnemy(Player $player, Player $enemy)
    {
        $this->damager[$player->getName()] = $enemy->getName();
        $this->damager[$enemy->getName()] = $player->getName();
    }

    public function setTimer(Player $player, Player $enemy)
    {
        $this->timer[$player->getName()] = 15;
        $this->timer[$enemy->getName()] = 15;
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        unset(self::$clicks[$event->getPlayer()->getName()]);
        $event->getPlayer()->getInventory()->clearAll();
        if (isset($this->damager[$event->getPlayer()->getName()])) {
            $damager = Server::getInstance()->getPlayerExact($this->damager[$event->getPlayer()->getName()]);
            if ($damager->isOnline()) {
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
        if (Arena::isMatch($player)) {
            foreach (Arena::$match as $index => $matchs) {
                foreach ($matchs as $indeks => $match) {
                    if ($indeks == $player->getName()) {
                        $enemy = Server::getInstance()->getPlayerExact($match);
                        if ($enemy->isOnline()) {
                            $enemy->sendTitle(TextFormat::YELLOW . "VICTORY!");
                        }
                    }
                }
            }
        }
        $event->setQuitMessage(TextFormat::GRAY . "[" . TextFormat::RED . "-" . TextFormat::GRAY . "]" . TextFormat::RED . " " . $player->getName());
        Arena::unsetQueue($player);
        Arena::unsetMatch($player);
        DatabaseControler::setKill($player, DatabaseControler::$kill[$player->getName()]);
        DatabaseControler::setDeath($player, DatabaseControler::$death[$player->getName()]);
        DatabaseControler::setElo($player, DatabaseControler::$elo[$player->getName()]);
        DatabaseControler::setCoin($player, DatabaseControler::$coins[$player->getName()]);
        DatabaseControler::setCosmetic($player, DatabaseControler::$cosmetic[$player->getName()]);
        unset(DatabaseControler::$kill[$player->getName()], DatabaseControler::$death[$player->getName()], DatabaseControler::$elo[$player->getName()], DatabaseControler::$coins[$player->getName()],
            DatabaseControler::$cosmetic[$player->getName()]);
        unset(PlayerManager::$playerstatus[$player->getName()]);
    }

    public function addClick(Player $player)
    {
        array_unshift(self::$clicks[$player->getName()], microtime(true));
        if (count(self::$clicks[$player->getName()]) >= 100) {
            array_pop(self::$clicks[$player->getName()]);
        }
        if (self::$cpspopup[$player->getName()] == 1) {
            $player->sendTip(TextFormat::AQUA . "CPS: " . TextFormat::RESET . self::getCps($player));
        }
    }

    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->addClick($event->getOrigin()->getPlayer());
            }
        }
        if ($packet instanceof LevelSoundEventPacket and $packet->sound == 42) {
            $this->addClick($player);
        }
    }

    public static function getCps(Player $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (empty(self::$clicks[$player->getName()])) {
            return 0.0;
        }
        $mt = microtime(true);
        return round(count(array_filter(self::$clicks[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {
                return ($mt - $t) <= $deltaTime;
            })) / $deltaTime, $roundPrecision);
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
        }
//         if($event->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
//             if($entity instanceof Player) {
//                 if ($event->getEntity()->getWorld()->getFolderName() == "combo" or Arena::isCombo($entity)) {
//                     $event->uncancel();
//                 }
//             }
//    }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        self::$movementsession[$player->getName()] = 120;
        if ($player->getPosition()->getY() <= 2) {
            if ($player->getWorld()->getFolderName() == "sumo") {
                if (isset($this->damager[$player->getName()])) {
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                    $killer = $this->damager[$player->getName()];
                    Server::getInstance()->broadcastMessage($player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer . " §6[" . "20" . " HP]");
                    self::teleportLobby($player);
                    if (Server::getInstance()->getPlayerExact($killer)->isOnline()) {
                        $killer = Server::getInstance()->getPlayerExact($this->damager[$player->getName()]);
                        ++DatabaseControler::$kill[$killer->getName()];
                    } else {
                        if (isset($this->damager[$player->getName()])) {
                            ++DatabaseControler::$kill[$this->damager[$player->getName()]];
                        }
                    }
                    ++DatabaseControler::$death[$player->getName()];
                    LevelManager::addExp($killer, mt_rand(20, 50));
                }
            }
            self::teleportLobby($player);
        }
    }

    public static function teleportLobby(Player $player)
    {
        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::LOBBY;
        $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        $player->getInventory()->clearAll();
        $player->setHealth(20);
        self::sendItem($player);
    }

    public function initJoin(Player $player)
    {
        $rank = DatabaseControler::getRanks($player);
        $this->plugin->rank[$player->getName()] = $rank;
        LevelManager::$level[$player->getName()] = DatabaseControler::getLevel($player);
        $extradata = $player->getNetworkSession()->getPlayerInfo()->getExtraData();
        $os = ["Unknown", "Android", "iOS", "macOS", "FireOS", "GearVR", "HoloLens", "Win10", "Windows", "Dedicated", "Orbis", "PS4", "Nintendo Switch", "Xbox One"];
        $control = ["Unknown", "Mouse", "Touch", "Controller"];
        $cosmetic = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
        self::$clicks[$player->getName()] = [];
        foreach ($cosmetic["equip"] as $key => $value) {
            if ($value !== "default") {
                switch ($key) {
                    case "capes":
                        $oldSkin = $player->getSkin();
                        $skinmanager = new SkinManager();
                        $capeData = $skinmanager->createCape($value);
                        $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                        $player->setSkin($setCape);
                        $player->sendSkin();
                        break;
                    case "wings":
                        // TODO tdk tw bikin wings di pm4
                        break;
                }
            }
        }
        try {
            self::$control[$player->getName()] = $control[$extradata["CurrentInputMode"]];
        } catch (\ErrorException $exception) {

        }
        self::$device[$player->getName()] = $os[$extradata["DeviceOS"]];
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new NametagTask($player, $this), 1);
        $data = $player->getPlayerInfo()->getExtraData();
        $name = $data["ThirdPartyName"];
        if ($data["PersonaSkin"]) {
            if (!file_exists(Main::getInstance()->getDataFolder() . "saveskin")) {
                mkdir(Main::getInstance()->getDataFolder() . "saveskin", 0777);
            }
            copy(Main::getInstance()->getDataFolder() . "steve.png", Main::getInstance()->getDataFolder() . "saveskin/$name.png");
            return;
        }
        if ($data["SkinImageHeight"] == 32) {
        }
        $saveSkin = new SkinManager();
        $saveSkin->saveSkin(base64_decode($data["SkinData"], true), $name);
    }

    public function onChat(PlayerChatEvent $event)
    {

        $player = $event->getPlayer();
        if (isset(KitManager::$setupkit[$player->getName()])) {
            if (strtolower($event->getMessage()) == "y") {
                KitManager::saveKit($player, KitManager::$setupkit[$player->getName()]);
                $event->cancel();
                return;
            }
            if (strtolower($event->getMessage()) == "n") {
                $player->getInventory()->clearAll();
                self::sendItem($player);
                $event->cancel();
                return;
            }
            $player->sendMessage("Chat y or n for save kits!");
        }
        if (isset($this->lastchat[$player->getName()])) {
            if ($event->getMessage() == $this->lastchat[$player->getName()]) {
                $event->cancel();
                $player->sendMessage(TextFormat::RED . "You cant send message at twice");
            }
            $this->lastchat[$player->getName()] = $event->getMessage();
        } else {
            $this->lastchat[$player->getName()] = $event->getMessage();
        }
        if (strtoupper($this->plugin->rank[$player->getName()]) == "DEFAULT") {
            if (isset($this->chatdelay[$player->getName()])) {
                if ($this->chatdelay[$player->getName()] + 3 < time()) {
                    $this->chatdelay[$player->getName()] = time();
                } else {
                    if (!$event->isCancelled()) {
                        $event->cancel();
                        $player->sendMessage(TextFormat::RED . "Please wait 3 Second to chat");
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
                        $player->sendMessage(TextFormat::RED . "Please wait 1 Second to chat");
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

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $event->cancel();
    }

    public function onRegen(EntityRegainHealthEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if ($event->getRegainReason() == 4) {
                $event->cancel();
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $event->setXpDropAmount(0);
        $event->setDrops([]);
        $event->setDeathMessage("");
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        self::teleportLobby($player);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if($event->getBlock() instanceof Bed){
            if(substr($event->getBlock()->getPosition()->getWorld()->getFolderName(), 0, 9) == "voidfight") {
                $pos = ["x" => $event->getBlock()->getPosition()->getX(), "y" => $event->getBlock()->getPosition()->getY(), "z" => $event->getBlock()->getPosition()->getZ()];
                if ($event->getBlock()->getPosition()->getY() == 10) {
                    if (isset(Game::$team[$player->getName()])) {
                        if (Game::$team[$player->getName()] !== "blue") {
                            $enemy = Game::$enemy[$player->getName()];
                            if($enemy->isOnline()){
                                $enemy->sendTitle(TextFormat::BOLD . "BED DESTROYED", TextFormat::GREEN . "You no longer to respawn");
                                Game::$bed[$enemy->getName()] = false;
                            }
                            return;
                        } else {
                            $player->sendMessage(TextFormat::RED . "You cant break bed your team");
                            $event->cancel();
                        }
                        if(Game::$team[$player->getName()] !== "red"){
                            $enemy = Game::$enemy[$player->getName()];
                            if($enemy->isOnline()){
                                $enemy->sendTitle(TextFormat::BOLD . "BED DESTROYED", TextFormat::GREEN . "You no longer to respawn");
                                Game::$bed[$enemy->getName()] = false;
                            }
                            return;
                        } else {
                            $player->sendMessage(TextFormat::RED . "You cant break bed your team");
                            $event->cancel();
                        }
                    }
                }
                return;
            }
        }
        $build = PlayerManager::$build[$player->getName()] ?? false;
        if(!$build) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $build = PlayerManager::$build[$player->getName()] ?? false;
        if(!$build) {
            $event->cancel();
        }
    }

    public function addEloToProperty(Player $player, int $value)
    {
        $player->sendMessage("ELO CHANGES " . DatabaseControler::$elo[$player->getName()] . " +$value");
        DatabaseControler::$elo[$player->getName()] += $value;

    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $player->getInventory()->clearAll();
        self::teleportLobby($player);
        DatabaseControler::registerPlayer($player);
        $this->initJoin($player);
    }

    public function onChangeWorld(EntityTeleportEvent $event){
        $entity = $event->getEntity();
        if($event->getTo()->getWorld()->getFolderName() == Main::getInstance()->getLobby()){
            if($entity instanceof Player){
                PlayerManager::$playerstatus[$entity->getName()] = PlayerManager::LOBBY;
                if(isset($this->plugin->rank[$entity->getName()])){
                if(strtoupper($this->plugin->rank[$entity->getName()]) !== "DEFAULT") {
                    $entity->setAllowFlight(true);
                }
                }
            }
        } else {
            if($entity instanceof Player){
                $entity->setAllowFlight(false);
            }
        }
    }

    public function onDrop(PlayerDropItemEvent $event){
        $event->cancel();
    }

    public function onHit(ProjectileHitBlockEvent $event){
        $projectile = $event->getEntity();
        if($projectile instanceof \pocketmine\entity\projectile\SplashPotion){
            $player = $projectile->getOwningEntity();
            if($player->isAlive()){
                $distance = $projectile->getPosition()->distance($player->getPosition()->asVector3());
                if($player instanceof Player and $distance <= 3){
                    $player->setHealth($player->getHealth() + 4);
                }
            }
        }
    }
}
