<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, backup any existing notifications data
        DB::statement('CREATE TABLE notifications_backup AS SELECT * FROM notifications');

        // Drop the existing notifications table
        Schema::dropIfExists('notifications');

        // Create the new notifications table with Laravel's standard structure
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Migrate existing data if any
        $backupData = DB::select('SELECT * FROM notifications_backup');
        foreach ($backupData as $notification) {
            DB::table('notifications')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $notification->user_id,
                'data' => json_encode([
                    'title' => $notification->title ?? '',
                    'message' => $notification->message ?? '',
                    'type' => $notification->type ?? 'info'
                ]),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at
            ]);
        }

        // Drop the backup table
        DB::statement('DROP TABLE notifications_backup');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This reversal might cause data loss, so we'll keep it simple
        Schema::dropIfExists('notifications');

        // Recreate the original structure
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
};
