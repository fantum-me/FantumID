<?php

namespace App\Service;

use App\Entity\User;
use App\Utils\VibrantColorGenerator;
use Exception;
use Imagick;
use ImagickPixel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class UserAvatarService
{
    public function __construct(
        private readonly string          $projectDir,
        private readonly string          $userAvatarPath,
        private readonly Filesystem      $filesystem,
        private readonly LoggerInterface $logger
    )
    {
    }

    const DEFAULT_AVATAR_PATH = "/public/avatar/default.png";
    const AVATAR_TEMPLATE_PATH = "/public/avatar/template.png";

    public function generateAvatar(User $user): void
    {
        try {
            if (!$this->filesystem->exists($this->userAvatarPath)) $this->filesystem->mkdir($this->userAvatarPath);

            $color = VibrantColorGenerator::getVibrantColor();
            $color = new ImagickPixel("rgb(" . implode(",", $color) . ")");

            $image = new Imagick($this->projectDir . self::AVATAR_TEMPLATE_PATH);

            $width = $image->getImageWidth();
            $height = $image->getImageHeight();

            $background = new Imagick();
            $background->newImage($width, $height, $color);

            $background->compositeImage($image, Imagick::COMPOSITE_OVER, 0, 0);

            $background->setImageFormat('png');

            $background->writeImage($this->getAvatarPath($user));

            $image->destroy();
            $background->destroy();
        } catch (Exception $e) {
            $this->logger->alert("Error while generating user avatar (" . $user->getId() . "): " . $e->getMessage());
        }
    }

    public function getAvatarPath(User $user): string
    {
        return $this->userAvatarPath . "/" . $user->getId() . ".png";
    }

    public function getDefaultAvatarPath(): string
    {
        return $this->projectDir . self::DEFAULT_AVATAR_PATH;
    }
}
