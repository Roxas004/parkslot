<?php

namespace App\Services;

use App\Models\FileAttente;
use App\Models\Parking;
use App\Models\Place;
use App\Models\Reservation;
use App\Models\Voiture;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HistoriqueService
{
    public function getQueue(?int $parkingId): array
    {
        $parkings = Parking::orderBy('lib_parking')->get();

        if (! $parkingId) {
            return [
                'parkings'  => $parkings,
                'files'     => collect(),
                'parkingId' => null,
            ];
        }

        $files = FileAttente::with(['voiture.user', 'parking'])
            ->where('parking_id', $parkingId)
            ->orderBy('position')
            ->get();

        return [
            'parkings'  => $parkings,
            'files'     => $files,
            'parkingId' => $parkingId,
        ];
    }

    public function swapPositions(int $fileId1, int $fileId2): void
    {
        DB::transaction(function () use ($fileId1, $fileId2) {
            $file1 = FileAttente::lockForUpdate()->findOrFail($fileId1);
            $file2 = FileAttente::lockForUpdate()->findOrFail($fileId2);

            $pos1 = $file1->position;
            $file1->position = $file2->position;
            $file2->position = $pos1;

            $file1->save();
            $file2->save();
        });
    }

    public function getHistorique(
        ?int    $parkingId,
        ?string $numPlace,
        ?string $search,
        int     $perPage = 20
    ): array {
        $parkings = Parking::orderBy('lib_parking')->get();
        $places   = $parkingId
            ? Place::where('parking_id', $parkingId)->orderBy('num_place')->get()
            : collect();

        $query = Reservation::with(['place.parking', 'voiture.user'])
            ->whereNotNull('fin_reservation')
            ->where('fin_reservation', '<=', now())
            ->orderByDesc('fin_reservation');

        if ($parkingId) {
            $placeIds = Place::where('parking_id', $parkingId)->pluck('id');
            $query->whereIn('place_id', $placeIds);
        }

        if ($numPlace) {
            $placeIds = Place::where('num_place', $numPlace)
                ->when($parkingId, fn ($q) => $q->where('parking_id', $parkingId))
                ->pluck('id');
            $query->whereIn('place_id', $placeIds);
        }

        if ($search) {
            $userIds    = User::where('name', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->pluck('id');
            $voitureIds = Voiture::whereIn('user_id', $userIds)->pluck('id');
            $query->whereIn('voiture_id', $voitureIds);
        }

        return [
            'reservations' => $query->paginate($perPage)->withQueryString(),
            'parkings'     => $parkings,
            'places'       => $places,
            'parkingId'    => $parkingId,
            'numPlace'     => $numPlace,
            'search'       => $search,
        ];
    }
}
