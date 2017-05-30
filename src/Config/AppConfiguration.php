<?php
declare(strict_types = 1);

namespace App\Config;

use App\Infrastructure\Logger\PsrErrorLogger;
use bitExpert\Disco\Annotations\Bean;
use bitExpert\Disco\Annotations\Configuration;
use bitExpert\Disco\Annotations\Parameter;
use bitExpert\Disco\Annotations\Parameters;
use bitExpert\Disco\BeanFactoryRegistry;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventMachine\Container\ContainerChain;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\EventMachine;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Prooph\Psr7Middleware\MessageMiddleware;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\ErrorResponseGenerator;

/**
 * Class AppConfiguration
 *
 * @package App\Config
 * @Configuration
 */
class AppConfiguration
{
    /**
     * @Bean
     * @Parameters({
     *  @Parameter({"name" = "config"})
     * })
     * @param array $config
     * @return array
     */
    protected function config(array $config): array
    {
        return $config;
    }

    /**
     * @Bean
     * @Parameters({
     *  @Parameter({"name" = "config.event_machine.descriptions"})
     * })
     * @param array $descriptions
     * @return EventMachine
     */
    public function eventMachine(array $descriptions = []): EventMachine
    {
        //@TODO add config param to enable caching
        $eventMachine = new EventMachine();

        //Load descriptions here or add them to config/autoload/global.php
        foreach ($descriptions as $desc) {
            $eventMachine->load($desc);
        }

        $containerChain = new ContainerChain(
            BeanFactoryRegistry::getInstance(),
            new EventMachineContainer($eventMachine)
        );

        $eventMachine->initialize($containerChain);

        return $eventMachine;
    }

    /**
     * @Bean
     * @return MiddlewareInterface
     */
    public function eventMachineHttpMessageBox(): MiddlewareInterface
    {
        return $this->eventMachine()->httpMessageBox();
    }

    /**
     * @Bean
     * @Parameters({
     *  @Parameter({"name" = "config.pdo.dsn"}),
     *  @Parameter({"name" = "config.pdo.user"}),
     *  @Parameter({"name" = "config.pdo.pwd"})
     * })
     * @param string $dsn
     * @param string $user
     * @param string $pwd
     * @return \PDO
     */
    public function pdoConnection(string $dsn = '', string $user = '', string $pwd = ''): \PDO
    {
        return new \PDO($dsn, $user, $pwd);
    }

    /**
     * @Bean
     * @return PersistenceStrategy
     */
    protected function eventStorePersistenceStrategy(): PersistenceStrategy
    {
        return new PersistenceStrategy\PostgresSingleStreamStrategy();
    }

    /**
     * @Bean({"alias"="EventMachine.EventStore"})
     * @return EventStore
     */
    public function eventStore(): EventStore
    {
        $eventStore = new PostgresEventStore(
            $this->eventMachine()->messageFactory(),
            $this->pdoConnection(),
            $this->eventStorePersistenceStrategy()
        );

        return new TransactionalActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter(TransactionalActionEventEmitterEventStore::ALL_EVENTS)
        );
    }

    /**
     * @Bean({"alias" = "EventMachine.CommandBus"})
     * @return CommandBus
     */
    public function commandBus(): CommandBus
    {
        return new CommandBus();
    }

    /**
     * @Bean({"alias" = "EventMachine.EventBus"})
     * @return EventBus
     */
    public function eventBus(): EventBus
    {
        return new EventBus();
    }

    /**
     * @Bean
     * @Parameters({
     *  @Parameter({"name" = "config.environment"})
     * })
     * @param string $environment
     * @return ErrorHandler
     */
    public function httpErrorHandler($environment = 'prod'): ErrorHandler
    {
        $errorHandler = new ErrorHandler(
            new Response(),
            new ErrorResponseGenerator($environment === 'dev')
        );

        $errorHandler->attachListener(new PsrErrorLogger($this->logger()));

        return $errorHandler;
    }

    /**
     * @Bean
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        $streamHandler = new StreamHandler('php://stderr');

        return new Logger([$streamHandler]);
    }
}
