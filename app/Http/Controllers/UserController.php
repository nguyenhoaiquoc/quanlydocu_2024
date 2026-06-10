<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UserController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show($name)
    {
        $user = User::with(['products', 'followers'])->where('name', $name)->firstOrFail();
        $averageRating = round($user->receivedComments()->avg('rating'), 1);
        $totalReviews = $user->receivedComments()->count();

        return view('users.info', [
            'user' => $user,
            'products' => $user->products,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews
        ]);
    }

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}



public function generateFromPrompt(Request $request)
{
    \Log::info('📥 Yêu cầu AI từ prompt:', $request->all());

    $apiKey = env('COHERE_API_KEY');
    $prompt = $request->prompt;
try {
    $response = Http::withOptions(['verify' => true])
        ->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])
        ->post('https://api.cohere.ai/v1/generate', [
            'model' => 'command',
            'prompt' => $prompt,
            'max_tokens' => 300,
            'temperature' => 0.7,
        ]);

    if ($response->successful()) {
        $text = $response->json()['generations'][0]['text'];
        return response()->json(['description' => trim($text)]);
    } else {
        \Log::error('❌ Lỗi Cohere:', [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers()
        ]);
        return response()->json(['error' => 'Lỗi từ Cohere'], 500);
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    \Log::error('❌ Guzzle Request Exception:', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
    ]);
    return response()->json(['error' => 'Không thể kết nối AI'], 500);
} catch (\Exception $e) {
    \Log::error('❌ Exception khi gọi AI:', ['msg' => $e->getMessage()]);
    return response()->json(['error' => 'Không thể kết nối AI'], 500);
}
}


}
