<?php
/**
 * Overrides the default migration controller and opens up the relevant functions to call.
 * Note: Not using __call was done deliberately. This is already as far from kosjer as it should be.
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.yii2.migration-proxy
 * @copyright 2014 B&E DeZign
 */

namespace mattheobjornson\yii2\migrationproxy;

use yii\helpers\Console;

class MigrateController extends \yii\console\controllers\MigrateController
{
    public function __construct($config = [])
    {
        parent::__construct('migration', 'console', $config);
    }

    public function init()
    {
        parent::init();
        // This is required to make sure the aliases in the migrationPath are resolved
        $this->beforeAction(\Yii::$app->requestedAction);
    }

    /**
     * Upgrades with the specified migration class.
     * @param string $class the migration class name
     * @return boolean whether the migration is successful
     */
    public function migrateUp($class)
    {
        if ($class === self::BASE_MIGRATION) {
            return true;
        }

        $this->stdout("*** applying $class\n", Console::FG_YELLOW);
        $start = microtime(true);
        $migration = parent::createMigration($class);
        if ($migration->up() !== false) {
            $time = microtime(true) - $start;
            $this->stdout("*** applied $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_GREEN);

            return true;
        } else {
            $time = microtime(true) - $start;
            $this->stdout("*** failed to apply $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_RED);

            return false;
        }
    }

    public function migrateDown($class)         { return parent::migrateDown($class); }
    public function migrateToTime($time)        { return parent::migrateToTime($time); }
    public function migrateToVersion($version)  { return parent::migrateToVersion($version); }
    public function getNewMigrations()          { return parent::getNewMigrations(); }
}
