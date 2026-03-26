<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Services\PlacesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function __construct(
        private PlacesService $placesService
    ) {}

    // ──────────────────────────────────────────────
    // Places occupées (réservations actives)
    // ──────────────────────────────────────────────

    public function index(Request $request): View
    {
        $parkingId = $request->integer('parking_id') ?: null;

        return view('admin.place', [
            'reservations' => $this->placesService->getReservations($parkingId),
            'parkings'     => Parking::orderBy('lib_parking')->get(),
            'parkingId'    => $parkingId,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->placesService->deleteReservation($id);

        return redirect()->back()->with('success', 'Réservation supprimée et place libérée.');
    }

    // ──────────────────────────────────────────────
    // Gestion des places (CRUD)
    // ──────────────────────────────────────────────

    public function gestion(Request $request): View
    {
        $parkingId = $request->integer('parking_id') ?: null;

        return view('admin.gestion_places', [
            'places'    => $this->placesService->getPlaces($parkingId),
            'parkings'  => Parking::orderBy('lib_parking')->get(),
            'parkingId' => $parkingId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parking_id' => ['required', 'integer', 'exists:parkings,id'],
            'num_place'  => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z0-9]+([-][a-zA-Z0-9]+)?$/'],
        ], [
            'num_place.regex' => 'Le numéro doit être alphanumérique (ex: 5, A3, 1-10).',
        ]);

        try {
            $this->placesService->creerPlaces(
                (int) $validated['parking_id'],
                $validated['num_place']
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['num_place' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('places.gestion', ['parking_id' => $validated['parking_id']])
            ->with('success', 'Place(s) créée(s) avec succès.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'num_place' => ['required', 'string', 'max:20'],
        ]);

        try {
            $this->placesService->modifierPlace($id, $validated['num_place']);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Place modifiée.');
    }

    public function destroyPlace(int $id): RedirectResponse
    {
        try {
            $this->placesService->supprimerPlace($id);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Place supprimée.');
    }
}
