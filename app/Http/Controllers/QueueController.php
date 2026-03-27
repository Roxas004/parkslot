<?php

namespace App\Http\Controllers;

use App\Services\GererQueueService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
class QueueController extends Controller
{
    private GererQueueService $gererQueueService;

    public function __construct(GererQueueService $gererQueueService)
    {
        $this->gererQueueService = $gererQueueService;
    }

    public function index(Request $request): View
    {
        $data = $this->gererQueueService->getQueue(
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

        $this->gererQueueService->swapPositions(
            $request->input('file1'),
            $request->input('file2')
        );

        return response()->json(['success' => true]);
    }

}
