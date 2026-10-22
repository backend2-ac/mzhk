<?php
namespace App\Model\Entity;

use App\Service\ImageStorage;
use Psr\Http\Message\UploadedFileInterface;

trait ImageUploadTrait
{
    private $uploadedImageNames = [];

    public function uploadedImages(): array
    {
        return $this->uploadedImageNames;
    }

    private function uploadImageValue($file, string $field)
    {
        if (is_string($file) || $file === null) {
            return $file;
        }
        try {
            if (!$file instanceof UploadedFileInterface) {
                throw new \RuntimeException('Выберите файл изображения.');
            }
            $name = ImageStorage::upload($file, $this->imageDirectory);
            $this->uploadedImageNames[] = $name;
            return $name;
        } catch (\Throwable $error) {
            $this->setError($field, ['upload' => $error instanceof \RuntimeException
                ? $error->getMessage() : 'Не удалось обработать изображение. Проверьте файл и права записи.']);
            return false;
        }
    }

    public function _setImg($file)
    {
        return $this->uploadImageValue($file, 'img');
    }

    public function _setImages($file)
    {
        return $this->uploadImageValue($file, 'images');
    }
}
