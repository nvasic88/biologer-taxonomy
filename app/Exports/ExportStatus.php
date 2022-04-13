<?php

namespace App\Exports;

use MyCLabs\Enum\Enum;

class ExportStatus extends Enum
{
    public const QUEUED = 'queued';
    public const EXPORTING = 'exporting';
    public const FAILED = 'failed';
    public const FINISHED = 'finished';

    /**
     * Statuses that mean the export is in progress.
     *
     * @return array
     */
    public static function inProgress()
    {
        return [static::QUEUED, static::EXPORTING];
    }
}
