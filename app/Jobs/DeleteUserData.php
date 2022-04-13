<?php

namespace App\Jobs;

use App\Export;
use App\Import;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUserData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * @var bool
     */
    protected $deleteObservations = false;

    /**
     * Create a new job instance.
     *
     * @param  \App\User  $user
     * @param  bool  $deleteObservations
     * @return void
     */
    public function __construct(User $user, $deleteObservations)
    {
        $this->user = $user;
        $this->deleteObservations = $deleteObservations;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Export::where('user_id', $this->user->id)->each(function ($export) {
            $export->delete();
        });

        Import::where('user_id', $this->user->id)->each(function ($import) {
            $import->delete();
        });

        $this->user->forceDelete();
    }
}
