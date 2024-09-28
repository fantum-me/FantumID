<?php

namespace App\Service;

use App\Entity\User;
use App\Utils\VibrantColorGenerator;
use Exception;
use Imagick;
use ImagickPixel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class AvatarGenerationService
{
    public function __construct(
        private readonly string $projectDir,
        private readonly string $userAvatarPath,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger
    ) {
    }

    const DEFAULT_AVATAR_PATH = "/public/avatar/default.png";
    const AVATAR_TEMPLATE_PATH = "/public/avatar/template.png";

    public function generateAvatar(string $identifier): string
    {
        try {
            if (!$this->filesystem->exists($this->userAvatarPath)) {
                $this->filesystem->mkdir($this->userAvatarPath);
            }

            $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'avatar.png';

            $color = $this->stringToColorCode($identifier);
            $color = new ImagickPixel("#$color");

            $image = new Imagick($this->projectDir . self::AVATAR_TEMPLATE_PATH);

            $width = $image->getImageWidth();
            $height = $image->getImageHeight();

            $background = new Imagick();
            $background->newImage($width, $height, $color);

            $background->compositeImage($image, Imagick::COMPOSITE_OVER, 0, 0);

            $background->setImageFormat('png');

            $background->writeImage($path);

            $image->destroy();
            $background->destroy();

            return $path;
        } catch (Exception $e) {
            $this->logger->alert("Error while generating user avatar (" . $identifier . "): " . $e->getMessage());
            return $this->projectDir . DIRECTORY_SEPARATOR . self::DEFAULT_AVATAR_PATH;
        }
    }

    public function stringToColorCode(string $str): string
    {
        return substr(dechex(crc32($str)), 1, 6);
    }
}
