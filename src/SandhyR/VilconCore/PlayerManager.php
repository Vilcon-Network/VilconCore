<?php

namespace SandhyR\VilconCore;


class PlayerManager {


    /** @var int[] */
    public static $playerstatus = [];

    /** @var bool[] */
    public static $iscombat = [];

    /** @var bool[] */
    public static $build = [];
    public const LOBBY = 0;
    public const NODEBUFF_FFA = 1;
    public const SUMO_FFA = 2;
    public const COMBO_FFA = 3;
    public const RESISTANCE_FFA = 4;
    public const NODEBUFF_DUEL = 5;
    public const SUMO_DUEL = 6;
    public const BOXING_DUEL = 7;
    public const VOIDFIGHT_DUEL = 8;
    public const HACKER_BOT = 9;
    public const EASY_BOT = 10;
    public const MEDIUM_BOT = 11;
    public const HARD_BOT = 12;
    public const FIST_FFA = 13;
    public const FIST_DUEL = 14;
    public const GAPPLE_FFA = 15;
    public const RESISTANCE_DUEL = 16;
    public const GAPPLE_DUEL = 17;
}