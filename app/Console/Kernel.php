<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Product;
use App\Notifications\ProductExpired;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $expiredProducts = Product::where('updated_at', '<=', now()->subDays(7))
                ->where('is_approved', 1)
                ->get();

            foreach ($expiredProducts as $product) {
                $notificationExists = $product->user->notifications()
                    ->where('type', ProductExpired::class)
                    ->where('data->product_id', $product->id)
                    ->exists();

                if (!$notificationExists) {
                    $product->user->notify(new ProductExpired($product));
                }
            }
        })->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}