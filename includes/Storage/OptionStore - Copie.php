<?php
namespace Storage;

class OptionStore
{
    public static function get(string $key): array
    {
        return get_option('cv_' . $key, []);
    }
}
