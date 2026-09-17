<?php

namespace Liberta\Event;

/**
 * Base class for all events.
 *
 * Extend this to create typed event classes:
 *
 *   class UserCreated extends Event
 *   {
 *       public function __construct(
 *           public readonly string $userId,
 *           public readonly string $username,
 *       ) {}
 *   }
 *
 *   $dispatcher->dispatch(new UserCreated($id, $name));
 */
class Event
{
    private bool $propagationStopped = false;

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
