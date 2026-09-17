<?php

namespace Liberta\Event;

class EventDispatcher
{
    /**
     * @var array<string, array<int, callable|Listener>>
     * Key = event class name (FQCN)
     * Value = ordered list of listeners
     */
    private array $listeners = [];

    /**
     * @var array<string, array<int, callable|Listener>>
     * Wildcard listeners (listen to all events)
     */
    private array $wildcardListeners = [];

    /**
     * @var array<int, Event> Event stack for nested dispatch detection
     */
    private array $eventStack = [];

    /**
     * Register a listener for an event class.
     *
     * @param class-string<Event> $eventClass  FQCN of the event
     * @param callable|Listener   $listener    Closure or Listener instance
     */
    public function listen(string $eventClass, callable|Listener $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Register a wildcard listener (receives all events).
     *
     * @param callable|Listener $listener
     */
    public function listenAll(callable|Listener $listener): void
    {
        $this->wildcardListeners[] = $listener;
    }

    /**
     * Dispatch an event to all registered listeners.
     *
     * Listeners are called in registration order.
     * Call $event->stopPropagation() to prevent further listeners.
     *
     * @template T of Event
     * @param T $event
     * @return T
     */
    public function dispatch(Event $event): Event
    {
        $eventClass = get_class($event);
        $this->eventStack[] = $event;

        // Regular listeners
        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $this->callListener($listener, $event);
        }

        // Wildcard listeners
        if (!$event->isPropagationStopped()) {
            foreach ($this->wildcardListeners as $listener) {
                if ($event->isPropagationStopped()) {
                    break;
                }

                $this->callListener($listener, $event);
            }
        }

        array_pop($this->eventStack);

        return $event;
    }

    /**
     * Remove all listeners for a specific event class.
     */
    public function removeListeners(string $eventClass): void
    {
        unset($this->listeners[$eventClass]);
    }

    /**
     * Remove all wildcard listeners.
     */
    public function removeWildcardListeners(): void
    {
        $this->wildcardListeners = [];
    }

    /**
     * Get listener count for a specific event.
     */
    public function listenerCount(string $eventClass): int
    {
        return count($this->listeners[$eventClass] ?? []);
    }

    /**
     * Get all registered event class names.
     */
    public function getEventNames(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Clear all listeners.
     */
    public function clear(): void
    {
        $this->listeners = [];
        $this->wildcardListeners = [];
    }

    private function callListener(callable|Listener $listener, Event $event): void
    {
        if ($listener instanceof Listener) {
            $listener->handle($event);
        } else {
            $listener($event);
        }
    }
}
