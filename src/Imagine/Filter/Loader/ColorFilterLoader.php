<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 France Télévisions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Ftven\AppBundle\Imagine\Filter\Loader;

use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Image;
use Imagine\Image\Palette\Color;
use Imagine\Exception\InvalidArgumentException;

/**
 * Apply grayscale, then colorize image with color
 *
 * @package Ftven\Bundle\AppBundle\Imagine\Filter\Loader
 */
class ColorFilterLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        if (!isset($options['color'])) {
            throw new InvalidArgumentException('Expected color key in options, none given.');
        }

        if (!$image instanceof Image) {
            throw new \LogicException('Filter only work with imagick driver.');
        }

        $image->effects()->grayscale();

        /** @var \Imagick $imagickImage */
        $imagickImage = $image->getImagick();

        $colorLayer = new \Imagick();
        $colorLayer->newImage($image->getSize()->getWidth(), $image->getSize()->getHeight(), $options['color']);
        $colorLayer->setImageFormat('jpeg');

        $imagickImage->compositeImage($colorLayer, \Imagick::COMPOSITE_MULTIPLY, 0, 0);

        return new Image($imagickImage, $image->palette(), $image->metadata());
    }
}
