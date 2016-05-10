# Liip Imagine Filter

This project is part of [francetv zoom open source projects](https://github.com/francetv/zoom-public) (iOS, Android and Angular).

Convert image to grayscale and apply color. Use composite function to mix color.

## How to use

LiipImagineBundle is required : [https://github.com/liip/LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
ImageMagick extension is required: [http://php.net/manual/fr/book.imagick.php](http://php.net/manual/fr/book.imagick.php)

Copy file into your project.

In config.yml

```
liip_imagine:
    driver: imagick
    filter_sets:
        color_filter:
            jpeg_quality: 80
            png_compression_level: 9
            filters:
                color_filter: {color: "#002353"}
```
 
In service.xml
 
```
<service id="ftven.app.imagine.filter.loader.color_filter" class="Ftven\AppBundle\Imagine\Filter\Loader\ColorFilterLoader">
    <tag name="liip_imagine.filter.loader" loader="color_filter" />
</service>
```

That's it!

## Change color on the fly

```
# app/service/ImageManager.php

public function getFormattedColorImage($path, $color)
{
    if (!$color) {
        return;
    }

    // if not in cache, generate it
    if (!$this->cacheManager->isStored($this->generateName($path, $color), "color_filter")) {
        $binaryOriginal = $this->dataManager->find("color_filter", $path);
        $binaryColored = $this->filterManager->applyFilter(
            $binaryOriginal,
            "color_filter",
            [
                "format" => $this->format,
                "filters" => [
                    "color_filter" => [
                        "color" => $color
                    ]
                ]
            ]
        );
        
        $this->cacheManager->store(
            $binaryColored,
            $this->generateName($path, $color),
            "color_filter"
        );
    }

    return $this->cacheManager->resolve($this->generateName($path, $color), "color_filter");
}

private function generateName($path, $color, $filter)
{
    return $path.substr($color, 1);
}
```