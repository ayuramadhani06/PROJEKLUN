<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetentionPolicyService
{
    private string $hypertable = 'flows_history'; //Ubah nama tabel di sini

    public function sync(int $days): void
    {
        DB::transaction(function () use ($days) {
            $this->removeExistingPolicy();
            $this->applyNewPolicy($days);
        });
    }

    private function removeExistingPolicy(): void
    {
        $exists = DB::selectOne("
            SELECT 1
            FROM timescaledb_information.jobs
            WHERE proc_name = 'policy_retention'
            AND hypertable_name = ?
        ", [$this->hypertable]);

        if ($exists) {
            DB::statement("SELECT remove_retention_policy(?)", [$this->hypertable]);

            Log::info("Retention policy removed");
        }
    }

    private function applyNewPolicy(int $days): void
    {
        DB::statement("
            SELECT add_retention_policy(?, INTERVAL '{$days} days')
        ", [$this->hypertable]);

        Log::info("Retention policy applied: {$days} days");
    }

    public function getActivePolicy(): ?object
    {
        return DB::selectOne("
            SELECT hypertable_name, config->>'drop_after' as drop_after
            FROM timescaledb_information.jobs
            WHERE proc_name = 'policy_retention'
            AND hypertable_name = ?
        ", [$this->hypertable]);
    }
}