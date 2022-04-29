<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingSuccess;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BookingCreated  $event
     * @return void
     */
    public function handle(BookingCreated $event)
    {
        $booking = $event->booking;

        $admins = User::where('admin', true)->get();
        if ($booking->email)
            Mail::to($booking->email)->bcc($admins)->send(new BookingSuccess($booking));
        else
            Mail::to($admins)->send(new BookingSuccess($booking));

        if ($booking->phone) {
            $client = new \GuzzleHttp\Client();
            $values = [
                'sender' => '11992931816',
                'date' => $booking->date->format('d/m/Y H:i'),
                'name' => $booking->name,
                'phone' => $booking->phone,
                'service' => $booking->service->name
            ];
            //$client->post(env('APP_SMS_AGENDAMENTO'), []);
            $response = $client->post(env('APP_SMS_AGENDAMENTO'), ['json' => $values]);
        }
    }
}
