<?php

namespace blakit\api\utils;

class ApplicationVersion
{
    public static function getLastCommitDate()
    {
        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        return $commitDate ? $commitDate->format('d.m.Y H:i') : null;
    }

    public static function getVersion()
    {
        $filename = __DIR__ . '/../../../../../composer.json';
        if (!is_file($filename)) {
            return null;
        }

        $f = fopen($filename, 'r+');
        $data = fread($f, filesize($filename));
        fclose($f);

        $data = json_decode($data, true);

        return isset($data['version']) ? $data['version'] : null;
    }

    public static function get()
    {
        try {
            $params = [];

            $version = self::getVersion();
            if ($version) {
                $params[] = 'v. ' . $version;
            }

            $updatedDate = self::getLastCommitDate();
            if ($updatedDate) {
                $params[] = 'updated at ' . $updatedDate;
            }

            return implode(', ', $params);
        } catch (\Exception $exception) {
            return null;
        }
    }
}