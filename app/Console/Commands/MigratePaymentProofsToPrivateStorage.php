<?php

namespace App\Console\Commands;

use App\Models\PaymentProof;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MigratePaymentProofsToPrivateStorage extends Command
{
    protected $signature = 'payment-proofs:migrate-private';

    protected $description = 'Safely migrate legacy public Whish receipts to private storage';

    public function handle(): int
    {
        $migrated = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        PaymentProof::query()->orderBy('id')->each(function (PaymentProof $proof) use (
            &$migrated,
            &$skipped,
            &$missing,
            &$failed
        ): void {
            $source = $this->legacyPublicKey($proof);
            $target = $this->privateKey($proof, $source);

            if ($target === null) {
                $this->warn("Proof {$proof->id}: invalid storage key; skipped.");
                $failed++;

                return;
            }

            $private = Storage::disk(config('filesystems.payment_proofs_disk'));
            $public = Storage::disk('public');

            if ($private->exists($target)) {
                $proof->update(['url' => $target, 'public_id' => $target]);

                if ($source !== null && $public->exists($source)) {
                    $public->delete($source);

                    if ($public->exists($source)) {
                        $this->error("Proof {$proof->id}: private copy exists but public copy could not be deleted.");
                        $failed++;

                        return;
                    }
                }

                $this->line("Proof {$proof->id}: already private; skipped.");
                $skipped++;

                return;
            }

            if ($source === null || ! $public->exists($source)) {
                $this->warn("Proof {$proof->id}: source file is missing.");
                $missing++;

                return;
            }

            try {
                $stream = $public->readStream($source);

                if ($stream === false || ! $private->writeStream($target, $stream)) {
                    throw new \RuntimeException('Unable to copy receipt to private storage.');
                }

                if (is_resource($stream)) {
                    fclose($stream);
                }

                if (! $private->exists($target) || $private->size($target) !== $public->size($source)) {
                    $private->delete($target);
                    throw new \RuntimeException('Private copy verification failed.');
                }

                $proof->update(['url' => $target, 'public_id' => $target]);

                $public->delete($source);

                if ($public->exists($source)) {
                    throw new \RuntimeException('Public copy could not be deleted after migration.');
                }

                $this->info("Proof {$proof->id}: migrated.");
                $migrated++;
            } catch (Throwable $exception) {
                $this->error("Proof {$proof->id}: {$exception->getMessage()}");
                $failed++;
            }
        });

        $this->newLine();
        $this->line("Migrated: {$migrated}; skipped: {$skipped}; missing: {$missing}; failed: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function legacyPublicKey(PaymentProof $proof): ?string
    {
        foreach ([$proof->public_id, $proof->url] as $value) {
            if (! is_string($value) || $value === '') {
                continue;
            }

            $path = parse_url($value, PHP_URL_PATH) ?: $value;
            $path = ltrim(str_replace('\\', '/', $path), '/');
            $path = preg_replace('#^storage/#', '', $path);

            if (is_string($path) && str_starts_with($path, 'payment-proofs/') && ! str_contains($path, '..')) {
                return $path;
            }
        }

        if (is_string($proof->public_id) && $proof->public_id !== '') {
            $fallback = 'payment-proofs/'.ltrim(str_replace('\\', '/', $proof->public_id), '/');

            if (! str_contains($fallback, '..') && Storage::disk('public')->exists($fallback)) {
                return $fallback;
            }
        }

        return null;
    }

    private function privateKey(PaymentProof $proof, ?string $source): ?string
    {
        $value = $source !== null
            ? substr($source, strlen('payment-proofs/'))
            : $proof->public_id;

        if (! is_string($value) || $value === '') {
            return null;
        }

        $value = ltrim(str_replace('\\', '/', $value), '/');

        return str_contains($value, '..') ? null : $value;
    }
}
