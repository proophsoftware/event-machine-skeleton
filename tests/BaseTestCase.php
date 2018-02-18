<?php

declare(strict_types=1);

namespace AppTest;

use PHPUnit\Framework\TestCase;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\Messaging\Message;

class BaseTestCase extends TestCase
{
    /**
     * @var EventMachine
     */
    protected $eventMachine;

    protected function setUp()
    {
        $this->eventMachine = new EventMachine();

        $config = include __DIR__ . '/../config/autoload/global.php';

        foreach ($config['event_machine']['descriptions'] as $description) {
            $this->eventMachine->load($description);
        }

        $this->eventMachine->initialize(new EventMachineContainer($this->eventMachine));
    }

    protected function tearDown()
    {
        $this->eventMachine = null;
    }

    protected function message(string $msgName, array $payload = [], array $metadata = []): Message
    {
        return $this->eventMachine->messageFactory()->createMessageFromArray($msgName, [
            'payload' => $payload,
            'metadata' => $metadata
        ]);
    }

    protected function assertRecordedEvent(string $eventName, array $payload = [], array $events): void
    {
        $isRecorded = false;

        foreach ($events as [$evtName, $evtPayload]) {
            if($eventName === $evtName) {
                $isRecorded = true;
                $this->assertEquals($payload, $evtPayload, "Payload of recorded event $evtName does not match with expected payload.");
            }
        }

        $this->assertTrue($isRecorded, "Event $eventName is not recorded");
    }
}
