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
    /**
     * Découvre tous les dossiers de pages API (internes et externes via filtre)
     * @param string $pluginBase
     * @return array
     */
    public static function discoverApis(string $pluginBase): array
    {
        $manifests = [];

        // Dossiers internes
        $dirs = [];
        $pattern = $pluginBase . '/Admin/Pages/*';
        $internalDirs = array_filter(glob($pattern, GLOB_ONLYDIR) ?: [], function ($dir) {
            $base = basename($dir);
            return $base !== '.' && $base !== '..';
        });
        $dirs = array_merge($dirs, $internalDirs);

        // Dossiers externes via filtre WordPress
        if (function_exists('apply_filters')) {
            $externalDirs = apply_filters('corbidev_api_pages_dirs', []);
            if (is_array($externalDirs)) {
                $dirs = array_merge($dirs, $externalDirs);
            }
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
                // Recherche et chargement d'une classe d'admin (Page.php)
                $pageClassFile = $dir . '/../Page.php';
                if (file_exists($pageClassFile)) {
                    require_once $pageClassFile;
                    // Déduire le nom de la classe (ex: CvPage, TestPage, ModelePage)
                    $slug = $manifest['slug'] ?? basename($dir);
                    $ucSlug = ucfirst($slug);
                    $adminClass = "Admin\\Page\\{$ucSlug}Page";
                    $manifest['admin_class'] = $adminClass;
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

                // Charger tous les fichiers PHP du dossier (sauf Pages.php)
                foreach (glob($dir . '/*.php') as $phpFile) {
                    $baseName = basename($phpFile);
                    if (in_array($baseName, ['Pages.php'])) {
                        continue;
                    }
                    try {
                        require_once $phpFile;
                    } catch (\Throwable $e) {
                        // ignorer les erreurs de chargement optionnelles
                    }
                }
                // Charger la classe d'admin si présente
                if (!empty($manifest['admin_class']) && file_exists($dir . '/../Page.php')) {
                    require_once $dir . '/../Page.php';
                }
            }
        }
        return $manifests;
    }
}
