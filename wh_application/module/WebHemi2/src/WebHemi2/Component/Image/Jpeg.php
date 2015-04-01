<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category   WebHemi2
 * @package    WebHemi2_Component
 * @subpackage WebHemi2_Component_Image
 * @author     Gabor Ivan <gixx@gixx-web.com>
 * @copyright  2015 Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link       http://www.gixx-web.com
 */

namespace WebHemi2\Component\Image;

use Exception;

/**
 * WebHemi2
 *
 * .JPEG Image processing Component
 *
 * @category   WebHemi2
 * @package    WebHemi2_Component
 * @subpackage WebHemi2_Component_Image
 * @author     Gabor Ivan <gixx@gixx-web.com>
 * @copyright  2015 Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link       http://www.gixx-web.com
 */
class Jpeg extends AbstractImage
{
    /** No compression: best quality but big file size. */
    const COMPRESSION_BEST = 100;
    /** Best compression: low quality and small file size. */
    const COMPRESSION_WORST = 0;

    /** @var int $quality Compression index of the output if supported. */
    protected $quality = 80;

    /**
     * Set image compression.
     *
     * @param int $quality
     *
     * @throws Exception
     *
     * @return Png
     */
    public function setQuality($quality)
    {
        if (!is_integer($quality)) {
            throw new Exception('Parameter is not valid.');
        }

        if ($quality > self::COMPRESSION_BEST || $quality < self::COMPRESSION_WORST) {
            throw new Exception('Parameter is out of range.');
        }

        $this->quality = $quality;

        return $this;
    }

    /**
     * Read image file and create resource.
     *
     * @param string $fileName
     *
     * @throws Exception
     *
     * @return Jpeg
     */
    public function readImage($fileName)
    {
        if (!is_null($fileName) && file_exists($fileName) && is_readable($fileName)) {
            if ($resource = imagecreatefromjpeg($fileName)) {
                $this->imageResource[0] = $resource;
            } else {
                throw new Exception('Resource is not a JPEG image');
            }
        } else {
            throw new Exception('File not found or not readable');
        }
        return $this;
    }

    /**
     * Write image resource into Jpeg file.
     *
     * @param string $fileName Name of the Jpeg file. If null the file will be written on stdout.
     *
     * @throws Exception
     * @return void
     */
    public function writeImage($fileName = null)
    {
        if ($resource = $this->getResource(0)) {
            if (is_null($fileName)) {
                header('Content-type: image/jpeg');
            }

            imagejpeg($resource, $fileName, $this->quality);

            if (!is_null($fileName)) {
                chmod($fileName, $this->chmod);
            }
        } else {
            throw new Exception('No image resource available!');
        }
    }
}
