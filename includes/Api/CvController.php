<?php
namespace Api;

use Storage\OptionStore;
use WP_REST_Request;

class CvController
{
    public static function all(): array
    {
        return [
            'identity' => OptionStore::get('identity')
        ];
    }

    public static function module(WP_REST_Request $request): array
    {
        return OptionStore::get($request->get_param('module'));
    }
}
