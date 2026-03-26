<?php

namespace App\Http\Controllers;

use App\Services\HistoriqueService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriqueController extends Controller
{
    public function __construct(
        private HistoriqueService $historiqueService
    ) {}

    public function index(Request $request): View
    {
        $data = $this->historiqueService->getHistorique(
            parkingId: $request->integer('parking_id') ?: null,
            numPlace:  $request->string('num_place')->toString() ?: null,
            search:    $request->string('search')->toString() ?: null,
        );

        return view('admin.historique', $data);
    }
}
