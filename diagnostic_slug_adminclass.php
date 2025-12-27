<?php
// Diagnostic automatique de la correspondance slug ↔ classe d'admin

$manifests = $GLOBALS['cv_headless_discovered_apis'] ?? [];

if (empty($manifests)) {
    echo "Aucun manifest détecté.\n";
    exit(1);
}

foreach ($manifests as $slug => $manifest) {
    echo "---\n";
    echo "Slug : $slug\n";
    echo "  admin_class : ";
    if (!empty($manifest['admin_class'])) {
        echo $manifest['admin_class'];
        if (class_exists($manifest['admin_class'])) {
            echo "  [OK : classe chargée]";
        } else {
            echo "  [ERREUR : classe non chargée]";
        }
    } else {
        echo "[Aucune admin_class dans le manifest]";
    }
    echo "\n";
    if (!empty($manifest['__path'])) {
        $pageFile = dirname($manifest['__path']) . '/Page.php';
        echo "  Page.php : ";
        echo file_exists($pageFile) ? $pageFile . " [trouvé]" : "[absent]";
        echo "\n";
    }
}
