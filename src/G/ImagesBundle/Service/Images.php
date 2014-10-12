<?php

namespace Stenik\ImagesBundle\Service;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * Images service. Use for create thumbnails.
 *
 * @author Hristo Hristoff <hristo.hristov@stenik.bg>
 **/

class Images {

    protected $imagine, $parameters;

    public function __construct($parameters){
        if (class_exists('Imagick')) {
            $this->imagine = new \Imagine\Imagick\Imagine();    
        } else {
            $this->imagine = new \Imagine\Gd\Imagine();    
        }
        $this->parameters = $parameters;
    }

    /**
     * Checks if the Picture Configuration for the given app is ok
     *
     * @param string $app
     */
    public function checkImageConfig($app) {
        $imageSizes = $this->parameters['contexts'][$app]['formats'];

        if (!array_key_exists('original', $imageSizes))
            throw new Exception('Configuration for images is not ok!');

        foreach ($imageSizes as $imageSizeId => $imageSizeArray) {
            if (!array_key_exists('path', $imageSizeArray))
                throw new Exception('ImageConfigurationError: path is missing for image size "' . $imageSizeId . '"');

            if ($imageSizeId != 'original') {
                if (!array_key_exists('height', $imageSizeArray) || !is_int($imageSizeArray['height']))
                    throw new Exception('ImageConfigurationError: height is missing for image size "' . $imageSizeId . '"');
                if (!array_key_exists('width', $imageSizeArray) || !is_int($imageSizeArray['width']))
                    throw new Exception('ImageConfigurationError: width is missing for image size "' . $imageSizeId . '"');
            }

            if (!file_exists($this->getImagePath($app, $imageSizeId))) {
                if (!mkdir($this->getImagePath($app, $imageSizeId), 0777, true))
                    throw new Exception('ImageConfigurationProblem: path for image size could not be created -' . $this->getImagePath($app, $imageSizeId));
            }
        }

        // ako ne sushtestwuwa papkata togawa da se suzdade, ako ne mozhe da se furli exception!
        return true;
    }

    /**
     * Generates thumbnails for the given app and file according to the app's picture settings
     *
     * @param string $app
     * @param string $fileName
     * @param boolean $exact
     */
    public function createImageThumbnails($app, $fileName) {
        $this->checkImageConfig($app);

        $originalDir = $this->getImagePath($app, 'original');
        $fullPath = $originalDir . $fileName;

        $pictureSizes = $this->parameters['contexts'][$app]['formats'];

        foreach ($pictureSizes as $pictureSizeId => $pictureSizeArray) {
            if($pictureSizeId == 'original')
                continue;

            $targetDir = $this->getImagePath($app, $pictureSizeId);
            $width = $pictureSizeArray['width'];
            $height = $pictureSizeArray['height'];
            $mode    = ImageInterface::THUMBNAIL_OUTBOUND;
            $size = new Box($width, $height);

            $resizeimg = $this->imagine->open($originalDir . $fileName)
                ->thumbnail($size, $mode);
            $sizeR     = $resizeimg->getSize();
            $widthR    = $sizeR->getWidth();
            $heightR   = $sizeR->getHeight();

            $preserve  = $this->imagine->create($size);
            $startX = $startY = 0;
            if ( $widthR < $width ) {
                $startX = ( $width - $widthR ) / 2;
            }
            if ( $heightR < $height ) {
                $startY = ( $height - $heightR ) / 2;
            }
            $preserve->paste($resizeimg, new \Imagine\Image\Point($startX, $startY))
                ->save($targetDir . $fileName);
        }
    }

    /**
     * Deletes the thumbnails for the given app and file according to the app's image settings
     *
     * @param string $app
     * @param string $fileName
     */
    public function deleteImageThumbnails($app, $fileName) {
        $this->checkImageConfig($app);

        $pictureSizes = $this->parameters['contexts'][$app]['formats'];

        foreach ($pictureSizes as $pictureSizeId => $pictureSizeArray) {
            $targetDir = $this->getImagePath($app, $pictureSizeId);
            @unlink($targetDir . $fileName);
        }
    }

    /**
     * Returns the Base-URL to the image
     *
     * @param string $format Format of the image to return path to
     */
    public function getImageURL($app, $format = 'original') {
        $pictureSizes = $this->parameters['contexts'][$app]['formats'];
        if (!array_key_exists($format, $pictureSizes)) {
            return false;
        }
        return $this->parameters['cdn']['server']['path'] . $app . '/' . $pictureSizes[$format]['path'] . '/';
    }

    /**
     * Returns the file system path to the image
     *
     * @param string $format Format of the image to return path to
     */
    private function getImagePath($app, $format) {

        $res = $this->parameters['contexts'][$app]['formats'];
        if (!array_key_exists($format, $res)) {
            return false;
        }
        return $this->parameters['filesystem']['local']['dir'] . $app . DIRECTORY_SEPARATOR . $res[$format]['path'] . DIRECTORY_SEPARATOR;
    }
}
