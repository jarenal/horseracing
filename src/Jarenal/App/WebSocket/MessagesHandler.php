<?php

namespace Jarenal\App\WebSocket;

use DI\Annotation\Inject;
use Jarenal\App\Model\Game;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Jarenal\App\Model\RaceQueries;
use Symfony\Component\Serializer\Serializer;

class MessagesHandler implements MessageComponentInterface
{
    public $clients;

    /**
     * @Inject("Jarenal\App\Model\Game")
     * @var Game $game
     */
    private $game;

    /**
     * @Inject("Jarenal\App\Model\RaceQueries")
     * @var RaceQueries $raceQueries
     */
    private $raceQueries;

    /**
     * @Inject("Symfony\Component\Serializer\Serializer")
     * @var Serializer $serializer
     */
    private $serializer;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        $response = ["type" => "statistics", "data" => $this->game->getStatistics()];
        $json = $this->serializer->serialize($response, 'json');
        $conn->send($json);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).
     * SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and
     * bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        switch ($msg) {
            case "create":
                echo "Create race event\n";
                $this->game->createRace();
                echo "Race created successfully!\n";
                break;
            case "progress":
                echo "Next tick event\n";
                $this->game->nextTick();
                echo "New tick generated!\n";
                break;
        }

        $response = ["type" => "statistics", "data" => $this->game->getStatistics()];
        $json = $this->serializer->serialize($response, 'json');

        foreach ($this->clients as $client) {
            $client->send($json);
        }
    }

    public function getClients()
    {
        return $this->clients;
    }
}
