<?php

namespace Jarenal\App\Model;

use DI\Annotation\Inject;
use Jarenal\App\Model\Horse;
use Jarenal\App\Model\Race;
use DI\Container;
use Jarenal\App\Model\RaceQueries;

class Game
{
    /**
     * @Inject("DI\Container")
     * @var Container $container
     */
    private $container;

    /**
     * @Inject("Jarenal\App\Model\RaceQueries")
     * @var RaceQueries $raceQueries
     */
    private $raceQueries;

    /**
     * @Inject("Jarenal\App\Model\HorseQueries")
     * @var HorseQueries $horseQueries
     */
    private $horseQueries;

    public function createRace() {

        $total_active_races = $this->raceQueries->countActiveRaces();

        if ($total_active_races < 3) {
            $race = $this->container->make(Race::class);
            $race->setState(1);
            $race->save();

            for ($i=0; $i<8; $i++) {
                $horse = $this->container->make(Horse::class);
                $horse->setSpeed(rand(0, 100)/10)
                    ->setStrength(rand(0, 100)/10)
                    ->setEndurance(rand(0, 100)/10)
                    ->setDistance(0)
                    ->addRace($race);
                $horse->save();
                unset($horse);
            }
        }
    }

    public function nextTick()
    {
        $active_races = $this->raceQueries->getActiveRaces();

        foreach ($active_races as $race) {
            $race->nextTick();
        }
    }

    public function getStatistics()
    {
        $activeRaces = $this->raceQueries->getActiveRaces();
        $lastRaces = $this->raceQueries->getLastFiveRaces();
        $bestHorse = $this->horseQueries->getBestHorse();
        return ['activeRaces' => $activeRaces, 'lastRaces' => $lastRaces, 'bestHorse' => $bestHorse];
    }
}
