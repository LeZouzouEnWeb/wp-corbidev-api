<?php

namespace Api;

class MediaHelper
{
    public static function resolve(int $media_id): ?array
    {
        $post = get_post($media_id);

        if (!$post || $post->post_type !== 'attachment') {
            return null;
        }

        $url = wp_get_attachment_url($media_id);
        $alt = get_post_meta($media_id, '_wp_attachment_image_alt', true);
        $meta = wp_get_attachment_metadata($media_id);

        $sizes = [];

        if (!empty($meta['sizes'])) {
            foreach ($meta['sizes'] as $size => $data) {
                $sizes[$size] = wp_get_attachment_image_url($media_id, $size);
            }
        }

        return [
            'id' => $media_id,
            'url' => $url,
            'alt' => $alt,
            'sizes' => $sizes,
        ];
    }
}