<?php

namespace App\Services;

use App\Models\FileAttente;
use App\Models\Parking;
use Illuminate\Support\Facades\DB;
class HistoriqueService
{
    public function getQueue(?int $parkingId): array
    {
        $parkings = Parking::all();

        if (!$parkingId) {
            return [
                'parkings' => $parkings,
                'files' => collect(),
                'parkingId' => null,
            ];
        }
        $files = FileAttente::with(['voiture.user', 'parking'])
            ->where('parking_id', $parkingId)
            ->orderBy('position')
            ->get();

        return [
            'parkings' => $parkings,
            'files' => $files,
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
}
