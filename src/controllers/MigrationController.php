<?php
namespace blakit\api\controllers;
use ComposerScriptEvent;

/**
 * Class MigrationController
 * @package blakit\api\controllers
 */
class MigrationController {
    /**
     * @param ComposerScriptEvent $event
     * @return bool
     */
    public static function migrate($event) {
        $migrationsDir = __DIR__ . '/../migrations';

        $prepare = array_reverse(explode('/', __DIR__));
        foreach ($prepare as $key => $value) {
            if ($value != 'vendor') {
                unset($prepare[$key]);
            } else break;
        }
        unset($prepare[array_search('vendor', $prepare)]);
        $execFile = 'php '. implode('/', array_reverse($prepare)) . '/yii migrate --interactive=0 --migrationPath=\''.$migrationsDir.'\'';
        exec($execFile);

        print 'Migrations Finished';
        return true;
    }
}