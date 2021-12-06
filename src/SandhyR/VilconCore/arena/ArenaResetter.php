<?php

namespace SandhyR\VilconCore\arena;

use pocketmine\Server;
use SandhyR\VilconCore\EventListener;
use SandhyR\VilconCore\Main;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ArenaResetter{

    public static $index = [];

    public static function reset(string $name) {
        if(!isset(self::$index["voidfight"])){
            self::$index["voidfight"] = 0;
        }
        ++self::$index["voidfight"];
       Server::getInstance()->getWorldManager()->loadWorld(Main::getInstance()->getLobby());
        $path = Server::getInstance()->getDataPath();
        self::recurse_copy($path."worldsbackup/$name" . self::$index["voidfight"],$path."worlds/$name" . self::$index["voidfight"]);
        Server::getInstance()->getWorldManager()->loadWorld("$name". self::$index["voidfight"]);
    }

    public static function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (($file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file) ) {
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function removeWorld(string $name){
        if(Server::getInstance()->getWorldManager()->isWorldLoaded($name)) {
            $level = Server::getInstance()->getWorldManager()->getWorldByName($name);
            if(count($level->getPlayers()) > 0) {
                foreach($level->getPlayers() as $player) {
                    EventListener::teleportLobby($player);
                }
            }

            Server::getInstance()->getWorldManager()->unloadWorld($level);
        }

        $removedFiles = 1;

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/$name", RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $fileInfo */
        foreach($files as $fileInfo) {
            if($filePath = $fileInfo->getRealPath()) {
                if($fileInfo->isFile()) {
                    unlink($filePath);
                } else {
                    rmdir($filePath);
                }

                $removedFiles++;
            }
        }

        rmdir($worldPath);
        return $removedFiles;
    }
}