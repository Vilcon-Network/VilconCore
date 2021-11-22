<?php

namespace SandhyR\VilconCore;

use pocketmine\Server;

class SkinManager{

    /* @var array */
    public array $skin_widght_map = [
        64 * 32 * 4 => 64,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 256

    ];
    /* @var array */
    public array $skin_height_map = [
        64 * 32 * 4 => 32,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 128
    ];

    public function saveSkin(string $skin,string $name)
    {
        $path = Main::getInstance()->getDataFolder();
        if (!file_exists($path . "saveskin")) {
            mkdir($path . "saveskin", 0777);
        }
        $img = $this->skinDataToImage($skin);
        if ($img == null) {
            return;
        }
        imagepng($img, $path . "saveskin/" . $name . ".png");
    }

    // taken from https://github.com/thebigsmileXD/skinapi/blob/master/src/xenialdan/skinapi/API.php
    //https://github.com/HimbeersaftLP/LibSkin/blob/master/LibSkin/src/Himbeer/LibSkin/LibSkin.php
    public function skinDataToImage(string $skinData)
    {
        $size = strlen($skinData);

        $width = $this->skin_widght_map[$size];
        $height = $this->skin_height_map[$size];
        $skinPos = 0;
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            Server::getInstance()->getLogger()->info("Error save skin id 2");
            return null;
        }
        // Make background transparent
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $r = ord($skinData[$skinPos]);
                $skinPos++;
                $g = ord($skinData[$skinPos]);
                $skinPos++;
                $b = ord($skinData[$skinPos]);
                $skinPos++;
                $a = 127 - intdiv(ord($skinData[$skinPos]), 2);
                $skinPos++;
                $col = imagecolorallocatealpha($image, $r, $g, $b, $a);
                imagesetpixel($image, $x, $y, $col);
            }
        }
        imagesavealpha($image, true);
        return $image;
    }

    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}