<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SQLite doesn't support ALTER CHECK constraints, so we need to recreate the tables.
     */
    public function up(): void
    {
        // Fix quote_requests table
        $this->rebuildTable('quote_requests', [
            'pending', 'processing', 'completed', 'cancelled', 'expired',
        ]);

        // Fix quote_responses table
        $this->rebuildTable('quote_responses', [
            'pending', 'submitted', 'declined', 'timeout', 'accepted', 'rejected',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: We don't want to revert to the broken CHECK constraints
    }

    private function rebuildTable(string $tableName, array $allowedStatuses): void
    {
        $statusList = implode(', ', array_map(fn ($s) => "'{$s}'", $allowedStatuses));

        // Get current table schema
        $currentSchema = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
        if (! $currentSchema) {
            return;
        }

        // Extract the schema and update the CHECK constraint
        $sql = $currentSchema->sql;

        // Replace the existing CHECK constraint with updated one
        $pattern = '/check\s*\(\s*"status"\s+in\s*\([^)]+\)\s*\)/i';
        $replacement = "check (\"status\" in ({$statusList}))";
        $newSql = preg_replace($pattern, $replacement, $sql);

        if ($newSql === $sql) {
            // No CHECK constraint found, nothing to do
            return;
        }

        // Rename to temp table
        $tempTable = $tableName.'_temp_rebuild';
        $newSql = str_replace(
            "CREATE TABLE \"{$tableName}\"",
            "CREATE TABLE \"{$tempTable}\"",
            $newSql
        );

        DB::statement('PRAGMA foreign_keys=OFF');

        try {
            // Create new table with correct CHECK constraint
            DB::statement($newSql);

            // Copy data
            DB::statement("INSERT INTO \"{$tempTable}\" SELECT * FROM \"{$tableName}\"");

            // Drop old table
            DB::statement("DROP TABLE \"{$tableName}\"");

            // Rename new table
            DB::statement("ALTER TABLE \"{$tempTable}\" RENAME TO \"{$tableName}\"");
        } finally {
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }
};
