<?php

namespace Jarenal\App\Model;

use DI\Annotation\Inject;
use DI\Container;
use Jarenal\Core\Database;

class HorseQueries
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

    public function getDistanceCoveredByRaceId($race_id)
    {
        $sql = "SELECT MAX(`distance`) AS distance_covered FROM `horse` WHERE `race_id`=%s;";
        $result = $this->database->executeQuery($sql, [$race_id]);
        $obj = $result->fetch_object();
        return $obj->distance_covered;
    }

    public function getBestHorse()
    {
        $sql = "SET @rank := 0";
        $this->database->executeQuery($sql);
        $sql = "SELECT h.*, @rank := @rank + 1 AS rank FROM `horse` h INNER JOIN `race` r ON h.`race_id`=r.`id` WHERE h.`time_elapsed` > 0 AND r.state=0 ORDER BY h.`time_elapsed` ASC, h.`distance` DESC LIMIT 1";
        $result = $this->database->executeQuery($sql);
        $obj = $result->fetch_object();
        $horse = $this->container->make(Horse::class);
        if ($obj) {
            foreach ($obj as $property => $value) {
                $horse->$property = $value;
            }
        }
        return $horse;
    }
}