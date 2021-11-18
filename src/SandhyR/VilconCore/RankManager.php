<?php

namespace SandhyR\VilconCore;

use pocketmine\utils\TextFormat;

class RankManager{

    public static function getRankFormat(string $rank): string{
        $rank = strtoupper($rank);
        if ($rank == "DEFAULT"){
            return TextFormat::GRAY;
        } elseif($rank == "VIP"){
            return TextFormat::GREEN . "[VIP]";
        } elseif ($rank == "VIP+"){
            return TextFormat::GREEN . "[VIP" . TextFormat::YELLOW . "+" . TextFormat::GREEN . "]";
        } elseif ($rank == "MVP"){
            return TextFormat::AQUA . "[MVP]";
        } elseif ($rank == "MVP+"){
            return TextFormat::AQUA . "[MVP" . TextFormat::YELLOW . "+" . TextFormat::AQUA . "]";
        } elseif ($rank == "MVP++"){
            return TextFormat::AQUA . "[MVP" . TextFormat::YELLOW . "++" . TextFormat::AQUA . "]";
        } elseif ($rank == "OWNER"){
            return TextFormat::RED . "[OWNER]";
        } elseif($rank == "ADMIN"){
            return TextFormat::RED . "[ADMIN]";
        }
        return TextFormat::GRAY;
    }
}
