<?php

namespace App\Services;

use App\Models\DigitalAsset;
use App\Models\MintRequest;

class SuiService
{
    protected string $packageId = '0xcdb3d074f5cf8a60685bf6e0c5ae745903b2e7ec756e0793473941053a43d445';

    public function mint(MintRequest $request): array
    {
        $asset = DigitalAsset::findOrFail($request->digital_asset_id);

        $title = escapeshellarg($asset->title);
        $desc = escapeshellarg($asset->description ?? '');

        $cmd = "docker exec sui_cli sui client call " .
            "--package {$this->packageId} " .
            "--module nft --function mint " .
            "--args $title $desc --gas-budget 5000000";

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        $outputText = implode("\n", $output);

        if ($returnCode !== 0 || str_contains($outputText, 'Error')) {
            return [
                'success' => false,
                'error' => $outputText,
            ];
        }

        if (preg_match('/Transaction Digest:\s+([a-zA-Z0-9]+)/', $outputText, $matches)) {
            $digest = $matches[1];

            $request->update([
                'status' => 'success',
                'digest' => $digest,
            ]);

            $asset->update([
                'is_minted' => true,
                'minted_url' => "https://explorer.sui.io/tx/$digest?network=testnet",
            ]);

            return [
                'success' => true,
                'digest' => $digest,
                'output' => $outputText
            ];
        }

        return [
            'success' => false,
            'error' => 'Digest not found in output.',
        ];
    }
}
