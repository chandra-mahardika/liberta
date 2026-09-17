<?php

declare(strict_types=1);

namespace Liberta\SqlBuilder\Profiler;

class Profiler
{
    private QueryLog $queryLog;
    private array $sections = [];
    private ?string $currentSection = null;

    public function __construct(
        ?int $slowQueryThresholdMs = 1000
    ) {
        $this->queryLog = new QueryLog($slowQueryThresholdMs);
    }

    public function start(string $name): void
    {
        $this->sections[$name] = [
            'start' => microtime(true),
            'end' => null,
            'label' => $name,
        ];
        $this->currentSection = $name;
    }

    public function stop(string $name): void
    {
        if (isset($this->sections[$name])) {
            $this->sections[$name]['end'] = microtime(true);
        }
        $this->currentSection = null;
    }

    public function stopWatch(string $name, callable $callback): mixed
    {
        $this->start($name);
        $result = $callback();
        $this->stop($name);
        return $result;
    }

    public function logQuery(string $sql, array $bindings, float $timeMs, ?string $connection = null): void
    {
        $this->queryLog->log($sql, $bindings, $timeMs, $connection);
    }

    public function getQueryLog(): QueryLog
    {
        return $this->queryLog;
    }

    public function getSections(): array
    {
        $results = [];

        foreach ($this->sections as $name => $section) {
            $end = $section['end'] ?? microtime(true);
            $results[$name] = [
                'duration_ms' => round(($end - $section['start']) * 1000, 2),
                'start' => $section['start'],
                'end' => $end,
            ];
        }

        return $results;
    }

    public function getReport(): array
    {
        return [
            'sections' => $this->getSections(),
            'queries' => $this->queryLog->getStats(),
            'slow_queries' => $this->queryLog->getSlowQueries(),
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
            ],
        ];
    }

    public function clear(): void
    {
        $this->sections = [];
        $this->queryLog->clear();
    }
}
