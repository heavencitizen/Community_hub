<?php

namespace App\Console\Commands;

use App\Models\CommunityAuction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('auctions:manage-lifecycle')]
#[Description('Otomatisasi siklus hidup lelang: menutup lelang yang habis waktu dan menangani wanprestasi pelunasan')]
class ManageAuctionLifecycleCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan siklus hidup lelang komunitas...');

        // 1. Finalisasi lelang aktif yang waktu berakhirnya telah terlewati
        $expiredAuctions = CommunityAuction::where('status', 'active')
            ->where('end_time', '<=', now())
            ->get();

        $finalizedCount = 0;
        foreach ($expiredAuctions as $auction) {
            $auction->autoFinalizeIfNeeded();
            $finalizedCount++;
        }

        // 2. Deteksi pemenang lelang yang wanprestasi (melewati payment_deadline)
        $defaultedAuctions = CommunityAuction::where('status', 'closed')
            ->whereNotNull('winner_id')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<=', now())
            ->whereNull('wanprestasi_at')
            ->get();

        $wanprestasiCount = 0;
        foreach ($defaultedAuctions as $auction) {
            $auction->markAsWanprestasi();
            $wanprestasiCount++;
        }

        $this->info("Selesai. {$finalizedCount} lelang difinalisasi, {$wanprestasiCount} kasus wanprestasi diproses.");

        return Command::SUCCESS;
    }
}
