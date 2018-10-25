<?php

$configFile = isset($argv[1]) ? $argv[1] : '';
if (!$configFile) {
    error('Config file is required as first parameter.');
}

$magentoVersion = isset($argv[2]) ? $argv[2] : '';
if (!$magentoVersion) {
    error('Magento Version is required as second parameter.');
}
$isEnterprise = versionAtLeast($magentoVersion, '1.10.0.0');

$config = getConfigData($configFile);

$patches = [];
foreach ($config as $patch => $fixedVersions) {
    $compareWith = $isEnterprise ? $fixedVersions['enterprise'] : $fixedVersions['community'];
    if (!versionAtLeast($magentoVersion, $compareWith)) {
        $patches[] = $patch;
    }
}

print implode(',', $patches);

function versionAtLeast($versionToCheck, $requiredVersion)
{
    return version_compare($versionToCheck, $requiredVersion) >= 0;
}

function error($message)
{
    print $message . PHP_EOL;
    exit(1);
}

function getConfigData($configFile)
{
    if (!file_exists($configFile)) {
        error('Config file is required as parameter.');
    }

    if (!is_readable($configFile)) {
        error('Config file is not readable.');
    }

    $config = file_get_contents($configFile);
    if (!$config) {
        error('Config file is empty');
    }

    $parsedConfig = json_decode($config, true);
    if (is_null($parsedConfig)) {
        error('Cannot parse JSON in config file');
    }

    return $parsedConfig;
}