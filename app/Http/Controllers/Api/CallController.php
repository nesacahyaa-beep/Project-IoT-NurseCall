<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Services\CallService;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index(Request $request)
    {
        $q = Call::with('room')->latest('called_at');
        if ($request->filled('room_id')) $q->where('room_id', $request->room_id);
        if ($request->filled('level'))   $q->where('level', $request->level);
        if ($request->filled('from'))    $q->whereDate('called_at', '>=', $request->from);
        if ($request->filled('to'))      $q->whereDate('called_at', '<=', $request->to);

        return $q->paginate(20);
    }

    public function stats()
    {
        return [
            'total'            => Call::count(),
            'avg_response_sec' => round((float) Call::whereNotNull('response_seconds')->avg('response_seconds'), 1),
            'max_response_sec' => (int) Call::max('response_seconds'),
        ];
    }

    public function accept(Call $call, CallService $svc)
    {
        return $svc->accept($call, $request->user()?->id);
    }

    public function complete(Request $request, Call $call, CallService $svc)
    {
        $data = $request->validate([
            'items'            => 'array',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return $svc->complete($call, $data['items'] ?? []);
    }
}