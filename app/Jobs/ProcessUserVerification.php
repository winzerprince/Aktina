<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ProcessUserVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected bool $isVerified;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, bool $isVerified)
    {
        $this->userId = $userId;
        $this->isVerified = $isVerified;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::find($this->userId);

            if (!$user) {
                Log::error("User not found for verification processing", ['user_id' => $this->userId]);
                return;
            }

            Log::info("Processing user verification change", [
                'user_id' => $this->userId,
                'email' => $user->email,
                'is_verified' => $this->isVerified
            ]);

            // Here you can add additional verification processing logic
            // such as sending notification emails, updating related records, etc.

            // Example: Send verification email to user
            // if ($this->isVerified) {
            //     $user->notify(new UserVerifiedNotification());
            // }

        } catch (\Exception $e) {
            Log::error("Failed to process user verification", [
                'user_id' => $this->userId,
                'is_verified' => $this->isVerified,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
