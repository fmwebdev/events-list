<?php
class Puc_v4_Factory {
    public static function buildUpdateChecker($metadataUrl, $pluginFile, $pluginSlug) {
        return new PluginUpdateChecker($metadataUrl, $pluginFile, $pluginSlug);
    }
}
class PluginUpdateChecker {
    public function __construct($url, $file, $slug) {}
    public function setBranch($branch) {}
}
