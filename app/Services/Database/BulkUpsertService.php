<?php

namespace App\Services\Database;

use Illuminate\Database\Eloquent\Model;

class BulkUpsertService
{
    /**
     * @param class-string<Model> $model
     */
    public function upsert(
        string $model,
        array $rows,
        array $uniqueBy,
        array $updateColumns,
        int $chunkSize = 1000
    ): void {

        if (empty($rows)) {
            return;
        }

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            $model::upsert(
                $chunk,
                $uniqueBy,
                $updateColumns
            );
        }
    }
}
