<?php

namespace Liberta\Report;

interface ReportGenerator
{
    /**
     * Generate a report from data.
     *
     * @param array<string, mixed> $data     Report data
     * @param array<string, mixed> $options  Generator-specific options
     */
    public function generate(array $data, array $options = []): string;

    /**
     * Generate a report and save to file.
     */
    public function save(string $path, array $data, array $options = []): void;

    /**
     * Get the content type for this report type.
     */
    public function contentType(): string;

    /**
     * Get the file extension for this report type.
     */
    public function extension(): string;
}
