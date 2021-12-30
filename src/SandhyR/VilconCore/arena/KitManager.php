<?php

namespace SandhyR\VilconCore\arena;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;
use SandhyR\VilconCore\PlayerManager;
use pocketmine\item\ItemIds;

class KitManager{

    public static $kit = [];
    public static $setupkit = [];

    public function sendKit(Player $player,int $id){
        $item = new ItemFactory();
        switch ($id){
            case PlayerManager::NODEBUFF_FFA:
                $player->getInventory()->clearAll();
                $player->getEffects()->clear();
                $player->setHealth(20);
                    $sword = $item->get(ItemIds::DIAMOND_SWORD, 0, 1);
                    $helmet = $item->get(ItemIds::DIAMOND_HELMET, 0, 1);
                    $chestplate = $item->get(ItemIds::DIAMOND_CHESTPLATE, 0, 1);
                    $leggins = $item->get(ItemIds::DIAMOND_LEGGINGS, 0, 1);
                    $boots = $item->get(ItemIds::DIAMOND_BOOTS, 0, 1);
                    $protection = new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2);
                    $sharpness = new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2);
                    $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3);

                    $sword->addEnchantment($sharpness);
                    $sword->addEnchantment($unbreaking);
                    $helmet->addEnchantment($protection);
                    $chestplate->addEnchantment($protection);
                    $leggins->addEnchantment($protection);
                    $boots->addEnchantment($protection);
                    $helmet->addEnchantment($unbreaking);
                    $chestplate->addEnchantment($unbreaking);
                    $leggins->addEnchantment($unbreaking);
                    $boots->addEnchantment($unbreaking);
                    $player->getInventory()->addItem($sword);
                    $player->getInventory()->addItem($item->get(ItemIds::ENDER_PEARL, 0, 16));
                    $player->getInventory()->addItem($item->get(ItemIds::SPLASH_POTION, 22, 34));
                    $player->getArmorInventory()->setHelmet($helmet);
                    $player->getArmorInventory()->setChestplate($chestplate);
                    $player->getArmorInventory()->setLeggings($leggins);
                    $player->getArmorInventory()->setBoots($boots);
                break;
            case PlayerManager::RESISTANCE_FFA:
                $player->getInventory()->clearAll();
                $player->getInventory()->addItem($item->get(ItemIds::DIAMOND_SWORD,0,1));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 60*1000,100,false));
                break;
            case PlayerManager::FIST_FFA:
                $player->getInventory()->clearAll();
                $player->getEffects()->clear();
                $player->setHealth(20);
                $player->getInventory()->setItem(0,$item->get(ItemIds::STEAK,0,1));
                break;
            case PlayerManager::COMBO_FFA:
                break;
            case PlayerManager::SUMO_FFA:
                $player->getInventory()->clearAll();
                $player->getEffects()->clear();
                $player->setHealth(20);
                $player->getInventory()->addItem($item->get(ItemIds::STEAK,0,1));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 60*1000,100,false));
        }
    }

    public function teleportffa(Player $player, int $id){
        switch ($id){
            case PlayerManager::NODEBUFF_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("nodebuff")) {
                    Server::getInstance()->getWorldManager()->loadWorld("nodebuff");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getSafeSpawn());
                break;
            case PlayerManager::FIST_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("fist")) {
                    Server::getInstance()->getWorldManager()->loadWorld("fist");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getSafeSpawn());
                break;
            case PlayerManager::RESISTANCE_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("resistance")) {
                    Server::getInstance()->getWorldManager()->loadWorld("resistance");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("resistance")->getSafeSpawn());
                break;
            case PlayerManager::SUMO_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("sumo")) {
                    Server::getInstance()->getWorldManager()->loadWorld("sumo");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getSafeSpawn());
                break;
            case PlayerManager::GAPPLE_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("gapple")) {
                    Server::getInstance()->getWorldManager()->loadWorld("gapple");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getSafeSpawn());
                break;
            case PlayerManager::COMBO_FFA:
                if(!Server::getInstance()->getWorldManager()->isWorldLoaded("combo")) {
                    Server::getInstance()->getWorldManager()->loadWorld("combo");
                }
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getSafeSpawn());
        }
    }

    public static function sendDuelKit(Player $player, int $id){
        $item = new ItemFactory();
        switch ($id){
            case PlayerManager::NODEBUFF_DUEL:
                if(self::$kit["nodebuff"][$player->getName()] == "default"){
                    $player->getInventory()->clearAll();
                    $sword = $item->get(ItemIds::DIAMOND_SWORD, 0, 1);
                    $helmet = $item->get(ItemIds::DIAMOND_HELMET, 0, 1);
                    $chestplate = $item->get(ItemIds::DIAMOND_CHESTPLATE, 0, 1);
                    $leggins = $item->get(ItemIds::DIAMOND_LEGGINGS, 0, 1);
                    $boots = $item->get(ItemIds::DIAMOND_BOOTS, 0, 1);
                    $protection = new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2);
                    $sharpness = new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2);
                    $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3);
                    $sword->addEnchantment($sharpness);
                    $sword->addEnchantment($unbreaking);
                    $helmet->addEnchantment($protection);
                    $chestplate->addEnchantment($protection);
                    $leggins->addEnchantment($protection);
                    $boots->addEnchantment($protection);
                    $helmet->addEnchantment($unbreaking);
                    $chestplate->addEnchantment($unbreaking);
                    $leggins->addEnchantment($unbreaking);
                    $boots->addEnchantment($unbreaking);
                    $player->getInventory()->addItem($sword);
                    $player->getInventory()->addItem($item->get(ItemIds::ENDER_PEARL, 0, 16));
                    $player->getInventory()->addItem($item->get(ItemIds::SPLASH_POTION, 22, 34));
                    $player->getArmorInventory()->setHelmet($helmet);
                    $player->getArmorInventory()->setChestplate($chestplate);
                    $player->getArmorInventory()->setLeggings($leggins);
                    $player->getArmorInventory()->setBoots($boots);
                } else {
                    $kit = unserialize(base64_decode(self::$kit["nodebuff"][$player->getName()]));
                    foreach($kit as $slot => $item) {
                        $player->getInventory()->setItem($slot, $item);
                    }
                    $helmet = $item->get(ItemIds::DIAMOND_HELMET, 0, 1);
                    $chestplate = $item->get(ItemIds::DIAMOND_CHESTPLATE, 0, 1);
                    $leggins = $item->get(ItemIds::DIAMOND_LEGGINGS, 0, 1);
                    $boots = $item->get(ItemIds::DIAMOND_BOOTS, 0, 1);
                    $protection = new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2);
                    $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3);
                    $helmet->addEnchantment($protection);
                    $chestplate->addEnchantment($protection);
                    $leggins->addEnchantment($protection);
                    $boots->addEnchantment($protection);
                    $helmet->addEnchantment($unbreaking);
                    $chestplate->addEnchantment($unbreaking);
                    $leggins->addEnchantment($unbreaking);
                    $boots->addEnchantment($unbreaking);
                    $player->getArmorInventory()->setHelmet($helmet);
                    $player->getArmorInventory()->setChestplate($chestplate);
                    $player->getArmorInventory()->setLeggings($leggins);
                    $player->getArmorInventory()->setBoots($boots);
                }
        }
    }

    public static function saveKit(Player $player, string $kit){
        switch ($kit){
            case "nodebuff":
                $item = [];
                foreach ($player->getInventory()->getContents() as $slot => $content){
                    $item[$slot] = $item;
                }
                $item = base64_encode(serialize($item));
                self::$kit["nodebuff"][$player->getName()] = $item;
        }
    }
}
