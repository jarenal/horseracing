<?php

namespace Jarenal\App\Model;

use DI\Annotation\Inject;
use DI\Container;
use Jarenal\Core\Database;

class RaceQueries
{
    /**
     * @Inject("DI\Container")
     * @var Container $container
     */
    private $container;

    /**
     * @Inject("Jarenal\Core\Database")
     * @var Database $database
     */
    protected $database;

    public function countActiveRaces()
    {
        $sql = "SELECT COUNT(*) as total FROM `race` WHERE `state`=%s";
        $result = $this->database->executeQuery($sql, [1]);
        $row = $result->fetch_object();
        return $row->total;
    }

    public function getActiveRaces()
    {
        $sql = "SELECT * FROM `race` WHERE `state`=%s ORDER BY `id` DESC LIMIT 3";
        $result = $this->database->executeQuery($sql, [1]);
        $active_races = [];

        while ($obj = $result->fetch_object()) {
            $race = $this->container->make(Race::class);
            foreach ($obj as $property => $value) {
                $race->$property = $value;
            }
            $active_races[] = $race;
        }

        return $active_races;
    }

    public function getLastFiveRaces()
    {
        $sql = "SELECT * FROM `race` WHERE `state`=0 ORDER BY `id` DESC LIMIT 5";
        $result = $this->database->executeQuery($sql);
        $last_races = [];

        while ($obj = $result->fetch_object()) {
            $race = $this->container->make(Race::class);
            foreach ($obj as $property => $value) {
                $race->$property = $value;
            }
            $last_races[] = $race;
        }

        return $last_races;
    }

}