<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * scheduled_at was a NOT NULL TIMESTAMP, so MySQL gave it
     * "DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP": any later write to
     * the row (a student registering, a status change) reset the class time to now.
     * A plain DATETIME keeps exactly what the admin typed.
     */
    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE live_classes MODIFY scheduled_at DATETIME NOT NULL');
    }

    public function down(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE live_classes MODIFY scheduled_at TIMESTAMP NOT NULL');
    }
};
