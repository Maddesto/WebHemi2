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
use WebHemi2\Component\Image\AbstractImage;

/**
 * WebHemi2 .ICO Image processing Component
 *
 * @category     WebHemi2
 * @package      WebHemi2_Component
 * @subpackage   WebHemi2_Component_Image
 * @author       Gixx @ www.gixx-web.com
 * @copyright    Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license      http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 *
 * @copyright Original floIcon copyright (c) 2007 by Joshua Hatfield.
 * @license http://flobi.users.phpclasses.org/browse/file/19222.html
 * @example http://tech.flobi.com/test/floIcon/private.php
 */
class Ico extends AbstractImage
{
    /**
     * @var array $entry Container for one or more entry datas.
     */
    private $entry = array();
    /**
     * @var array $entryBin Container for one or more binary entry datas.
     */
    private $entryBin = array();
    /**
     * @var array $header Container for icon header arrays.
     */
    private $header = array();
    /**
     * @var array $headerBin Container for original icon headers.
     */
    private $headerBin = array();
    /**
     * @var array $imageBin Container for original image resources.
     */
    private $imageBin = array();

    /**
     * Read image file and create resource.
     *
     * @param string $fileName
     *
     * @throws Exception
     *
     * @return Ico
     */
    public function readImage($fileName)
    {
        // Try to read the file.
        if (file_exists($fileName) && filesize($fileName) > 0 && $filePointer = fopen($fileName, "rb")) {
            fseek($filePointer, 0);
            // Unpack headers.
            $header = unpack('SReserved/SType/SCount', fread($filePointer, 6));

            for ($i = 0; $i < $header["Count"]; $i++) {
                $position = ftell($filePointer);
                fseek($filePointer, (6 + ($i * 16)));

                // Get entry.
                $this->entryBin[$i] = fread($filePointer, 16);
                $this->entry[$i] = unpack(
                    'CWidth/CHeight/CColorCount/CReserved/SPlanes/SBitCount/LSizeInBytes/LFileOffset',
                    $this->entryBin[$i]
                );

                // Get image header.
                fseek($filePointer, $this->entry[$i]["FileOffset"]);
                $this->headerBin[$i] = fread($filePointer, 40);
                $this->header[$i] = unpack(
                    'LSize/LWidth/LHeight/SPlanes/SBitCount/LCompression/LImageSize/LXpixelsPerM/LYpixelsPerM/'
                    . 'LColorsUsed/LColorsImportant',
                    $this->headerBin[$i]
                );

                // Get image.
                $this->imageBin = @fread($filePointer, $this->entry[$i]["SizeInBytes"] - strlen($this->headerBin[$i]));
                fseek($filePointer, $position);

                // Set resource.
                if ($resource = @imagecreatefromstring($this->headerBin[$i] . $this->imageBin[$i])) {
                    // This must be a PNG image...
                    $this->header[$i] = array(
                        "Size" => 0,
                        "Width" => imagesx($resource),
                        "Height" => imagesy($resource) * 2,
                        "Planes" => 0,
                        "BitCount" => 32,
                        "Compression" => 0,
                        "ImageSize" => strlen($this->imageBin[$i]),
                        "XpixelsPerM" => 0,
                        "YpixelsPerM" => 0,
                        "ColorsUsed" => 0,
                        "ColorsImportant" => 0,
                    );
                } else {
                    // Otherwise we build up the image pixel-by-pixel.
                    $resource = imagecreatetruecolor($this->entry[$i]["Width"], $this->entry[$i]["Height"]);
                    imagesavealpha($resource, true);
                    imagealphablending($resource, false);
                    $position = 0;
                    // By default we do not ignore the alpha blending
                    $ignoreAlpha = false;
                    $palette = array();

                    // Bitcount < 24. That means we work wit palette.
                    if ($this->header[$i]["BitCount"] < 24) {
                        // Collecting the colors
                        for ($j = 0; $j < pow(2, $this->header[$i]["BitCount"]); $j++) {
                            $blue = ord($this->imageBin[$i][$position++]);
                            $green = ord($this->imageBin[$i][$position++]);
                            $red = ord($this->imageBin[$i][$position++]);
                            $position++;

                            // Looking for alpha blending.
                            $existingPaletteEntry = imagecolorexactalpha($resource, $red, $green, $blue, 0);

                            if ($existingPaletteEntry >= 0) {
                                $palette[] = $existingPaletteEntry;
                            } else {
                                $palette[] = imagecolorallocatealpha($resource, $red, $green, $blue, 0);
                            }
                        }

                        // Go through every pixel of the original image and clone it onto the resource.
                        for ($row = 0; $row < $this->entry[$i]["Height"]; $row++) {
                            $colors = array();

                            for ($column = 0; $column < $this->entry[$i]["Width"]; $column++) {
                                if ($this->header[$i]["BitCount"] < 8) {
                                    $color = array_shift($colors);

                                    if (is_null($color)) {
                                        $byte = ord($this->imageBin[$i][$position++]);
                                        $tmp_color = 0;

                                        for ($j = 7; $j >= 0; $j--) {
                                            $bit_value = pow(2, $j);
                                            $bit = floor($byte / $bit_value);
                                            $byte = $byte - ($bit * $bit_value);
                                            $tmp_color += $bit * pow(2, $j % $this->header[$i]["BitCount"]);

                                            if ($j % $this->header[$i]["BitCount"] == 0) {
                                                array_push($colors, $tmp_color);
                                                $tmp_color = 0;
                                            }
                                        }
                                        $color = array_shift($colors);
                                    }
                                } else {
                                    $color = ord($this->imageBin[$i][$position++]);
                                }

                                if (!imagesetpixel(
                                    $resource,
                                    $column,
                                    ($this->entry[$i]["Height"] - $row - 1),
                                    $palette[$color]
                                )
                                ) {
                                    throw new Exception("Cannot set pixel");
                                }
                            }

                            if ($position % 4 > 0) {
                                $position += 4 - ($position % 4);
                            }
                        }
                    } else {
                        // BitCount >= 24, No Palette needed.
                        $markPosition = $position;
                        $retry = true;

                        while ($retry) {
                            $alphas = array();
                            $retry = false;

                            for ($row = 0; $row < $this->entry[$i]["Height"] && !$retry; $row++) {
                                for ($column = 0; $column < $this->entry[$i]["Width"] && !$retry; $column++) {
                                    $blue = ord($this->imageBin[$i][$position++]);
                                    $green = ord($this->imageBin[$i][$position++]);
                                    $red = ord($this->imageBin[$i][$position++]);

                                    if ($this->header[$i]["BitCount"] < 32) {
                                        $alpha = 0;
                                    } elseif ($ignoreAlpha) {
                                        $alpha = 0;
                                        $position++;
                                    } else {
                                        // Look for alpha blending.
                                        $alpha = ord($this->imageBin[$i][$position++]);
                                        $alphas[$alpha] = $alpha;
                                        $alpha = 127 - round($alpha / 255 * 127);
                                    }

                                    $paletteEntry = imagecolorexactalpha($resource, $red, $green, $blue, $alpha);
                                    if ($paletteEntry < 0) {
                                        $paletteEntry = imagecolorallocatealpha($resource, $red, $green, $blue, $alpha);
                                    }

                                    if (!imagesetpixel(
                                        $resource,
                                        $column,
                                        ($this->entry[$i]["Height"] - $row - 1),
                                        $palette[$paletteEntry]
                                    )
                                    ) {
                                        throw new Exception("Cannot set pixel");
                                    }
                                }

                                if ($position % 4 > 0) {
                                    $position += 4 - ($position % 4);
                                }
                            }

                            if ($this->header[$i]["BitCount"] == 32 && isset($alphas[0]) && count($alphas) == 1) {
                                $retry = true;
                                $position = $markPosition;
                                $ignoreAlpha = true;
                            }
                        }
                    }

                    if ($this->header[$i]["BitCount"] < 32 || $ignoreAlpha) {
                        $palette[-1] = imagecolorallocatealpha($resource, 0, 0, 0, 127);
                        imagecolortransparent($resource, $palette[-1]);

                        for ($row = 0; $row < $this->entry[$i]["Height"]; $row++) {
                            $colors = array();

                            for ($column = 0; $column < $this->entry[$i]["Width"]; $column++) {
                                $color = array_shift($colors);

                                if (is_null($color)) {
                                    $byte = ord($this->imageBin[$i][$position++]);

                                    for ($j = 7; $j >= 0; $j--) {
                                        $bit_value = pow(2, $j);
                                        $bit = floor($byte / $bit_value);
                                        $byte = $byte - ($bit * $bit_value);
                                        array_push($colors, $bit);
                                    }
                                    $color = array_shift($colors);
                                }

                                if ($color) {
                                    if (!imagesetpixel(
                                        $resource,
                                        $column,
                                        ($this->entry[$i]["Height"] - $row - 1),
                                        $palette[-1]
                                    )
                                    ) {
                                        throw new Exception("Cannot set pixel");
                                    }
                                }
                            }

                            if ($position % 4 > 0) {
                                $position += 4 - ($position % 4);
                            }
                        }
                    }

                    if ($this->header[$i]["BitCount"] < 24) {
                        imagetruecolortopalette($resource, true, pow(2, $this->header[$i]["BitCount"]));
                    }
                }
                $this->imageResource[$i] = $resource;

                if ($this->entry[$i]["Width"] == 0) {
                    $this->entry[$i]["Width"] = $this->header[$i]["Width"];
                }
                if ($this->entry[$i]["Height"] == 0) {
                    $this->entry[$i]["Height"] = $this->header[$i]["Height"] / 2;
                }
            }
            fclose($filePointer);
        } else {
            throw new Exception('File not found or not radable');
        }

        return $this;
    }

