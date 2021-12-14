<?php

namespace SandhyR\VilconCore\database;

use SandhyR\VilconCore\Main;

class Database {

    public static function getDatabase(): \mysqli{
        return new \mysqli(Main::getInstance()->config->get("host"), Main::getInstance()->config->get("user"), Main::getInstance()->config->get("password"), Main::getInstance()->config->get("db-name"));
    }

    public static function getDatabaseByPlugin(): \mysqli{
        return new \mysqli("127.0.0.1", "root", "", "server");
    }
}
