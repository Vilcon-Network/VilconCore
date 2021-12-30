<?php

namespace SandhyR\VilconCore\player;

use pocketmine\entity\Attribute;
use pocketmine\player\Player;
use SandhyR\VilconCore\arena\Arena;
use SandhyR\VilconCore\PlayerManager;

class VilconPlayer extends Player
{

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $xzKB = 0.388;
        $yKb = 0.390;
        if (!Arena::isMatch($this)) {
            $xzKB = $this->getXZByworld();
            $yKb = $this->getYByworld();
        } else {
            $xzKB = $this->getXZDuel();
            $yKb = $this->getYDuel();
            }
        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motion = clone $this->motion;
            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $xzKB;
            $motion->y += $yKb;
            $motion->z += $z * $f * $xzKB;
            if ($motion->y > $yKb) {
                $motion->y = $yKb;
            }
            $this->setMotion($motion);
        }
    }
    public function getXZByworld()
    {
        switch ($this->getWorld()->getFolderName()) {
            case "nodebuff":
                $xzKB = 0.385;
                return $xzKB;
            case "nodebuff-low":
                $xzKB = 0.385;
                return $xzKB;
            case "nodebuff-java":
                $xzKB = 0.390;
                return $xzKB;
            case "gapple":
                $xzKB = 0.386;
                return $xzKB;
            case "opgapple":
                $xzKB = 0.391;
                return $xzKB;
            case "combo":
                $xzKB = 0.290;
                return $xzKB;
            case "fist":
                $xzKB = 0.370;
                return $xzKB;
            case "resistance":
                $xzKB = 0.370;
                return $xzKB;
            case "sumoffa":
                $xzKB = 0.370;
                return $xzKB;
            case "BuildFFA":
                $xzKB = 0.370;
                return $xzKB;
            default:
                $xzKB = 0.370;
                return $xzKB;
        }
    }

    public function getYByworld(){
        switch ($this->getWorld()->getFolderName()){
            case "nodebuff":
                $xzKB=0.385;
                $yKb=0.390;
                return $yKb;
            case "nodebuff-low":
                $xzKB=0.385;
                $yKb=0.380;
                return $yKb;
            case "nodebuff-java":
                $xzKB=0.390;
                $yKb=0.366;
                return $yKb;
            case "gapple":
                $xzKB=0.386;
                $yKb=0.388;
                return $yKb;
            case "opgapple":
                $xzKB=0.391;
                $yKb=0.391;
                return $yKb;
            case "combo":
                $xzKB=0.290;
                $yKb=0.260;
                return $yKb;
            case "fist":
                $xzKB=0.370;
                $yKb=0.381;
                return $yKb;
            case "resistance":
                $xzKB=0.370;
                $yKb=0.381;
                return $yKb;
            case "sumoffa":
                $xzKB=0.370;
                $yKb=0.381;
                return $yKb;
            case "BuildFFA":
                $xzKB=0.370;
                $yKb=0.381;
                return $yKb;
            default:
                $yKb = 0.390;
                return $yKb;
        }
    }

    public function getXZDuel(){
        switch (PlayerManager::$playerstatus[$this->getName()]) {
            case PlayerManager::NODEBUFF_DUEL:
                $xzKB = 0.385;
                $yKb = 0.390;
                return $xzKB;
            case PlayerManager::GAPPLE_DUEL:
                $xzKB = 0.386;
                $yKb = 0.388;
                return $xzKB;
            case PlayerManager::SUMO_DUEL:
                $xzKB = 0.375;
                $yKb = 0.380;
                return $xzKB;
            case "mlgrush":
                $xzKB = 0.370;
                $yKb = 0.384;
                return $xzKB;
            default:
                $xzKB = 0.370;
                $yKb = 0.381;
                return $xzKB;
        }
    }

    public function getYDuel()
    {
        switch (PlayerManager::$playerstatus[$this->getName()]) {
            case PlayerManager::NODEBUFF_DUEL:
                $xzKB = 0.385;
                $yKb = 0.390;
                return $yKb;
            case PlayerManager::GAPPLE_DUEL:
                $xzKB = 0.386;
                $yKb = 0.388;
                return $yKb;
            case PlayerManager::SUMO_DUEL:
                $xzKB = 0.375;
                $yKb = 0.380;
                return $yKb;
            case "mlgrush":
                $xzKB = 0.370;
                $yKb = 0.384;
                return $yKb;
            default:
                $xzKB = 0.370;
                $yKb = 0.381;
                return $yKb;
        }
    }
}
