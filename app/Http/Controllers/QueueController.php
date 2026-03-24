<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\HistoriqueService;
use Illuminate\Http\JsonResponse;
class QueueController extends Controller
{
    private HistoriqueService $historiqueService;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historiqueService = $historiqueService;
    }

    public function index(Request $request): View
    {
        $data = $this->historiqueService->getQueue(
            $request->get('parking_id')
        );

        return view('admin.queue', $data);
    }
    public function swap(Request $request): JsonResponse
    {
        $request->validate([
            'file1' => 'required|integer|exists:file_attente,id',
            'file2' => 'required|integer|exists:file_attente,id'
        ]);

        $this->historiqueService->swapPositions(
            $request->input('file1'),
            $request->input('file2')
        );

        return response()->json(['success' => true]);
    }
}
