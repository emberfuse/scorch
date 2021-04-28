<?php

namespace Cratespace\Sentinel\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Cratespace\Sentinel\Support\Traits\HasUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Sentinel\Contracts\Actions\DeletesUsers;

class DeleteUserJob implements ShouldQueue
{
    use HasUser;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Instance of user requested to be deleted.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable
     *
     * @return void
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param \App\Contracts\Auth\DeletesUsers $deletor
     *
     * @return void
     */
    public function handle(DeletesUsers $deletor)
    {
        try {
            $deletor->delete($this->user);
        } catch (Throwable $e) {
            logger()->error($e->getMessage(), ['user' => $this->user]);

            throw $e;
        }
    }
}
