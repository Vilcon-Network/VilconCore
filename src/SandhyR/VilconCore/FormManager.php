<?php

namespace SandhyR\VilconCore;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\arena\KitManager;

class FormManager{

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
        $form->setTitle(TextFormat::RED . "FFA");
        $form->addButton("Nodebuff" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getPlayers()) ?? 0, 0, "textures/items/potion_bottle_splash_heal");
        $form->addButton("Resistance" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("resistance")->getPlayers()) ?? 0, 0, "textures/ui/resistance_effect");
        $form->addButton("Fist" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getPlayers()) ?? 0, 0, "textures/items/beef_cooked");
        $form->addButton("Combo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getPlayers()) ?? 0, 0, "textures/items/fish_pufferfish_raw");
        $form->addButton("Sumo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getPlayers()) ?? 0, 0,"textures/items/feather");
        $form->addButton("Gapple" . "\n" . "Playing:" . count(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getPlayers()) ??  0, 0, "textures/items/apple_golden");
        $form->sendToPlayer($player);
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
        $form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$rankqueue["gapple"]) ?? 0, 0, "textures/items/fish_pufferfish_raw");
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
        $form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$unrankqueue["gapple"]) ?? 0, 0, "textures/items/fish_pufferfish_raw");
        $form->addButton("Sumo" . "\n" . "Queue: " . count(Arena::$unrankqueue["sumo"]) ?? 0, 0,"textures/items/feather");
        $form->addButton("Gapple" . "\n" . "Queue:" . count(Arena::$unrankqueue["gapple"]) ??  0, 0, "textures/items/apple_golden");
        $form->addButton("Voidfight" . "\n" . "Queue:" . count(Arena::$unrankqueue["voidfight"]) ??  0, 0, "textures/items/bed_red");
        $form->sendToPlayer($player);
        return $form;
    }
}
