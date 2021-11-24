<?php

namespace SandhyR\VilconCore;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\KitManager;
use pocketmine\entity\Skin;
use jojoe77777\FormAPI\SimpleForm;
use SandhyR\VilconCore\database\DatabaseControler;

class FormManager{

    private $price = ["Blue Creeper"=> 250000, "Enderman"=> 100000, "Energy" => 30000, "Fire" => 40000, "Red Creeper" => 50000, "Turtle" => 75000, "Pickaxe" => 60000, "Firework" => 70000, "Iron Golem" => 50000];

    public function ffaForm(Player $player){
        $api =Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $manager = new KitManager();
            switch ($result) {
                case 0:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::NODEBUFF_FFA;
                    $manager->sendKit($player, PlayerManager::NODEBUFF_FFA);
                    $manager->teleportffa($player, PlayerManager::NODEBUFF_FFA);
                    break;
                case 1:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::RESISTANCE_FFA;
                    $manager->sendKit($player, PlayerManager::RESISTANCE_FFA);
                    $manager->teleportffa($player, PlayerManager::RESISTANCE_FFA);
                    break;
                case 2:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::FIST_FFA;
                    $manager->sendKit($player, PlayerManager::FIST_FFA);
                    $manager->teleportffa($player, PlayerManager::FIST_FFA);
                    break;
                case 3:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::COMBO_FFA;
                    $manager->sendKit($player, PlayerManager::COMBO_FFA);
                    $manager->teleportffa($player, PlayerManager::COMBO_FFA);
                    break;
                case 4:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::SUMO_FFA;
                    $manager->sendKit($player, PlayerManager::SUMO_FFA);
                    $manager->teleportffa($player, PlayerManager::SUMO_FFA);
                    break;
                case 5:
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::GAPPLE_FFA;
                    $manager->sendKit($player, PlayerManager::GAPPLE_FFA);
                    $manager->teleportffa($player, PlayerManager::GAPPLE_FFA);
            }
            return false;
        });
        try {
            $form->setTitle(TextFormat::RED . "FFA");
            $form->addButton("Nodebuff" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getPlayers()) ?? 0, 0, "textures/items/potion_bottle_splash_heal");
            $form->addButton("Resistance" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("resistance")->getPlayers()) ?? 0, 0, "textures/ui/resistance_effect");
            $form->addButton("Fist" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getPlayers()) ?? 0, 0, "textures/items/beef_cooked");
//        $form->addButton("Combo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getPlayers()) ?? 0, 0, "textures/items/fish_pufferfish_raw");
            $form->addButton("Sumo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getPlayers()) ?? 0, 0, "textures/items/feather");
            $form->addButton("Gapple" . "\n" . "Playing:" . count(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getPlayers()) ?? 0, 0, "textures/items/apple_golden");
            $form->sendToPlayer($player);
        } catch (\Error $error){
            Server::getInstance()->getLogger()->error($error->getMessage());
        }
        return $form;
    }

    public function duelsForm(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->unrankform($player);
                    break;
                case 1:
                    $this->rankform($player);
                    break;
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Duels");
        $form->addButton("Unranked Duels", 0, "textures/items/iron_sword");
        $form->addButton("Ranked Duels", 0, "textures/items/diamond_sword");
        $form->sendToPlayer($player);
        return $form;
    }

    public function botForm(Player $player){

    }

    public function rankform(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    Arena::addrankQueue($player, PlayerManager::NODEBUFF_DUEL);
                    break;
                case 1:
                    Arena::addrankQueue($player, PlayerManager::RESISTANCE_DUEL);
                    break;
                case 2:
                    Arena::addrankQueue($player, PlayerManager::FIST_DUEL);
                    break;
                case 3:
                    Arena::addrankQueue($player, PlayerManager::COMBO_DUEL);
                    break;
                case 4:
                    Arena::addrankQueue($player, PlayerManager::SUMO_DUEL);
                    break;
                case 5:
                    Arena::addrankQueue($player, PlayerManager::GAPPLE_DUEL);
                    break;
                case 6:
                    Arena::addrankQueue($player, PlayerManager::VOIDFIGHT_DUEL);

            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Ranked Duels");
        $form->addButton("Nodebuff" . "\n" . "Queue: " . count(Arena::$rankqueue["nodebuff"]) ?? 0, 0, "textures/items/potion_bottle_splash_heal");
        $form->addButton("Resistance" . "\n" . "Queue: " . count(Arena::$rankqueue["resistance"]) ?? 0, 0, "textures/ui/resistance_effect");
        $form->addButton("Fist" . "\n" . "Queue: " . count(Arena::$rankqueue["fist"]) ?? 0, 0, "textures/items/beef_cooked");
//        $form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$rankqueue["gapple"]) ?? 0, 0, "textures/items/fish_pufferfish_raw");
        $form->addButton("Sumo" . "\n" . "Queue: " . count(Arena::$rankqueue["sumo"]) ?? 0, 0,"textures/items/feather");
        $form->addButton("Gapple" . "\n" . "Queue:" . count(Arena::$rankqueue["gapple"]) ??  0, 0, "textures/items/apple_golden");
        $form->addButton("Voidfight" . "\n" . "Queue:" . count(Arena::$rankqueue["voidfight"]) ??  0, 0, "textures/items/bed_red");
        $form->sendToPlayer($player);
        return $form;
    }

    public function unrankform(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    Arena::addUnrankQueue($player, PlayerManager::NODEBUFF_DUEL);
                    break;
                case 1:
                    Arena::addUnrankQueue($player, PlayerManager::RESISTANCE_DUEL);
                    break;
                case 2:
                    Arena::addUnrankQueue($player, PlayerManager::FIST_DUEL);
                    break;
                case 3:
                    Arena::addUnrankQueue($player, PlayerManager::COMBO_DUEL);
                    break;
                case 4:
                    Arena::addUnrankQueue($player, PlayerManager::SUMO_DUEL);
                    break;
                case 5:
                    Arena::addUnrankQueue($player, PlayerManager::GAPPLE_DUEL);
                    break;
                case 6:
                    Arena::addUnrankQueue($player, PlayerManager::VOIDFIGHT_DUEL);

            }
            return false;
        });
        $form->setTitle(TextFormat::RED . " Unranked Duels");
        $form->addButton("Nodebuff" . "\n" . "Queue: " . count(Arena::$unrankqueue["nodebuff"]) ?? 0, 0, "textures/items/potion_bottle_splash_heal");
        $form->addButton("Resistance" . "\n" . "Queue: " . count(Arena::$unrankqueue["resistance"]) ?? 0, 0, "textures/ui/resistance_effect");
        $form->addButton("Fist" . "\n" . "Queue: " . count(Arena::$unrankqueue["fist"]) ?? 0, 0, "textures/items/beef_cooked");
