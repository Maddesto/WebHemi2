<?php

/**
 * WebHemi2
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category     WebHemi2
 * @package      WebHemi2_Component
 * @subpackage   WebHemi2_Component_Image
 * @author       Gixx @ www.gixx-web.com
 * @copyright    Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license      http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Component\Image;

use Exception;

/**
 * WebHemi2 Image processing Component
 *
 * @category     WebHemi2
 * @package      WebHemi2_Component
 * @subpackage   WebHemi2_Component_Image
 * @author       Gixx @ www.gixx-web.com
 * @copyright    Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license      http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
abstract class AbstractImage
{
    /** Image will be scaled up the fit the new size if the new image has greater dimensions: true|[FALSE] */
    const RESIZE_OPTION_SCALE_UP = 'scaleUp';
    /** Image will be resized to be not cropped. The empty parts will be filled with color: [TRUE]|false */
    const RESIZE_OPTION_FIT_FULL_SIZE = 'fitFullSize';
    /** The color or the empty parts upon rotation or resize: [TRANSPARENT]|"r,g,b[,a]" */
    const RESIZE_OPTION_FILL_COLOR = 'fillColor';
    /** The default file permisson for the output. */
    const OUTPUT_FILE_PERMISSION = 0640;

    /**
     * @var array $imageResource Container for one or more image resources.
     */
    protected $imageResource = array();
    /**
     * @var int $chmod File permission of the output.
     */
    protected $chmod;

    /**
     * Class constructor.
     *
     * @param string $fileName
     *
     * @throws Exception
     */
    public function __construct($fileName = null)
    {
        if (!extension_loaded('gd')) {
            throw new Exception('No image processing library found (GD).');
        }

        if (!is_null($fileName)) {
            if (file_exists($fileName) && is_readable($fileName)) {
                $this->readImage($fileName);
            }
        }
    }

    /**
     * Class desctructor.
     */
    public function __destruct()
    {
        $this->clearImage();
    }

    /**
     * Set file permission for the output file.
     *
     * @param int $chmod
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    public function setOutputFilePermission($chmod = self::OUTPUT_FILE_PERMISSION)
    {
        if (!is_integer($chmod)) {
            throw new Exception('Parameter is not valid.');
        }

        if ($chmod < 0 || $chmod > 0777) {
            throw new Exception('Parameter is out of range.');
        }

        $this->chmod = $chmod;

        return $this;
    }

    /**
     * Read image file and create resource.
     *
     * @param string $fileName
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    abstract public function readImage($fileName);

    /**
     * Write image resource into file.
     *
     * @param string $fileName
     *
     * @throws Exception
     */
    abstract public function writeImage($fileName);

    /**
     * Retrieve the specific image from the container
     *
     * @param int $index
     *
     * @return null|resource
     */
    public function getResource($index = 0)
    {
        return isset($this->imageResource[$index]) ? $this->imageResource[$index] : null;
    }

    /**
     * Retrieve all images from the container
     *
     * @return array
     */
    public function getAllResources()
    {
        return $this->imageResource;
    }

    /**
     * Add image to the container
     *
     * @param mixed $resource filepath or resource
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    public function addResource($resource)
    {
        if (is_resource($resource)) {
            $this->imageResource[] = $resource;
        } elseif ($resource instanceof AbstractImage) {
            $this->imageResource = array_merge($this->imageResource, $resource->getAllResources());
        } else {
            throw new Exception('Unkown image resource');
        }

        return $this;
    }

    /**
     * Create a blank image with the specified size
     *
     * @param int $width The width of the image
     * @param int $height The height of the image
     * @param mixed $color The background color in "r,g,b[,a]" or array(r,g,b[,a]) format, or "trans" if transparent.
     *
     * @return resource
     */
    public function createResource($width, $height, $color = 'transparent')
    {
        $resource = imagecreatetruecolor($width, $height);

        imagesavealpha($resource, true);
        imagealphablending($resource, false);

        if ($color == 'transparent') {
            $color = array(0, 0, 0, 127);
        } elseif (is_string($color)) {
            $color = explode(',', $color);
        }

        if (!isset($color[3])) {
            $color[3] = 0;
        }

        $resourceColor = imagecolorallocatealpha($resource, $color[0], $color[1], $color[2], $color[3]);
        imagefill($resource, 0, 0, $resourceColor);

        return $resource;
    }

    /**
     * Delete the specific image from the container
     *
     * @param int $index
     *
     * @return AbstractImage
     */
    public function dropResource($index)
    {
        if (isset($this->imageResource[$index])) {
            if (is_resource($this->imageResource[$index])) {
                @imagedestroy($this->imageResource[$index]);
            }
            // So the array will be re-indexed.
            array_splice($this->imageResource, ($index + 1), 1);
        }

        return $this;
    }

    /**
     * Replace a specific image in the container
     *
     * @param resource $newResource
     * @param int $index
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    public function replaceResource($newResource, $index = 0)
    {
        if (is_resource($newResource)) {
            $oldResource = $this->getResource($index);
            if ($oldResource) {
                $this->imageResource[$index] = $newResource;
                // Free up memory.
                @imagedestroy($oldResource);
            } else {
                throw new Exception('No such resource to replace!');
            }
        } else {
            throw new Exception('Function requires valid image resource');
        }

        return $this;
    }

    /**
     * Retrieve the dimensions of a specific resource
     *
     * @throws Exception
     *
     * @return array    Array with the resource image's dimensions (in pixel)
     */
    public function getResourceDimension($index = 0)
    {
        if ($resource = $this->getResource($index)) {
            return array(
                'width' => imagesx($resource),
                'height' => imagesy($resource)
            );
        } else {
            throw new Exception('No such resource!');
        }
    }

    /**
     * Resize image resource to specific size
     *
     * @param int $width - the new width of the resource
     * @param int $height - the new height of the resource only if $fitFullSize is true
     * @param int $index - resource index
     * @param array $options - bool  $scaleUp     - whether scale up image is new size is bigger
     *                         bool  $fitFullSize - fill the whole canvas with color or just resample
     *                         mixed $fillColor   - fill color
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    public function resizeResource($width, $height, $index = 0, $options = array())
    {
        if ($resource = $this->getResource($index)) {
            $scaleUp = array_key_exists(self::RESIZE_OPTION_SCALE_UP, $options)
                ? (bool)$options[self::RESIZE_OPTION_SCALE_UP]
                : false;
            $fitFullSize = array_key_exists(self::RESIZE_OPTION_FIT_FULL_SIZE, $options)
                ? (bool)$options[self::RESIZE_OPTION_FIT_FULL_SIZE]
                : true;
            $fillColor = array_key_exists(self::RESIZE_OPTION_FILL_COLOR, $options)
                ? $options[self::RESIZE_OPTION_FILL_COLOR]
                : 'transparent';

            if (!$fitFullSize) {
                $fillColor = 'transparent';
            }

            // Get the dimensions of the source image.
            list($sourceWidth, $sourceHeight) = $this->getResourceDimension($index);

            // Fix final height if needed.
            $height = $fitFullSize ? $height : round($width / $sourceWidth * $sourceHeight);

            // The replacement resource.
            $target = $this->createResource($width, $height, $fillColor);

            // Setting target width.
            if (!$scaleUp && $width >= $sourceWidth) {
                $targetWidth = $sourceWidth;
                $targetHeight = $sourceHeight;
            } else {
                $targetWidth = $width;
                $targetHeight = round($width / $sourceWidth * $sourceHeight);
            }

            // Fitting into size if still overflows.
            if ($targetHeight > $height) {
                $targetHeight = $height;
                $targetWidth = round($height / $sourceWidth * $sourceHeight);
            }

            // Resample and copy resource into center.
            imagecopyresampled(
                $target,
                $resource,
                round(($width - $targetWidth) / 2),
                round(($height - $targetHeight) / 2),
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            $this->imageResource[$index] = $target;

            // Free up memory.
            imagedestroy($resource);
        } else {
            throw new Exception('No such resource!');
        }

        return $this;
    }

    /**
     * Convert a resource to Grayscaled image
     *
     * @param int $index
     *
     * @throws Exception
     *
     * @return AbstractImage
     */
    public function grayscaleResource($index = 0)
    {
        if ($resource = $this->getResource($index)) {
            list($width, $height) = $this->getResourceDimension($index);

            // Create a blank image with the same dimensions.
            $blackWhiteImage = imagecreate($width, $height);
            for ($c = 0; $c < 256; $c++) {
                $grayscalePalette[$c] = imagecolorallocate($blackWhiteImage, $c, $c, $c);
            }

            // Go through all the colums (Y) and rows (X)
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    // Get the RGB color of the current pixel (RGB - Red Green Blue).
                    $rgb = imagecolorat($resource, $x, $y);
                    // Split the color into components.
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    // Convert the components into grayscale alternatives and merge into a new color
                    $grayscaleIndex = (($r * 0.299) + ($g * 0.587) + ($b * 0.114));
                    // Save the pixel onto new resource.
                    imagesetpixel($blackWhiteImage, $x, $y, $grayscalePalette[$grayscaleIndex]);
                }
            }
            $this->replaceResource($blackWhiteImage, $index);
        } else {
            throw new Exception('No such resource!');
        }

        return $this;
    }

    /**
     * Clear the container
     *
     * @return AbstractImage
     */
    public function clearImage()
    {
        foreach ($this->imageResource as $index => $resource) {
            if (is_resource($resource)) {
                @imagedestroy($resource);
            }
        }

        $this->imageResource = array();
        return $this;
    }
}
