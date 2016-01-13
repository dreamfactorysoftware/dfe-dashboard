<?php
/**
 * Return sizes readable by humans
 *
 * @param int $byteCount
 * @param int $precision
 *
 * @return string
 */
function normalize_byte_count($byteCount, $precision = 2)
{
    static $_size = [null, 'K', 'M', 'G', 'T', 'P'];
    $_factor = floor((strlen($byteCount) - 1) / 3);

    return sprintf("%.{$precision}f", $byteCount / pow(1024, $_factor)) . array_get($_size, $_factor);
}

/**
 * Is the mime type an image
 *
 * @param string $mimeType
 *
 * @return bool
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}
