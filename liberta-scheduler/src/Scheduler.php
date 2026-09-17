<?php

namespace Liberta\Scheduler;

class Scheduler
{
    /** @var Task[] */
    private array $tasks = [];

    public function add(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function run(): void
    {
        $now = new \DateTimeImmutable();

        foreach ($this->tasks as $task) {
            if (!$task->isEnabled()) {
                continue;
            }

            if ($this->shouldRun($task, $now)) {
                $task->run();
                $task->setLastRunAt($now);
            }
        }
    }

    public function getDueTasks(): array
    {
        $now = new \DateTimeImmutable();
        $due = [];

        foreach ($this->tasks as $task) {
            if ($task->isEnabled() && $this->shouldRun($task, $now)) {
                $due[] = $task;
            }
        }

        return $due;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    private function shouldRun(Task $task, \DateTimeImmutable $now): bool
    {
        if ($task->getCronExpression() !== null) {
            return $this->matchesCron($task->getCronExpression(), $now);
        }

        if ($task->getInterval() !== null) {
            $lastRun = $task->getLastRunAt();
            if ($lastRun === null) {
                return true;
            }
            $diff = $now->getTimestamp() - $lastRun->getTimestamp();
            return $diff >= $task->getInterval();
        }

        return false;
    }

    private function matchesCron(string $cron, \DateTimeImmutable $time): bool
    {
        $parts = preg_split('/\s+/', trim($cron));
        if (count($parts) !== 5) {
            return false;
        }

        [$minute, $hour, $day, $month, $dayOfWeek] = $parts;

        return $this->matchesField($minute, (int) $time->format('i'))
            && $this->matchesField($hour, (int) $time->format('G'))
            && $this->matchesField($day, (int) $time->format('j'))
            && $this->matchesField($month, (int) $time->format('n'))
            && $this->matchesField($dayOfWeek, (int) $time->format('w'));
    }

    private function matchesField(string $pattern, int $value): bool
    {
        if ($pattern === '*') {
            return true;
        }

        if (str_contains($pattern, ',')) {
            $values = array_map('intval', explode(',', $pattern));
            return in_array($value, $values, true);
        }

        if (str_contains($pattern, '-')) {
            [$min, $max] = explode('-', $pattern);
            return $value >= (int) $min && $value <= (int) $max;
        }

        if (str_contains($pattern, '/')) {
            [$start, $step] = explode('/', $pattern);
            $start = $start === '*' ? 0 : (int) $start;
            $step = (int) $step;
            return $step > 0 && $value >= $start && ($value - $start) % $step === 0;
        }

        return $value === (int) $pattern;
    }
}
