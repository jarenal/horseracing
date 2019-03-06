<?php

namespace Jarenal\App\Model;

use Jarenal\Core\Model;
use SplSubject;

class Horse extends Model implements \SplObserver
{
    public $id;
    public $race_id;
    public $speed;
    public $strength;
    public $endurance;
    public $distance;
    public $time_elapsed;
    public $created_at;
    public $race;
    public $rank;

    /**
     * @Inject("Jarenal\Core\Config")
     * @var Config $config
     */
    private $config;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Horse
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRaceId()
    {
        return $this->race_id;
    }

    /**
     * @param mixed $race_id
     * @return Horse
     */
    public function setRaceId($race_id)
    {
        $this->race_id = $race_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @param mixed $speed
     * @return Horse
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param mixed $strength
     * @return Horse
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndurance()
    {
        return $this->endurance;
    }

    /**
     * @param mixed $endurance
     * @return Horse
     */
    public function setEndurance($endurance)
    {
        $this->endurance = $endurance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     * @return Horse
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     * @return Horse
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeElapsed()
    {
        return $this->time_elapsed;
    }

    /**
     * @param mixed $time_elapsed
     * @return Horse
     */
    public function setTimeElapsed($time_elapsed)
    {
        $this->time_elapsed = $time_elapsed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param Race $race
     */
    public function addRace(Race $race)
    {
        $this->race = $race;
        $this->race_id = $race->getId();
    }

    public function save()
    {
        $this->database->connect();

        if ($this->id) {
            $sql = "UPDATE `horse` SET `speed`=%s, `strength`=%s, `endurance`=%s, `distance`=%s,`time_elapsed`=%s WHERE `id`=%s";
            $result = $this->database->executeQuery($sql, [$this->speed, $this->strength, $this->endurance, $this->distance, $this->time_elapsed, $this->id]);
        } else {
            $sql = "INSERT INTO `horse` (`race_id`, `speed`, `strength`, `endurance`, `distance`) VALUES (%s, %s, %s, %s, %s)";
            $result = $this->database->executeQuery($sql, [$this->race_id, $this->speed, $this->strength, $this->endurance, $this->distance]);
        }

        if (!$this->id) {
            $last_id = $this->database->getLastId();
            $sql = "SELECT * FROM `horse` WHERE `id`=%s";
            $result = $this->database->executeQuery($sql, [$last_id]);
            while ($obj = $result->fetch_object()) {
                foreach ($obj as $property => $value) {
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * Receive update from subject
     * @link http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     * The <b>SplSubject</b> notifying the observer of an update.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject)
    {
        $game = $this->config->get("game");
        $total_distance = $game['total_distance'];
        $tick_time = $game['tick_time'];

        for ($i=0; $i < $tick_time; $i++) {

            if ($this->distance >= $total_distance) {
                break;
            }

            if ($this->distance > $this->endurance * 100) {
                $speed_subtraction = 5 * ((100 - ($this->strength * 8)) / 100);
            } else {
                $speed_subtraction = 0;
            }

            $this->distance += (5 + $this->speed) - $speed_subtraction;

            if ($this->distance > $total_distance) {
                $this->distance = $total_distance;
            }

            $this->time_elapsed += 1;
        }

        $this->save();
    }

    public function getDistancePercentage()
    {
        $game = $this->config->get("game");
        $total_distance = $game['total_distance'];
        $percentage = round((($this->distance * 100) / $total_distance), 1);
        return ($percentage > 100) ? 100 : $percentage;
    }
}