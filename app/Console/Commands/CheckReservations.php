<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Services\ReservationService;
use App\Models\Place;
use App\Services\AutoQueueService;
class CheckReservations extends Command
{
    protected $signature = 'reservations:check';
    protected $description = 'Libère les places des réservations expirées et attribue aux utilisateurs en file d\'attente';

    protected $autoQueueService;


    public function __construct(AutoQueueService $autoQueueService)
    {
        parent::__construct();
        $this->autoQueueService = $autoQueueService;
    }
    public function handle()
    {
        $now = Carbon::now();

        $reservations = Reservation::where('fin_reservation', '<=', $now)
            ->where('traitee', false)
            ->get();

        foreach ($reservations as $reservation) {

            $place = $reservation->place;

            if ($place) {
                $place->disponible = true;
                $place->save();

                $this->autoQueueService->attribuerPlaceDepuisFile($place);
            }

            $reservation->traitee = true;
            $reservation->save();
        }

        return Command::SUCCESS;
    }
}
