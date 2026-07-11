<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function publicIndex()
    {
        $items = Testimonial::where('is_active', true)->orderBy('id')->get()
            ->map(fn($t) => $this->format($t));
        return response()->json($items);
    }

    public function publicStore(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'content'   => 'required|string',
            'rating'    => 'required|string|in:1,2,3,4,5',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('uploads', 'public');
            $photoUrl = url('storage/' . $path);
        }

        $testimonial = Testimonial::create([
            'name'       => $data['name'],
            'content'    => $data['content'],
            'rating'     => $data['rating'],
            'phone'      => $data['phone'] ?? null,
            'email'      => $data['email'] ?? null,
            'avatar_url' => $photoUrl,
            'is_active'  => false,
        ]);

        return response()->json(['message' => 'Terima kasih! Testimoni Anda akan ditinjau oleh admin.'], 201);
    }

    public function index()
    {
        $items = Testimonial::orderBy('id')->get()->map(fn($t) => $this->formatAdmin($t));
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'content'   => 'required|string',
            'rating'    => 'required|string|in:1,2,3,4,5',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'avatarUrl' => 'nullable|string',
            'isActive'  => 'nullable|boolean',
        ]);

        $testimonial = Testimonial::create([
            'name'             => $data['name'],
            'content'          => $data['content'],
            'rating'           => $data['rating'],
            'phone'            => $data['phone'] ?? null,
            'email'            => $data['email'] ?? null,
            'avatar_url'       => $data['avatarUrl'] ?? null,
            'is_active'        => $data['isActive'] ?? true,
            'is_admin_created' => true,
        ]);

        return response()->json($this->formatAdmin($testimonial), 201);
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $data = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'content'   => 'sometimes|string',
            'rating'    => 'sometimes|string|in:1,2,3,4,5',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'avatarUrl' => 'nullable|string',
            'isActive'  => 'nullable|boolean',
        ]);

        $testimonial->update([
            'name'       => $data['name']      ?? $testimonial->name,
            'content'    => $data['content']   ?? $testimonial->content,
            'rating'     => $data['rating']    ?? $testimonial->rating,
            'phone'      => array_key_exists('phone', $data)     ? $data['phone']     : $testimonial->phone,
            'email'      => array_key_exists('email', $data)     ? $data['email']     : $testimonial->email,
            'avatar_url' => array_key_exists('avatarUrl', $data) ? $data['avatarUrl'] : $testimonial->avatar_url,
            'is_active'  => array_key_exists('isActive', $data)  ? $data['isActive']  : $testimonial->is_active,
        ]);

        return response()->json($this->formatAdmin($testimonial->fresh()));
    }

    public function destroy($id)
    {
        Testimonial::findOrFail($id)->delete();
        return response()->json(['message' => 'Testimoni dihapus']);
    }

    private function format(Testimonial $t): array
    {
        return [
            'id'      => $t->id,
            'name'    => $t->name,
            'content' => $t->content,
            'rating'  => $t->rating,
        ];
    }

    private function formatAdmin(Testimonial $t): array
    {
        return [
            'id'              => $t->id,
            'name'            => $t->name,
            'content'         => $t->content,
            'rating'          => $t->rating,
            'phone'           => $t->phone,
            'email'           => $t->email,
            'avatarUrl'       => $t->avatar_url,
            'isActive'        => (bool) $t->is_active,
            'isAdminCreated'  => (bool) $t->is_admin_created,
        ];
    }
}
