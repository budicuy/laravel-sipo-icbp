<?php

namespace App\Http\Controllers;

use App\Models\TokenEmergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TokenEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tokens = TokenEmergency::orderBy('created_at', 'desc')->paginate(20);
        return view('token-emergency.index', compact('tokens'));
    }

    /**
     * Show the form for creating new tokens.
     */
    public function create()
    {
        return view('token-emergency.create');
    }

    /**
     * Generate multiple tokens.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50',
            'length' => 'required|integer|min:4|max:6'
        ]);

        $count = $request->count;
        $length = $request->length;

        $tokens = TokenEmergency::generateMultipleTokens($count, $length);

        return redirect()->route('token-emergency.index')
            ->with('success', "Berhasil generate {$count} token emergency dengan panjang {$length} digit.");
    }

    /**
     * Show the form for token validation.
     */
    public function validateForm()
    {
        return view('token-emergency.validate');
    }

    /**
     * Validate and use token.
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|digits_between:4,6'
        ]);

        $token = TokenEmergency::isValidToken($request->token);

        if (!$token) {
            // Always return JSON response for AJAX requests
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah digunakan.'
            ], 422);
        }

        // Simpan token yang valid ke session
        Session::put('valid_emergency_token', $token->token);

        // Always return JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'message' => 'Token valid! Anda dapat menambahkan rekam medis emergency.',
            'redirect_url' => route('rekam-medis-emergency.create')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $token = TokenEmergency::findOrFail($id);

        if ($token->status === 'used') {
            return redirect()->back()
                ->with('error', 'Token yang sudah digunakan tidak dapat dihapus.');
        }

        $token->delete();

        return redirect()->route('token-emergency.index')
            ->with('success', 'Token berhasil dihapus.');
    }

    /**
     * Clear token from session (logout from emergency mode)
     */
    public function clearToken()
    {
        Session::forget('valid_emergency_token');
        return redirect()->route('dashboard')
            ->with('success', 'Token emergency telah dihapus dari sesi.');
    }
}
