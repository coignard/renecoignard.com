<?php

error_reporting(0);

require __DIR__ . '/vendor/autoload.php';

function inRange($value, $min, $max)
{
    return is_numeric($value) && $min <= $value && $value <= $max;
}

function getPath()
{
    if (isset($_GET['hash'])) {
        $hash = $_GET['hash'];

        if (ctype_xdigit($hash) && strlen($hash) == 8) {
            $image = glob('images/*' . $hash . '.scr');

            if (!empty($image)) {
                return $image[0];
            }
            else {
                http_response_code(404);
                return $_SERVER['DOCUMENT_ROOT'] . '/stub/404.scr';
            }
        }
        elseif ($hash == 'about') {
            return $_SERVER['DOCUMENT_ROOT'] . '/stub/about.scr';
        }
        else {
            http_response_code(404);
            return $_SERVER['DOCUMENT_ROOT'] . '/stub/404.scr';
        }
    }

    if (isset($_COOKIE['visited']) && $_COOKIE['visited'] === 'true') {
        $image = glob('images/*.scr');

        if (!empty($image)) {
            return $image[array_rand($image)];
        }
        else {
            http_response_code(404);
            return $_SERVER['DOCUMENT_ROOT'] . '/stub/404.scr';
        }
    } else {
        setcookie('visited', 'true', time() + 86400);

        return $_SERVER['DOCUMENT_ROOT'] . '/stub/about.scr';
    }
}

function getBorder()
{
    $border = $_GET['border'];
    if (isset($border) && inRange($border, 0, 7))
    {
        return $border;
    }
    else
    {
        return rand(0, 7);
    }
}

function getZoom()
{
    $zoom = $_GET['zoom'];
    if (isset($zoom) && inRange($zoom, 0.25, 4))
    {
        return $zoom;
    }
    else
    {
        return 2;
    }
}

function getImage()
{
    $converter = new \ZxImage\Converter();
    $converter->setType('standard');
    $converter->setPath(getPath());
    $converter->setZoom(getZoom());

    if (strval($_GET['border']) != 'false')
    {
        $converter->setBorder(getBorder());
    }

    if (strval($_GET['scanlines']) == 'true')
    {
        $converter->addPreFilter('Scanlines');
    }

    return $converter->getBinary();
}

$image = getImage();

header('Content-Type: image/png');
header('Content-Length: ' . strlen($image));

die($image);
