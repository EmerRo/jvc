<?php

require_once 'app/helpers/ImageStorage.php';

class ImageController extends Controller
{
    public function serve()
    {
        $args = func_get_args();
        if (count($args) < 2) {
            ImageStorage::serveDefault();
            return;
        }
        $category = $args[0];
        $filename = $args[1];
        ImageStorage::serve($category, $filename);
    }

    public function serveThumb()
    {
        $args = func_get_args();
        if (count($args) < 2) {
            ImageStorage::serveDefault();
            return;
        }
        $category = $args[0];
        $filename = $args[1];
        ImageStorage::serveThumbnail($category, $filename);
    }
}
