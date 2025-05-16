<?php

use App\Models\MintRequest;
use App\Services\SuiService;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__, '.env.worker')->load();

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "[Mint Worker] Starting...\n";

$sui = new SuiService();

$pendingRequests = MintRequest::where('status', 'pending')->get();

foreach ($pendingRequests as $request) {
    echo "Minting request ID {$request->id}...\n";

    try {
        $result = $sui->mint($request);

        if ($result['success']) {
            echo "✅ Success: " . $result['digest'] . "\n";
        } else {
            $request->update([
                'status' => 'failed',
                'error_message' => $result['error']
            ]);
            echo "❌ Failed: " . $result['error'] . "\n";
        }

    } catch (\Throwable $e) {
        $request->update([
            'status' => 'failed',
            'error_message' => $e->getMessage()
        ]);
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
}

echo "[Mint Worker] Done.\n";
