<?php
namespace Core;

class Loader
{
    /**
     * Discover Pages_<slug> directories and attempt to load their manifests/pages.
     * Returns array of discovered manifests.
     *
     * @param string $pluginBase Absolute path to plugin root
     * @return array
     */
    public static function discoverApis(string $pluginBase): array
    {
        $manifests = [];

        // Le plugin passe $pluginBase comme dossier includes.
        // On prend tous les sous-dossiers de includes/Admin/Pages comme modules.
        $pattern = $pluginBase . '/Admin/Pages/*';
        $dirs = array_filter(glob($pattern, GLOB_ONLYDIR), function($dir) {
            $base = basename($dir);
            return $base !== '.' && $base !== '..';
        });
        if (!$dirs) {
            return [];
        }

        foreach ($dirs as $dir) {
            $manifest = null;

            $phpManifest = $dir . '/Pages.php';
            $jsonManifest = $dir . '/manifest.json';

            if (file_exists($phpManifest)) {
                $loaded = include $phpManifest;
                if (is_array($loaded)) {
                    $manifest = $loaded;
                }
            } elseif (file_exists($jsonManifest)) {
                $json = file_get_contents($jsonManifest);
                $data = json_decode($json, true);
                if (is_array($data)) {
                    $manifest = $data;
                }
            }

            if (is_array($manifest) && !empty($manifest['slug'])) {
                $manifest['__path'] = $dir;
                $manifests[$manifest['slug']] = $manifest;

                // If the manifest declares an admin class or includes, try to require files
                // Require all PHP files in the folder (except Pages.php and manifest.json)
                foreach (glob($dir . '/*.php') as $phpFile) {
                    $baseName = basename($phpFile);
                    if (in_array($baseName, ['Pages.php'])) {
                        continue;
                    }

                    try {
                        require_once $phpFile;
                    } catch (\Throwable $e) {
                        // ignore load errors for optional files
                    }
                }
            }
        }

        return $manifests;
    }
}
