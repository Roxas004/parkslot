<?php

namespace App\Http\Controllers;

use App\Services\PlacesService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Parking;

class PlaceController extends Controller
{
    private PlacesService $placesService;

    public function __construct(PlacesService $placesService)
    {
        $this->placesService = $placesService;
    }

    public function index(Request $request): View
    {
        $parkingId = $request->input('parking_id');

        return view('admin.place', [
            'reservations' => $this->placesService->getReservations($parkingId),
            'parkings' => Parking::all(),
            'parkingId' => $parkingId
        ]);
    }

    public function destroy(int $id)
    {
        $this->placesService->deleteReservation($id);

        return redirect()->back();
    }
}
