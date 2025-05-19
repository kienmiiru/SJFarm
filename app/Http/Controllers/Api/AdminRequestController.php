<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest; // Agar tidak konflik dengan model Request
use App\Models\Request;

class AdminRequestController extends Controller
{
    public function index()
    {
        $requests = Request::with('fruit', 'distributor', 'status')->get();
        return response()->json($requests);
    }

    public function approve($id)
    {
        $request = Request::with('fruit')->findOrFail($id);

        if ($request->status_id != 1) {
            return response()->json(['message' => 'Permintaan sudah diproses.'], 422);
        }

        $fruit = $request->fruit;
        if ($fruit->stock_in_kg < $request->requested_stock_in_kg) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        // Kurangi stok
        $fruit->stock_in_kg -= $request->requested_stock_in_kg;
        $fruit->save();

        $request->status_id = 2; // approved
        $request->status_changed_date = now();
        $request->status_changed_message = 'Disetujui oleh admin.';
        $request->save();

        return response()->json(['message' => 'Permintaan disetujui.']);
    }

    public function reject(HttpRequest $httpRequest, $id)
    {
        $request = Request::findOrFail($id);

        if ($request->status_id != 1) {
            return response()->json(['message' => 'Permintaan sudah diproses.'], 422);
        }

        $request->status_id = 3; // rejected
        $request->status_changed_date = now();
        $request->status_changed_message = $httpRequest->input('message', 'Permintaan ditolak.');
        $request->save();

        return response()->json(['message' => 'Permintaan ditolak.']);
    }
}
