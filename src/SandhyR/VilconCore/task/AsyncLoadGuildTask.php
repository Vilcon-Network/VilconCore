<?php

namespace SandhyR\VilconCore\task;

use pocketmine\scheduler\AsyncTask;
use SandhyR\VilconCore\database\Database;
use SandhyR\VilconCore\guild\GuildManager;
use SandhyR\VilconCore\Main;

class AsyncLoadGuildTask extends AsyncTask{

    public function onRun(): void
    {
        $guild = Database::getDatabaseByPlugin()->query("SELECT * FROM playerguild");
        $allRows = [];
        while($row = mysqli_fetch_assoc($guild)){
        $allRows[$row["guildname"]][] = $row;
        }
        $this->setResult($allRows);
    }

    public function onCompletion(): void
    {
        GuildManager::$guild = $this->getResult();
    }
}