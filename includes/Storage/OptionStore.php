<?php
namespace Storage;

class OptionStore
{
    public static function get(string $key, $default = []): array
    {
        return get_option('cv_' . $key, $default);
    }

    public static function set(string $key, $value): void
    {
        update_option('cv_' . $key, $value);
    }
}
