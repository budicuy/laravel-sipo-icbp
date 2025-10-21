<?php

namespace App\Http\Controllers;

use App\Models\TokenEmergency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokenEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to monitoring page since the index page is removed
        return redirect()->route('token-emergency.monitoring');
    }

    /**
     * Show the form for creating new tokens.
     */
    public function create()
    {
        $users = User::where('role', 'User')->get();
        return view('token-emergency.create', compact('users'));
    }

    /**
     * Generate multiple tokens.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50',
            'length' => 'required|integer|min:4|max:6',
            'user_id' => 'nullable|exists:user,id_user',
            'notes' => 'nullable|string|max:255'
        ]);

        TokenEmergency::generateMultipleTokens(
            $request->count,
            $request->length,
            $request->user_id,
            Auth::id()
        );

        return redirect()->route('token-emergency.monitoring')
            ->with('success', "Berhasil generate {$request->count} token emergency dengan panjang {$request->length} digit.");
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

        return redirect()->route('token-emergency.monitoring')
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

    /**
     * Show token request form.
     */
    public function requestForm()
    {
        return view('token-emergency.request');
    }

    /**
     * Store token request.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:255'
        ]);

        // Create a token request record
        $tokenRequest = TokenEmergency::create([
            'token' => 'REQ-' . time(), // Temporary token for request
            'status' => 'used', // Mark as used until approved
            'request_quantity' => $request->quantity,
            'request_status' => TokenEmergency::REQUEST_STATUS_PENDING,
            'requested_by' => Auth::id(),
            'notes' => $request->notes
        ]);

        // Check if request is from AJAX (modal)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan token telah dikirim. Menunggu persetujuan admin.'
            ]);
        }

        return redirect()->route('token-emergency.my-tokens')
            ->with('success', 'Permintaan token telah dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Show pending requests for admin.
     */
    public function pendingRequests()
    {
        $pendingRequests = TokenEmergency::with('requester')
            ->where('request_status', TokenEmergency::REQUEST_STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('token-emergency.pending-requests', compact('pendingRequests'));
    }

    /**
     * Approve token request.
     */
    public function approveRequest(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255'
        ]);

        $tokenRequest = TokenEmergency::findOrFail($id);

        if ($tokenRequest->request_status !== TokenEmergency::REQUEST_STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Permintaan ini tidak dapat diproses.');
        }

        DB::beginTransaction();
        try {
            // Update the request record
            $tokenRequest->request_status = TokenEmergency::REQUEST_STATUS_APPROVED;
            $tokenRequest->request_approved_at = now();
            $tokenRequest->request_approved_by = Auth::id();
            $tokenRequest->notes = $request->notes;
            $tokenRequest->save();

            // Generate tokens for the user
            TokenEmergency::generateMultipleTokens(
                $tokenRequest->request_quantity,
                6,
                $tokenRequest->requested_by,
                Auth::id()
            );

            DB::commit();

            return redirect()->route('token-emergency.pending-requests')
                ->with('success', 'Permintaan token telah disetujui. ' .
                    $tokenRequest->request_quantity . ' token telah digenerate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject token request.
     */
    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $tokenRequest = TokenEmergency::findOrFail($id);

        if ($tokenRequest->request_status !== TokenEmergency::REQUEST_STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Permintaan ini tidak dapat diproses.');
        }

        $tokenRequest->request_status = TokenEmergency::REQUEST_STATUS_REJECTED;
        $tokenRequest->request_approved_at = now();
        $tokenRequest->request_approved_by = Auth::id();
        $tokenRequest->notes = 'Ditolak: ' . $request->rejection_reason;
        $tokenRequest->save();

        return redirect()->route('token-emergency.pending-requests')
            ->with('success', 'Permintaan token telah ditolak.');
    }

    /**
     * Show token monitoring dashboard.
     */
    public function monitoring()
    {
        $usersWithLowTokens = TokenEmergency::getUsersWithLowTokens(5);
        $pendingRequestsCount = TokenEmergency::getPendingRequestsCount();
        $pendingRequests = TokenEmergency::with('requester')
            ->where('request_status', TokenEmergency::REQUEST_STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $totalUsers = User::where('role', 'User')->count();
        $usersWithTokens = User::where('role', 'User')
            ->whereHas('tokens', function ($query) {
                $query->where('status', TokenEmergency::STATUS_AVAILABLE);
            })
            ->count();
        $totalAvailableTokens = TokenEmergency::where('status', TokenEmergency::STATUS_AVAILABLE)->count();
        $totalUsedTokens = TokenEmergency::where('status', TokenEmergency::STATUS_USED)->count();

        return view('token-emergency.monitoring', compact(
            'usersWithLowTokens',
            'pendingRequestsCount',
            'pendingRequests',
            'totalUsers',
            'usersWithTokens',
            'totalAvailableTokens',
            'totalUsedTokens'
        ));
    }

    /**
     * Show token audit trail.
     */
    public function auditTrail()
    {
        $auditTrail = TokenEmergency::getAuditTrail();

        return view('token-emergency.audit-trail', compact('auditTrail'));
    }

    /**
     * Show user profile with tokens.
     */
    public function userProfile($userId)
    {
        $user = User::findOrFail($userId);
        $tokens = TokenEmergency::getUserTokens($userId);

        return view('token-emergency.user-profile', compact('user', 'tokens'));
    }

    /**
     * Show user's tokens
     */
    public function myTokens()
    {
        $userId = Auth::id();
        $tokens = TokenEmergency::getUserTokens($userId);
        $availableTokensCount = TokenEmergency::getAvailableTokensCount($userId);

        return view('token-emergency.my-tokens', compact('tokens', 'availableTokensCount'));
    }

    /**
     * API endpoint to get all pending requests
     */
    public function apiPendingRequests()
    {
        $pendingRequests = TokenEmergency::with('requester')
            ->where('request_status', TokenEmergency::REQUEST_STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedRequests = $pendingRequests->map(function ($request) {
            return [
                'id_token' => $request->id_token,
                'requester' => [
                    'nama_lengkap' => $request->requester->nama_lengkap,
                    'username' => $request->requester->username
                ],
                'request_quantity' => $request->request_quantity,
                'notes' => $request->notes,
                'created_at_formatted' => $request->created_at->format('d/m/Y H:i'),
                'time_ago' => $request->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'requests' => $formattedRequests
        ]);
    }

    /**
     * API endpoint to get audit trail data
     */
    public function apiAuditTrail()
    {
        $perPage = request('per_page', 20);
        $page = request('page', 1);

        $tokens = TokenEmergency::with(['user', 'generator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedTokens = $tokens->getCollection()->map(function ($token) {
            return [
                'id_token' => $token->id_token,
                'token' => $token->token,
                'user' => $token->user ? [
                    'nama_lengkap' => $token->user->nama_lengkap,
                    'username' => $token->user->username
                ] : null,
                'generator' => $token->generator ? [
                    'nama_lengkap' => $token->generator->nama_lengkap,
                    'username' => $token->generator->username
                ] : null,
                'status' => $token->status,
                'status_badge' => $token->status_badge,
                'created_at_formatted' => $token->created_at->format('d/m/Y H:i'),
                'used_at_formatted' => $token->used_at ? $token->used_at->format('d/m/Y H:i') : null,
                'time_ago' => $token->created_at->diffForHumans(),
                'notes' => $token->notes
            ];
        });

        return response()->json([
            'tokens' => new \Illuminate\Pagination\LengthAwarePaginator(
                $formattedTokens,
                $tokens->total(),
                $tokens->perPage(),
                $tokens->currentPage(),
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                    'query' => request()->query(),
                ]
            )
        ]);
    }

    /**
     * API endpoint to get tokens for management
     */
    public function apiManageTokens()
    {
        $perPage = request('per_page', 20);
        $page = request('page', 1);

        $tokens = TokenEmergency::with(['user', 'generator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedTokens = $tokens->getCollection()->map(function ($token) {
            return [
                'id_token' => $token->id_token,
                'token' => $token->token,
                'user' => $token->user ? [
                    'nama_lengkap' => $token->user->nama_lengkap,
                    'username' => $token->user->username
                ] : null,
                'generator' => $token->generator ? [
                    'nama_lengkap' => $token->generator->nama_lengkap,
                    'username' => $token->generator->username
                ] : null,
                'status' => $token->status,
                'status_badge' => $token->status_badge,
                'created_at_formatted' => $token->created_at->format('d/m/Y H:i'),
                'used_at_formatted' => $token->used_at ? $token->used_at->format('d/m/Y H:i') : null,
                'time_ago' => $token->created_at->diffForHumans(),
                'notes' => $token->notes
            ];
        });

        return response()->json([
            'tokens' => new \Illuminate\Pagination\LengthAwarePaginator(
                $formattedTokens,
                $tokens->total(),
                $tokens->perPage(),
                $tokens->currentPage(),
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                    'query' => request()->query(),
                ]
            )
        ]);
    }
}
