<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Mark card orders that stayed unpaid past the payment timeout as failed, so students can buy again';

    public function handle(): int
    {
        $count = Order::expireStalePending();

        $this->info("Expired {$count} pending order(s).");

        return self::SUCCESS;
    }
}
