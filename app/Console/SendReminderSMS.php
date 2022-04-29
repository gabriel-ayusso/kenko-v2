<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminderSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia mensagens SMS para os clientes que possuem agendamentos para o dia seguinte.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line("Verificando Agendamentos...");
        $start = Carbon::parse('tomorrow');
        $end = $start->clone()->addDays(1);
        $now = Carbon::parse('now');

        $bookings = Booking::where('date', '>=', $start)->where('date', '<', $end)->whereNull('reminder_date')->orderBy('date')->get();
        $client = new \GuzzleHttp\Client();
        $cont = 0;
        foreach ($bookings as $booking) {
            if ($booking->phone) {
                $values = [
                    'sender' => '11992931816',
                    'date' => $booking->date->format('d/m/Y H:i'),
                    'name' => $booking->name,
                    'phone' => $booking->phone,
                    'service' => $booking->service->name
                ];
                $response = $client->post(env('APP_SMS_LEMBRETE'), ['json' => $values]);
                $booking->reminder_date = $now;
                $booking->save();
                Log::info("SMS enviado para $booking->name $booking->phone");
                $cont++;
            }
        }

        $this->info("Enviados $cont SMS");
    }
}
