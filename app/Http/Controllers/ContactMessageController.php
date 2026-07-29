<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create([
            'name'    => $data['name'],
            'phone'   => $data['phone'],
            'email'   => $data['email'],
            'message' => $data['message'],
            'is_read' => false,
        ]);

        $last4 = substr($message->phone, -4);

        return response()->json([
            'message' => "Pesan dari {$message->phone} berhasil dikirim!",
        ], 201);
    }

    public function index(Request $request)
    {
        $query = ContactMessage::orderBy('created_at', 'desc');

        if ($request->is_read !== null) {
            $query->where('is_read', filter_var($request->is_read, FILTER_VALIDATE_BOOLEAN));
        }

        $messages = $query->get();

        return response()->json($messages);
    }

    public function markRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);
        return response()->json(['message' => 'Pesan ditandai sudah dibaca']);
    }

    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();
        return response()->json(['message' => 'Pesan dihapus']);
    }
}