    /**
     * Write image resource into Ico file.
     *
     * @param string $fileName Name of the Ico file. If null the file will be written on stdout.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function writeImage($fileName = null)
    {
        $order = array();
        $counter = count($this->imageResource);

        // Sort resources from smallest to largest.
        for ($i = 0; $i < $counter; $i++) {
            $order[$i] = imagesx($this->imageResource[$i]);
        }

        $output = pack("SSS", 0, 1, $counter);
        $outputImages = "";

        foreach ($order as $index => $size) {
            $newImageOffset = 6  // Header
                + ($counter * 16) // Entries
                + strlen($outputImages);

            // 4 bytes available for position
            if ($newImageOffset > pow(256, 4)) {
                return false;
            }

            $this->entry[$index]["FileOffset"] = $newImageOffset;
            $this->entryBin[$index] = pack(
                "CCCCSSLL",
                $this->entry[$index]["Width"] >= 256 ? 0 : $this->entry[$index]["Width"],
                $this->entry[$index]["Height"] >= 256 ? 0 : $this->entry[$index]["Height"],
                $this->entry[$index]["ColorCount"],
                $this->entry[$index]["Reserved"],
                $this->entry[$index]["Planes"],
                $this->entry[$index]["BitCount"],
                $this->entry[$index]["SizeInBytes"],
                $this->entry[$index]["FileOffset"]
            );

            $output .= $this->entryBin[$index];
            $outputImages .= $this->headerBin[$index] . $this->imageBin[$index];
        }

        if (is_null($fileName)) {
            header('Content-type: image/x-icon');
            echo $output . $outputImages;
            exit;
        } elseif (!@file_put_contents($fileName, $output . $outputImages)) {
            throw new Exception('Unable to write file: ' . $fileName);
        }

        if (!is_null($fileName)) {
            @chmod($fileName, $this->chmod);
        }

        return true;
    }

    /**
     * Add image to the container
     *
     * @param mixed $resource File path or resource
     *
     * @throws Exception
     *
     * @return Ico
     */
    public function addResource($resource)
    {
        if ($resource instanceof AbstractImage) {
            $resource = $resource->getResource(0);
        } else {
            throw new Exception('Unable to add resource. Resource is invalid.');
        }

        imagesavealpha($resource, true);
        imagealphablending($resource, false);

        $index = count($this->imageResource);
        $height = imagesy($resource);
        $width = imagesx($resource);
        $realIndexPalette = array();
        $realPalette = array();
        $hasTransparency = false;
        $blackColor = false;
        $isTrueColor = false;

        for ($column = 0; $column < $width && !$isTrueColor; $column++) {
            for ($row = 0; $row < $height && !$isTrueColor; $row++) {
                $colorIndex = imagecolorat($resource, $column, $row);
                $color = imagecolorsforindex($resource, $colorIndex);

                if ($color["alpha"] == 0) {
                    if (count($realPalette) < 257 && !$isTrueColor) {
                        $inRealPalette = false;

                        foreach ($realPalette as $realPaletteKey => $realPaletteColor) {
                            if ($color["red"] == $realPaletteColor["red"]
                                && $color["green"] == $realPaletteColor["green"]
                                && $color["blue"] == $realPaletteColor["blue"]
                            ) {
                                $inRealPalette = $realPaletteKey;
                                break;
                            }
                        }

                        if ($inRealPalette === false) {
                            $realIndexPalette[$colorIndex] = count($realPalette);

                            if ($blackColor === false
                                && $color["red"] == 0
                                && $color["green"] == 0
                                && $color["blue"] == 0
                            ) {
                                $blackColor = count($realPalette);
                            }
                            $realPalette[] = $color;
                        } else {
                            $realIndexPalette[$colorIndex] = $inRealPalette;
                        }
                    }
                } else {
                    $hasTransparency = true;
                }

                if ($color["alpha"] != 0 && $color["alpha"] != 127) {
                    $isTrueColor = true;
                }
            }
        }

        if ($isTrueColor) {
            $colorCount = 0;
            $bitCount = 32;
        } else {
            if ($hasTransparency && $blackColor === false) {
                $blackColor = count($realPalette);
                $color = array(
                    "red" => 0,
                    "blue" => 0,
                    "green" => 0,
                    "alpha" => 0
                );
                $realPalette[] = $color;
            }
            $colorCount = count($realPalette);

            if ($colorCount > 256 || $colorCount == 0) {
                $bitCount = 24;
            } elseif ($colorCount > 16) {
                $bitCount = 8;
            } elseif ($colorCount > 2) {
                $bitCount = 4;
            } else {
                $bitCount = 1;
            }

            if (32 > $bitCount) {
                $bitCount = 32;
            }

            switch ($bitCount) {
                case 24:
                    $colorCount = 0;
                    break;

                case 8:
                    $colorCount = 256;
                    break;

                case 4:
                    $colorCount = 16;
                    break;

                case 1:
                    $colorCount = 2;
                    break;

            }
        }

        // Create $this->imageBin[$index]...
        $this->imageBin[$index] = "";

        if ($bitCount < 24) {
            foreach ($realIndexPalette as $colorIndex => $paletteIndex) {
                $color = $realPalette[$paletteIndex];
                $this->imageBin[$index] .= pack("CCCC", $color["blue"], $color["green"], $color["red"], 0);
            }

            while (strlen($this->imageBin) < $colorCount * 4) {
                $this->imageBin[$index] .= pack("CCCC", 0, 0, 0, 0);
            }

            // Save Each Pixel
            $byte = 0;
            $bitPosition = 0;
            for ($row = 0; $row < $height; $row++) {
                for ($column = 0; $column < $width; $column++) {
                    $color = imagecolorat($resource, $column, $height - $row - 1);

                    if (isset($realIndexPalette[$color])) {
                        $color = $realIndexPalette[$color];
                    } else {
                        $color = $blackColor;
                    }

                    if ($bitCount < 8) {
                        $bitPosition += $bitCount;
                        $colorAdjusted = $color * pow(2, 8 - $bitPosition);
                        $byte += $colorAdjusted;

                        if ($bitPosition == 8) {
                            $this->imageBin[$index] .= chr($byte);
                            $bitPosition = 0;
                            $byte = 0;
                        }
                    } else {
                        $this->imageBin[$index] .= chr($color);
                    }
                }

                // Each row ends with dumping the remaining bits and filling up to the 32bit line with 0's.
                if ($bitPosition) {
                    $this->imageBin[$index] .= chr($byte);
                    $bitPosition = 0;
                    $byte = 0;
                }

                if (strlen($this->imageBin[$index]) % 4) {
                    $this->imageBin[$index] .= str_repeat(chr(0), 4 - (strlen($this->imageBin[$index]) % 4));
                }
            }
        } else {
            // Save each pixel.
            for ($row = 0; $row < $height; $row++) {
                for ($column = 0; $column < $width; $column++) {
                    $color = imagecolorat($resource, $column, $height - $row - 1);
                    $color = imagecolorsforindex($resource, $color);

                    if ($bitCount == 24) {
                        if ($color["alpha"]) {
                            $this->imageBin[$index] .= pack("CCC", 0, 0, 0);
                        } else {
                            $this->imageBin[$index] .= pack("CCC", $color["blue"], $color["green"], $color["red"]);
                        }
                    } else {
                        $color["alpha"] = round((127 - $color["alpha"]) / 127 * 255);
                        $this->imageBin[$index] .= pack(
                            "CCCC",
                            $color["blue"],
                            $color["green"],
                            $color["red"],
                            $color["alpha"]
                        );
                    }
                }

                if (strlen($this->imageBin[$index]) % 4) {
                    $this->imageBin[$index] .= str_repeat(chr(0), 4 - (strlen($this->imageBin[$index]) % 4));
                }
            }
        }

        $byte = $bitPosition = 0;

        for ($row = 0; $row < $height; $row++) {
            for ($column = 0; $column < $width; $column++) {
                if ($bitCount < 32) {
                    $color = imagecolorat($resource, $column, $height - $row - 1);
                    $color = imagecolorsforindex($resource, $color);
                    $color = $color["alpha"] == 127 ? 1 : 0;
                } else {
                    $color = 0;
                }

                $bitPosition += 1;
                $colorAdjusted = $color * pow(2, 8 - $bitPosition);
                $byte += $colorAdjusted;

                if ($bitPosition == 8) {
                    $this->imageBin[$index] .= chr($byte);
                    $bitPosition = 0;
                    $byte = 0;
                }
            }

            if ($bitPosition) {
                $this->imageBin[$index] .= chr($byte);
                $bitPosition = 0;
                $byte = 0;
            }

            while (strlen($this->imageBin[$index]) % 4) {
                $this->imageBin[$index] .= chr(0);
            }
        }
        if ($colorCount >= 256) {
            $colorCount = 0;
        }

        $this->header[$index] = array(
            "Size" => 40,
            "Width" => $width,
            "Height" => $height * 2,
            "Planes" => 1,
            "BitCount" => $bitCount,
            "Compression" => 0,
            "ImageSize" => strlen($this->imageBin[$index]),
            "XpixelsPerM" => 0,
            "YpixelsPerM" => 0,
            "ColorsUsed" => $colorCount,
            "ColorsImportant" => 0,
        );
        $this->headerBin[$index] = pack(
            "LLLSSLLLLLL",
            $this->header[$index]["Size"],
            $this->header[$index]["Width"],
            $this->header[$index]["Height"],
            $this->header[$index]["Planes"],
            $this->header[$index]["BitCount"],
            $this->header[$index]["Compression"],
            $this->header[$index]["ImageSize"],
            $this->header[$index]["XpixelsPerM"],
            $this->header[$index]["YpixelsPerM"],
            $this->header[$index]["ColorsUsed"],
            $this->header[$index]["ColorsImportant"]
        );
        $this->entry[$index] = array(
            "Width" => $width,
            "Height" => $height,
            "ColorCount" => $colorCount,
            "Reserved" => 0,
            "Planes" => 1,
            "BitCount" => $bitCount,
            "SizeInBytes" => $this->header[$index]["Size"] + $this->header[$index]["ImageSize"],
            "FileOffset" => -1,
        );
        $this->entryBin[$index] = "";
        $this->imageResource[$index] = $resource;

        return $this;
    }

    /**
     * Drops the specific image from the containers
     *
     * @param int $index
     *
     * @return Ico
     */
    public function dropResource($index)
    {
        if (isset($this->imageResource[$index])) {
            // so the array will be re-indexed
            array_splice($this->imageResource, ($index + 1), 1);
            @array_splice($this->entry, ($index + 1), 1);
            @array_splice($this->entryBin, ($index + 1), 1);
            @array_splice($this->header, ($index + 1), 1);
            @array_splice($this->headerBin, ($index + 1), 1);
            @array_splice($this->imageBin, ($index + 1), 1);
        }

        return $this;
    }

    /**
     * Clears the containers
     *
     * @return Ico
     */
    public function clearImage()
    {
        $this->entry = array();
        $this->entryBin = array();
        $this->header = array();
        $this->headerBin = array();
        $this->imageBin = array();

        return parent::clearImage();
    }
}
