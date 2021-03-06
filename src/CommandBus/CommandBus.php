<?php

namespace Bruli\EventBusBundle\CommandBus;

use Bruli\EventBusBundle\BusOptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandBus
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var BusOptionsResolver
     */
    private $optionsResolver;

    /**
     * CommandBus constructor.
     *
     * @param ContainerInterface $container
     * @param BusOptionsResolver $optionsResolver
     */
    public function __construct(ContainerInterface $container, BusOptionsResolver $optionsResolver)
    {
        $this->container = $container;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command)
    {
        $this->handlePreMiddleWares($command);
        $this->handleCommand($command);
        $this->handlePostMiddleWares($command);
    }

    /**
     * @param CommandInterface $command
     */
    private function handlePreMiddleWares(CommandInterface $command)
    {
        $commandName = '\\' . get_class($command);
        if (true === $this->optionsResolver->preMiddleWareHasCommands($commandName)) {
            foreach ($this->optionsResolver->getPreMiddleWareOptions($commandName) as $preMiddleWareOption) {
                $this->container->get($preMiddleWareOption)->handle($command);
            }
        }
    }

    /**
     * @param CommandInterface $command
     */
    private function handleCommand(CommandInterface $command)
    {
        $this->container->get($this->optionsResolver->getOption('\\' . get_class($command)))->handle($command);
    }

    /**
     * @param CommandInterface $command
     */
    private function handlePostMiddleWares(CommandInterface $command)
    {
        $commandName = '\\' . get_class($command);
        if (true === $this->optionsResolver->postMiddleWareHasCommands($commandName)) {
            foreach ($this->optionsResolver->getPostMiddleWareOptions($commandName) as $postMiddleWareOption) {
                $this->container->get($postMiddleWareOption)->handle($command);
            }
        }
    }
}
