<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest; // Agar tidak konflik dengan model Request
use App\Models\Request;
use Illuminate\Support\Facades\Validator;

class AdminRequestController extends Controller
{
    public function index(HttpRequest $httpRequest)
    {
        $query = Request::with('fruit', 'distributor', 'status')->orderBy('requested_date', 'DESC');

        if ($httpRequest->has('distributor_name')) {
            $query->whereHas('distributor', function ($q) use ($httpRequest) {
                $q->where('name', 'like', '%' . $httpRequest->input('distributor_name') . '%');
            });
        }

        if ($httpRequest->has('status') && in_array($httpRequest->input('status'), ['pending', 'approved', 'rejected'])) {
            $query->whereHas('status', function ($q) use ($httpRequest) {
                $q->where('status', $httpRequest->input('status'));
            });
        }

        $requests = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $requests->items(),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ]
        ]);
    }

    public function approve(HttpRequest $httpRequest, $id)
    {
        $validator = Validator::make($httpRequest->all(), [
            'message' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }
        $request = Request::with('fruit')->findOrFail($id);

        if ($request->status_id != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan sudah diproses.'
            ], 422);
        }

        $fruit = $request->fruit;
        if ($fruit->stock_in_kg < $request->requested_stock_in_kg) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok tidak mencukupi.'
            ], 422);
        }

        // Kurangi stok
        $fruit->stock_in_kg -= $request->requested_stock_in_kg;
        $fruit->save();

        $request->status_id = 2; // approved
        $request->status_changed_date = now();
        $request->status_changed_message = $httpRequest->input('message');
        $request->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan berhasil disetujui.'
        ]);
    }

    public function reject(HttpRequest $httpRequest, $id)
    {
        $validator = Validator::make($httpRequest->all(), [
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'Pesan diperlukan untuk penolakan.',
            'message.string' => 'Pesan penolakan harus berupa teks.',
            'message.max' => 'Pesan penolakan tidak boleh lebih dari 255 karakter.'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }
        $request = Request::findOrFail($id);

        if ($request->status_id != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan sudah diproses.'
            ], 422);
        }

        $request->status_id = 3; // rejected
        $request->status_changed_date = now();
        $request->status_changed_message = $httpRequest->input('message');
        $request->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan berhasil ditolak.'
        ]);
    }
}
