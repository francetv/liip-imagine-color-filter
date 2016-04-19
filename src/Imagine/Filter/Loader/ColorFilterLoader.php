<?php

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
