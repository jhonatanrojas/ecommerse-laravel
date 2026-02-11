<?php

namespace App\Listeners\Cart;

use App\Events\Cart\CartCheckedOut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CartCheckedOut $event): void
    {
        // Check if order confirmation emails are enabled
        if (!config('cart.emails.order_confirmation_enabled', false)) {
            Log::info('Order confirmation email skipped (disabled in config)', [
                'order_id' => $event->order->id,
                'order_number' => $event->order->order_number,
            ]);
            return;
        }

        $order = $event->order;
        $cart = $event->cart;

        // Log the order confirmation
        Log::info('Order confirmation email triggered', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'total' => $order->total,
        ]);

        // TODO: Implement actual email sending
        // Example:
        // if ($order->user) {
        //     Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
        // }

        // For now, just log that we would send an email
        if ($order->user) {
            Log::info('Order confirmation email would be sent', [
                'to' => $order->user->email,
                'order_number' => $order->order_number,
            ]);
        }
    }
}
