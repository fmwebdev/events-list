<?php
class Puc_v4_Factory {
    public static function buildUpdateChecker($url, $file, $slug) {
        return new PluginUpdateChecker($url, $file, $slug);
    }
}

class PluginUpdateChecker {
    public function __construct($url, $file, $slug) {}
    public function setBranch($branch) {}
}
