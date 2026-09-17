<?php

declare(strict_types=1);

namespace Liberta\SqlBuilder\Profiler;

class QueryLog
{
    private array $queries = [];
    private int $totalTime = 0;
    private int $slowQueryThreshold;
    private int $queryCount = 0;
    private int $slowQueryCount = 0;

    public function __construct(
        int $slowQueryThresholdMs = 1000
    ) {
        $this->slowQueryThreshold = $slowQueryThresholdMs;
    }

    public function log(string $sql, array $bindings, float $timeMs, ?string $connection = null): void
    {
        $query = [
            'sql' => $sql,
            'bindings' => $bindings,
            'time_ms' => $timeMs,
            'connection' => $connection,
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(true),
        ];

        $this->queries[] = $query;
        $this->totalTime += (int) $timeMs;
        $this->queryCount++;

        if ($timeMs >= $this->slowQueryThreshold) {
            $this->slowQueryCount++;
            $query['slow'] = true;
        }

        // Keep only last 1000 queries in memory
        if (count($this->queries) > 1000) {
            array_shift($this->queries);
        }
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getSlowQueries(): array
    {
        return array_filter($this->queries, fn($q) => ($q['time_ms'] ?? 0) >= $this->slowQueryThreshold);
    }

    public function getAverageTime(): float
    {
        return $this->queryCount > 0 ? $this->totalTime / $this->queryCount : 0;
    }

    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    public function getSlowQueryCount(): int
    {
        return $this->slowQueryCount;
    }

    public function getStats(): array
    {
        return [
            'total_queries' => $this->queryCount,
            'total_time_ms' => $this->totalTime,
            'average_time_ms' => $this->getAverageTime(),
            'slow_queries' => $this->slowQueryCount,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }

    public function clear(): void
    {
        $this->queries = [];
        $this->totalTime = 0;
        $this->queryCount = 0;
        $this->slowQueryCount = 0;
    }
}
