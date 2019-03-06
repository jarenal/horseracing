<?php

namespace Jarenal\App\Model;

use DI\Annotation\Inject;
use Jarenal\Core\Config;
use Jarenal\Core\Model;
use SplObserver;

class Race extends Model implements \SplSubject
{
    public $id;
    public $state;
    public $created_at;
    public $tick;
    protected $horses = [];

    /**
     * @Inject("DI\Container")
     * @var Container $container
     */
    private $container;

    /**
     * @Inject("Jarenal\Core\Config")
     * @var Config $config
     */
    private $config;

    /**
     * @Inject("Jarenal\App\Model\HorseQueries")
     * @var HorseQueries $horseQueries
     */
    private $horseQueries;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Race
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return Race
     */
    public function setState($state)
    {
        $this->state = $state;
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
     * @return Race
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTick()
    {
        return $this->tick;
    }

    /**
     * @param mixed $tick
     * @return Race
     */
    public function setTick($tick)
    {
        $this->tick = $tick;
        return $this;
    }

    public function save()
    {
        $this->database->connect();

        if ($this->id) {
            $sql = "UPDATE `race` SET `state`=%s, `tick`=%s WHERE `id`=%s";
            $this->database->executeQuery($sql, [$this->state, $this->tick, $this->id]);
        } else {
            $sql = "INSERT INTO `race` (`state`) VALUES (%s)";
            $this->database->executeQuery($sql, [$this->state]);
        }



        if (!$this->id) {
            $last_id = $this->database->getLastId();
            $sql = "SELECT * FROM `race` WHERE `id`=%s";
            $result = $this->database->executeQuery($sql, [$last_id]);
            while ($obj = $result->fetch_object()) {
                foreach ($obj as $property => $value) {
                    $this->$property = $value;
                }
            }
        }
    }

    public function getHorses()
    {
        $this->horses = [];

        if ($this->id) {
            $sql = "SET @rank := 0";
            $this->database->executeQuery($sql);
                $sql = "SELECT *, @rank := @rank + 1 AS rank FROM `horse` WHERE `race_id`=%s ORDER BY `time_elapsed` ASC, `distance` DESC";
            $result = $this->database->executeQuery($sql, [$this->id]);

            while ($obj = $result->fetch_object()) {
                $horse = $this->container->make(Horse::class);
                foreach ($obj as $property => $value) {
                    $horse->$property = $value;
                }
                $horse->addRace($this);
                $this->attach($horse);
            }
        }

        return $this->horses;
    }

    public function getHorsesTop3()
    {
        $this->horses = [];

        if ($this->id) {
            $sql = "SET @rank := 0";
            $this->database->executeQuery($sql);
                $sql = "SELECT *, @rank := @rank + 1 AS rank FROM `horse` WHERE `race_id`=%s ORDER BY `time_elapsed` ASC, `distance` DESC LIMIT 3";
            $result = $this->database->executeQuery($sql, [$this->id]);

            while ($obj = $result->fetch_object()) {
                $horse = $this->container->make(Horse::class);
                foreach ($obj as $property => $value) {
                    $horse->$property = $value;
                }
                $horse->addRace($this);
                $this->attach($horse);
            }
        }

        return $this->horses;
    }

    public function nextTick()
    {
        $this->tick++;
        $this->notify();

        $game = $this->config->get("game");
        $total_distance = $game['total_distance'];
        $this->getHorses();
        $pendingHorses = false;

        foreach ($this->horses as $horse) {
            if ($horse->getDistance() < $total_distance) {
                $pendingHorses = true;
            }
        }

        if (!$pendingHorses) {
            $this->state = 0;
            $this->save();
        }

    }

    /**
     * Attach an SplObserver
     * @link http://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function attach(SplObserver $observer)
    {
        $key = spl_object_hash($observer);
        $this->horses[$key] = $observer;
    }

    /**
     * Detach an observer
     * @link http://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function detach(SplObserver $observer)
    {
        $key = spl_object_hash($observer);
        unset($this->horses[$key]);
    }

    /**
     * Notify an observer
     * @link http://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     */
    public function notify()
    {
        if (!$this->horses) {
            $this->getHorses();
        }

        foreach ($this->horses as $observer) {
            $observer->update($this);
        }

        $this->save();
    }

    public function getTimeElapsed()
    {
        $game = $this->config->get("game");
        $tick_time = $game['tick_time'];
        return $this->tick * $tick_time;
    }

    public function getDistanceCovered()
    {
        $game = $this->config->get("game");
        $total_distance = $game['total_distance'];
        $distance = $this->horseQueries->getDistanceCoveredByRaceId($this->id);
        return $distance > $total_distance ? total_distance : $distance;
    }
}
