<?php

namespace Liberta\Scheduler;

abstract class BaseTask implements Task
{
    protected bool $enabled = true;
    protected ?string $cronExpression = null;
    protected ?int $interval = null;
    private ?\DateTimeImmutable $lastRunAt = null;

    public function getName(): string
    {
        return static::class;
    }

    public function getSchedule(): string
    {
        return $this->cronExpression ?? "every {$this->interval} seconds";
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getInterval(): ?int
    {
        return $this->interval;
    }

    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    public function getLastRunAt(): ?\DateTimeImmutable
    {
        return $this->lastRunAt;
    }

    public function setLastRunAt(\DateTimeImmutable $time): void
    {
        $this->lastRunAt = $time;
    }

    public function everyMinutes(int $minutes): static
    {
        $this->interval = $minutes * 60;
        return $this;
    }

    public function everyHour(): static
    {
        return $this->everyMinutes(60);
    }

    public function daily(): static
    {
        $this->cronExpression = '0 0 * * *';
        return $this;
    }

    public function weekly(): static
    {
        $this->cronExpression = '0 0 * * 0';
        return $this;
    }

    public function monthly(): static
    {
        $this->cronExpression = '0 0 1 * *';
        return $this;
    }

    public function cron(string $expression): static
    {
        $this->cronExpression = $expression;
        return $this;
    }

    public function disable(): static
    {
        $this->enabled = false;
        return $this;
    }

    public function enable(): static
    {
        $this->enabled = true;
        return $this;
    }
}