//        $form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$unrankqueue["gapple"]) ?? 0, 0, "textures/items/fish_pufferfish_raw");
        $form->addButton("Sumo" . "\n" . "Queue: " . count(Arena::$unrankqueue["sumo"]) ?? 0, 0,"textures/items/feather");
        $form->addButton("Gapple" . "\n" . "Queue:" . count(Arena::$unrankqueue["gapple"]) ??  0, 0, "textures/items/apple_golden");
        $form->addButton("Voidfight" . "\n" . "Queue:" . count(Arena::$unrankqueue["voidfight"]) ??  0, 0, "textures/items/bed_red");
        $form->sendToPlayer($player);
        return $form;
    }

    public function cosmeticshop(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->capeshop($player);
                    break;
                case 1:
                    $this->tagsshop($player);
                    break;
                case 2:
                    $this->soundshop($player);
                    break;
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Cosmetic Shop");
        $form->addButton("Cape Shop");
        $form->addButton("Tags Shop");
        $form->addButton("Kill Sound Shop");
        $player->sendForm($form);
        return $form;
    }

    public function capeshop(Player $player){
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $cape = $data;
            $pdata = new Config(Main::getInstance()->getDataFolder() . "data.yml", Config::YAML);
            if(!file_exists(Main::getInstance()->getDataFolder(). $data . ".png")) {
                $player->sendMessage("The choosen cape is not available!");
            }else{
                if (strtoupper(Main::getInstance()->rank[$player->getName()]) == "DEFAULT") {
                    $player->sendMessage("You must vip or higher to buy this cape");
                } else {
                    var_dump(DatabaseControler::$coins);
                    if(DatabaseControler::$coins[$player->getName()] >= $this->price[$cape]){
                        $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                        if(!in_array($cape, $array["capes"])) {
                            $player->sendMessage("You succesfully buy cape");
                            $array["capes"][] = $cape;
                            $final = base64_encode(serialize($array));
                            DatabaseControler::$cosmetic[$player->getName()] = $final;
                            var_dump(unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()])));
                            DatabaseControler::$coins[$player->getName()] -= $this->price[$cape];
                        } else {
                            $player->sendMessage("You already have this cape");
                        }
                    } else {
                        $player->sendMessage("Your money not enough");
                    }
                }
            }
            return false;
        });
        $form->setTitle("Cape Shop");
        $form->setContent("Choose your cape");
        $skinmanager = new SkinManager();
        foreach($skinmanager->getCapes() as $capes){
            $form->addButton("$capes" . "\n" . "Price: ". $this->price[$capes] , -1, "", $capes);
        }
        $form->sendToPlayer($player);
    }

    public function createCape($capeName){

        $path = Main::getInstance()->getDataFolder() . "{$capeName}.png";
        $img = @imagecreatefrompng($path);
        $bytes = '';
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public function usecosmeticform(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->usecapeform($player);
                    break;
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        $form->addButton("Cape");
        $player->sendForm($form);
        return $form;
    }

    public function usecapeform(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $oldSkin = $player->getSkin();
            $capeData = $this->createCape($data);
            $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
            $player->setSkin($setCape);
            $player->sendSkin();
            return false;
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        $cape = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
        foreach ($cape["capes"] as $capes){
            $form->addButton("$capes", -1,"", $capes);
        }
        $player->sendForm($form);
        return $form;
    }
}
