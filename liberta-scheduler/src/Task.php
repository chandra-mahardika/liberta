<?php

namespace Liberta\Scheduler;

interface Task
{
    public function run(): void;
    public function getName(): string;
    public function getSchedule(): string;
    public function isEnabled(): bool;
    public function getInterval(): ?int;
    public function getCronExpression(): ?string;
    public function getLastRunAt(): ?\DateTimeImmutable;
    public function setLastRunAt(\DateTimeImmutable $time): void;
}
