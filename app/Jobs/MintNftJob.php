<?php

namespace App\Jobs;

use App\Models\MintRequest;
use App\Services\SuiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MintNftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected MintRequest $request;

    public function __construct(MintRequest $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $sui = new SuiService();

        $result = $sui->mint($this->request);

        if (!$result['success']) {
            $this->request->update([
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Unknown error',
            ]);
        }

    }
}
