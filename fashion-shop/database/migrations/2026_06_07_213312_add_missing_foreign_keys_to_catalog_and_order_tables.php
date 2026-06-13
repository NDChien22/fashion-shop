<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->supportsForeignKeys()) {
            return;
        }

        foreach ($this->foreignKeyDefinitions() as $definition) {
            if (! Schema::hasTable($definition['table']) || ! Schema::hasColumn($definition['table'], $definition['column'])) {
                continue;
            }

            $this->alterColumn(
                $definition['table'],
                $definition['column'],
                $definition['up_type'],
                $definition['nullable'],
            );

            $this->cleanOrphanRows($definition);

            if (! $this->foreignKeyExists($definition['table'], $definition['column'])) {
                $this->addForeignKey($definition['table'], $definition['column'], $definition['references'], $definition['on_delete']);
            }
        }
    }

    public function down(): void
    {
        if (! $this->supportsForeignKeys()) {
            return;
        }

        foreach (array_reverse($this->foreignKeyDefinitions()) as $definition) {
            if (! Schema::hasTable($definition['table']) || ! Schema::hasColumn($definition['table'], $definition['column'])) {
                continue;
            }

            if ($this->foreignKeyExists($definition['table'], $definition['column'])) {
                $this->dropForeignKey($definition['table'], $definition['column']);
            }

            $this->alterColumn(
                $definition['table'],
                $definition['column'],
                $definition['down_type'],
                $definition['nullable'],
            );
        }
    }

    /**
     * @return array<int, array{table: string, column: string, references: string, on_delete: string, nullable: bool, up_type: string, down_type: string}>
     */
    private function foreignKeyDefinitions(): array
    {
        return [
            ['table' => 'categories', 'column' => 'parent_id', 'references' => 'categories', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'products', 'column' => 'category_id', 'references' => 'categories', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'products', 'column' => 'collection_id', 'references' => 'collections', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'product_skuses', 'column' => 'product_id', 'references' => 'products', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'payments', 'column' => 'order_id', 'references' => 'orders', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED', 'cleanup' => 'delete'],
            ['table' => 'user_activities', 'column' => 'user_id', 'references' => 'users', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED'],
            ['table' => 'orders', 'column' => 'user_id', 'references' => 'users', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED', 'cleanup' => 'null'],
            ['table' => 'orders', 'column' => 'staff_id', 'references' => 'users', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED'],
            ['table' => 'order_items', 'column' => 'order_id', 'references' => 'orders', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED'],
            ['table' => 'order_items', 'column' => 'product_sku_id', 'references' => 'product_skuses', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED', 'cleanup' => 'delete'],
            ['table' => 'vouchers', 'column' => 'category_id', 'references' => 'categories', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'vouchers', 'column' => 'collection_id', 'references' => 'collections', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'vouchers', 'column' => 'product_id', 'references' => 'products', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'flash_sales', 'column' => 'category_id', 'references' => 'categories', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'flash_sales', 'column' => 'collection_id', 'references' => 'collections', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'flash_sales', 'column' => 'product_id', 'references' => 'products', 'on_delete' => 'SET NULL', 'nullable' => true, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER'],
            ['table' => 'customer_membership_levels', 'column' => 'user_id', 'references' => 'users', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED'],
            ['table' => 'customer_membership_levels', 'column' => 'membership_level_id', 'references' => 'membership_levels', 'on_delete' => 'RESTRICT', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED'],
            ['table' => 'order_vouchers', 'column' => 'order_id', 'references' => 'orders', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'BIGINT UNSIGNED'],
            ['table' => 'order_vouchers', 'column' => 'voucher_id', 'references' => 'vouchers', 'on_delete' => 'RESTRICT', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'BIGINT UNSIGNED'],
            ['table' => 'wishlists', 'column' => 'product_id', 'references' => 'products', 'on_delete' => 'CASCADE', 'nullable' => false, 'up_type' => 'BIGINT UNSIGNED', 'down_type' => 'INTEGER UNSIGNED', 'cleanup' => 'delete'],
        ];
    }

    private function supportsForeignKeys(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column],
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function alterColumn(string $table, string $column, string $type, bool $nullable): void
    {
        $nullability = $nullable ? 'NULL' : 'NOT NULL';

        DB::statement(sprintf('ALTER TABLE `%s` MODIFY `%s` %s %s', $table, $column, $type, $nullability));
    }

    /**
     * @param  array{table: string, column: string, references: string, on_delete: string, nullable: bool, up_type: string, down_type: string, cleanup?: string}  $definition
     */
    private function cleanOrphanRows(array $definition): void
    {
        $cleanup = $definition['cleanup'] ?? null;

        if ($cleanup === null) {
            return;
        }

        $table = $definition['table'];
        $column = $definition['column'];
        $referencesTable = $definition['references'];

        if ($cleanup === 'null') {
            DB::statement(sprintf(
                'UPDATE `%s` AS child LEFT JOIN `%s` AS parent ON parent.`id` = child.`%s` SET child.`%s` = NULL WHERE child.`%s` IS NOT NULL AND parent.`id` IS NULL',
                $table,
                $referencesTable,
                $column,
                $column,
                $column,
            ));

            return;
        }

        DB::statement(sprintf(
            'DELETE child FROM `%s` AS child LEFT JOIN `%s` AS parent ON parent.`id` = child.`%s` WHERE child.`%s` IS NOT NULL AND parent.`id` IS NULL',
            $table,
            $referencesTable,
            $column,
            $column,
        ));
    }

    private function addForeignKey(string $table, string $column, string $referencesTable, string $onDelete): void
    {
        $constraintName = sprintf('%s_%s_foreign', $table, $column);

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s`(`id`) ON DELETE %s',
            $table,
            $constraintName,
            $column,
            $referencesTable,
            $onDelete,
        ));
    }

    private function dropForeignKey(string $table, string $column): void
    {
        $constraintName = sprintf('%s_%s_foreign', $table, $column);

        DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $table, $constraintName));
    }
};
