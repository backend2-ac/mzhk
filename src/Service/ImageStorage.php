<?php
namespace App\Service;

use Cake\ORM\Table;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/** Shared image storage for the existing CMS tables; no schema changes. */
class ImageStorage
{
    private static function directory(string $directory): string
    {
        if (!preg_match('/^[a-z]+$/D', $directory)) {
            throw new RuntimeException('Недопустимый раздел изображений.');
        }
        return WWW_ROOT . 'img' . DIRECTORY_SEPARATOR . $directory;
    }

    public static function upload(UploadedFileInterface $file, string $directory): string
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Файл не загружен. Проверьте размер файла и повторите попытку.');
        }
        if ($file->getSize() > 20 * 1024 * 1024) {
            throw new RuntimeException('Размер изображения не должен превышать 20 МБ.');
        }
        if (!extension_loaded('gd')) {
            throw new RuntimeException('На сервере недоступна обработка изображений (GD).');
        }

        $base = self::directory($directory);
        foreach ([$base, $base . '/original', $base . '/thumbs'] as $folder) {
            if (!is_dir($folder) && !@mkdir($folder, 0755, true) && !is_dir($folder)) {
                throw new RuntimeException('Не удалось создать папку изображений. Проверьте права записи на хостинге.');
            }
            if (!is_writable($folder)) {
                throw new RuntimeException('Папка изображений недоступна для записи. Проверьте права на хостинге.');
            }
        }

        $temporary = $base . '/original/' . bin2hex(random_bytes(16)) . '.tmp';
        $created = [];
        try {
            $file->moveTo($temporary);
            $info = @getimagesize($temporary);
            $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif'];
            if (!$info || !isset($types[$info[2]]) || $info[0] < 1 || $info[1] < 1) {
                throw new RuntimeException('Выберите корректное изображение JPG, PNG или GIF.');
            }
            if ($info[0] * $info[1] > 20000000) {
                throw new RuntimeException('Изображение слишком большое: допустимо до 20 мегапикселей.');
            }

            // Detect the actual format, independent of filename case or client MIME.
            $extension = $types[$info[2]];
            $name = bin2hex(random_bytes(16)) . '.' . $extension;
            $created = [$base . '/thumbs/' . $name, $base . '/' . $name];
            self::resize($temporary, $created[0], 300, $info);
            self::resize($temporary, $created[1], $directory === 'slides' ? 1920 : 1500, $info);
            return $name;
        } catch (\Throwable $error) {
            foreach ($created as $path) {
                if (is_file($path)) {
                    @unlink($path);
                }
            }
            throw $error;
        } finally {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }
    }

    private static function resize(string $source, string $destination, int $maximum, array $info): void
    {
        // Preserve small originals byte-for-byte, including floor plans and PNG alpha.
        if ($info[0] <= $maximum && $info[1] <= $maximum) {
            if (!copy($source, $destination)) {
                throw new RuntimeException('Не удалось сохранить изображение.');
            }
            return;
        }
        $readers = [IMAGETYPE_JPEG => 'imagecreatefromjpeg', IMAGETYPE_PNG => 'imagecreatefrompng', IMAGETYPE_GIF => 'imagecreatefromgif'];
        $image = @$readers[$info[2]]($source);
        if (!$image) {
            throw new RuntimeException('Не удалось прочитать изображение. Файл повреждён.');
        }
        $scale = min($maximum / $info[0], $maximum / $info[1]);
        $width = max(1, (int)round($info[0] * $scale));
        $height = max(1, (int)round($info[1] * $scale));
        $resized = imagecreatetruecolor($width, $height);
        try {
            if ($info[2] === IMAGETYPE_PNG || $info[2] === IMAGETYPE_GIF) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefill($resized, 0, 0, $transparent);
                if ($info[2] === IMAGETYPE_GIF) {
                    imagecolortransparent($resized, $transparent);
                }
            }
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
            if ($info[2] === IMAGETYPE_PNG) {
                $saved = imagepng($resized, $destination);
            } elseif ($info[2] === IMAGETYPE_GIF) {
                $saved = imagegif($resized, $destination);
            } else {
                $saved = imagejpeg($resized, $destination, 90);
            }
            if (!$saved) {
                throw new RuntimeException('Не удалось сохранить обработанное изображение.');
            }
        } finally {
            imagedestroy($image);
            imagedestroy($resized);
        }
    }

    /** Delete only files no longer referenced by any record in this section. */
    public static function deleteUnused(Table $table, string $directory, array $names): void
    {
        $fields = array_filter(['img', 'images'], function ($field) use ($table) {
            return $table->getSchema()->hasColumn($field);
        });
        if (!$fields) {
            return;
        }
        foreach (array_unique($names) as $name) {
            if (!is_string($name) || $name === '' || basename($name) !== $name || strpos($name, '\\') !== false) {
                continue;
            }
            $conditions = [];
            foreach ($fields as $field) {
                $conditions[] = [$field => $name];
            }
            if ($table->exists(['OR' => $conditions])) {
                continue;
            }
            $base = self::directory($directory);
            foreach ([$base . '/' . $name, $base . '/thumbs/' . $name, $base . '/original/' . $name] as $path) {
                if (is_file($path) && !@unlink($path)) {
                    \Cake\Log\Log::warning('Could not remove unused image: ' . $path);
                }
            }
        }
    }
}
