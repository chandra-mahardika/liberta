<?php

namespace Liberta\Queue\Drivers;

use Liberta\Queue\Job;
use Liberta\Queue\QueueDriver;
use Liberta\SqlBuilder\DB;

class DatabaseQueueDriver implements QueueDriver
{
    private DB $db;

    public function __construct(
        ?DB $db = null,
        private string $table = 'jobs'
    ) {
        $this->db = $db ?? DB::getInstance();
    }

    public function push(Job $job, string $queue = 'default'): string
    {
        return $this->addToQueue($job, $queue, 0);
    }

    public function pop(string $queue = 'default'): ?Job
    {
        $record = $this->db->table($this->table)
            ->where('queue', '=', $queue)
            ->where('reserved_at', '=', null)
            ->where('available_at', '<=', time())
            ->orderBy('id')
            ->first();

        if ($record === null) {
            return null;
        }

        $this->db->table($this->table)
            ->where('id', '=', $record['id'])
            ->update(['reserved_at' => time()]);

        return unserialize($record['payload']);
    }

    public function peek(string $queue = 'default', int $limit = 10): array
    {
        $records = $this->db->table($this->table)
            ->where('queue', '=', $queue)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        return array_map(fn($r) => unserialize($r['payload']), $records);
    }

    public function size(string $queue = 'default'): int
    {
        return $this->db->table($this->table)
            ->where('queue', '=', $queue)
            ->count();
    }

    public function delete(string $id): bool
    {
        return $this->db->table($this->table)->where('id', '=', $id)->delete();
    }

    public function later(int $delay, Job $job, string $queue = 'default'): string
    {
        return $this->addToQueue($job, $queue, $delay);
    }

    public function release(Job $job, int $delay = 0): string
    {
        return $this->later($delay, $job, $job->getQueue());
    }

    public function bulk(array $jobs, string $queue = 'default'): array
    {
        $ids = [];
        foreach ($jobs as $job) {
            $ids[] = $this->push($job, $queue);
        }
        return $ids;
    }

    private function addToQueue(Job $job, string $queue, int $delay): string
    {
        $this->db->table($this->table)->insert([
            'queue' => $queue,
            'payload' => serialize($job),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => time() + $delay,
            'created_at' => time(),
        ]);

        return (string) $this->db->lastInsertId();
    }
}
