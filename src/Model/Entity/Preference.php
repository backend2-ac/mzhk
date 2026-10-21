<?php 

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Preference extends Entity{

	protected $_accessible = [
		'icon' => true,
		'title' => true,
		'body' => true,
		'img' => true,
        'item_order' => true,
	];

    

	protected function _customUploadImg($file = array()){
        if( is_array($file) || is_object($file) ){
            $fileName = $file->getClientFilename();
            $oldPath = WWW_ROOT.'img'.DS.'preferences'.DS.$fileName;
            $oldPathThumb = WWW_ROOT.'img'.DS.'preferences'.DS.'thumbs'.DS.$fileName;

            if(file_exists($oldPath)){
                if( file_exists($oldPathThumb) ){
                    return $fileName;
                } else{
                    return false;
                }
            }

            $ext = strtolower(preg_replace("#.+\.([a-z]+)$#", "$1", $fileName));
            $fileName = $this->_genNameFile($ext);

            $path = WWW_ROOT . 'img'.DS.'preferences' .DS. $fileName;
            $path_th = WWW_ROOT . 'img'.DS.'preferences'.DS.'thumbs'.DS. $fileName;
            $path_orig = WWW_ROOT . 'img'.DS.'preferences'.DS.'original'.DS. $fileName;

            $file_move = $file->moveTo($path_orig);
            $this->_resizeImg($path_orig, $path_th, 300, 300, $ext);
            $this->_resizeImg($path_orig, $path, 1500, 1500, $ext);

            unlink($path_orig);

            return $fileName;

        } elseif( is_string($file) ){
            return $file;
        } else{
            return false;
        }
    }
    protected function _genNameFile($ext){
        $name = md5(microtime()) . ".{$ext}";
        if(is_file(WWW_ROOT . 'img'.DS.'preferences'.DS. $name)){
            $name = $this->_genNameFile($ext);
        }
        return $name;
    }
    protected function _resizeImg($target, $dest, $wmax = 300, $hmax = 300, $ext){
        /*
        $target - путь к оригинальному файлу
        $dest - путь сохранения обработанного файла
        $wmax - максимальная ширина
        $hmax - максимальная высота
        $ext - расширение файла
        */
        list($w_orig, $h_orig) = getimagesize($target);
        $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

        if(($wmax / $hmax) > $ratio){
            $wmax = $hmax * $ratio;
        }else{
            $hmax = $wmax / $ratio;
        }

        $img = "";
        // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
        switch($ext){
            case("gif"):
                $img = imagecreatefromgif($target);
                break;
            case("png"):
                $img = imagecreatefrompng($target);
                break;
            default:
                $img = imagecreatefromjpeg($target);
        }

        if( $w_orig > $wmax || $h_orig > $hmax ){ // если размер изображения превышает максимально допустимый
            $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки

            if($ext == "png"){
                imagesavealpha($newImg, true); // сохранение альфа канала
                $transPng = imagecolorallocatealpha($newImg,0,0,0,127); // добавляем прозрачность
                imagefill($newImg, 0, 0, $transPng); // заливка
            }

            imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение

        } else{
            if($ext == "png"){
                imagesavealpha($img, true); // сохранение альфа канала
                $transPng = imagecolorallocatealpha($img,0,0,0,127); // добавляем прозрачность
                imagefill($img, 0, 0, $transPng); // заливка
            }
        }

        if( isset($newImg) && $newImg ){
            $img_create = $newImg;
        } else{
            $img_create = $img;
        }

        switch($ext){
            case("gif"):
                imagegif($img_create, $dest);
                break;
            case("png"):
                imagepng($img_create, $dest);
                break;
            default:
                imagejpeg($img_create, $dest);
        }
        imagedestroy($img_create);
    }
    public function _setImages($file){
        if( is_string($file) ){
            return $this->_customUploadImg($file);
        } else{
            $mimeType = $file->getClientMediaType();
            switch($mimeType){
                case("image/jpeg"):
                case("image/jpg"):
                case("image/png"):
                case("image/gif"):
                    return $this->_customUploadImg($file);
                    break;
                default:
                    return false;
            }
        }
    }
    public function _setImg($file){
        if( is_string($file) ){
            return $this->_customUploadImg($file);
        } else{
            $mimeType = $file->getClientMediaType();
            switch($mimeType){
                case("image/jpeg"):
                case("image/jpg"):
                case("image/png"):
                case("image/gif"):
                    return $this->_customUploadImg($file);
                    break;
                default:
                    return false;
            }
        }
    }
}

 ?>