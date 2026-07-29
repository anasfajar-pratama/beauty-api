<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    public function publicIndex()
    {
        $faqs = Cache::remember('faqs', 3600, function () {
            return Faq::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($f) => $this->format($f));
        });

        return response()->json($faqs);
    }

    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->get()->map(fn($f) => $this->formatAdmin($f));
        return response()->json($faqs);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string',
            'answer'     => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $faq = Faq::create([
            'question'   => $data['question'],
            'answer'     => $data['answer'],
            'sort_order' => $data['sort_order'] ?? Faq::max('sort_order') + 1,
            'is_active'  => $data['is_active'] ?? true,
        ]);

        Cache::forget('faqs');

        return response()->json($this->formatAdmin($faq), 201);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $data = $request->validate([
            'question'   => 'sometimes|string',
            'answer'     => 'sometimes|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $faq->update(array_filter($data, fn($v) => $v !== null));

        Cache::forget('faqs');

        return response()->json($this->formatAdmin($faq->fresh()));
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();
        Cache::forget('faqs');
        return response()->json(['message' => 'FAQ dihapus']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*.id'         => 'required|exists:faqs,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Faq::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        Cache::forget('faqs');

        return response()->json(['message' => 'Urutan FAQ disimpan']);
    }

    private function format($faq): array
    {
        return [
            'id'       => $faq->id,
            'question' => $faq->question,
            'answer'   => $faq->answer,
        ];
    }

    private function formatAdmin($faq): array
    {
        return [
            'id'         => $faq->id,
            'question'   => $faq->question,
            'answer'     => $faq->answer,
            'sort_order' => $faq->sort_order,
            'is_active'  => (bool) $faq->is_active,
        ];
    }
}
