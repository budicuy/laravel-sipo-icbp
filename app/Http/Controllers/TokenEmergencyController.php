<?php

namespace App\Http\Controllers;

use App\Models\TokenEmergency;
use App\Models\TokenRequest;
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
     * Validate token via AJAX
     */
    public function validateToken(Request $request)
    {
        // Remove the exists validation to handle custom error messages
        $request->validate([
            'token' => 'required|string'
        ]);

        $currentUserId = Auth::id();

        // Check if token exists
        $existingToken = TokenEmergency::where('token', $request->token)->first();
        if (!$existingToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Pastikan token yang Anda masukkan benar.'
            ], 404);
        }

        // Check if token is available
        if ($existingToken->status !== TokenEmergency::STATUS_AVAILABLE) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah digunakan atau kadaluarsa.'
            ], 400);
        }

        // Check if token can be used by current user
        if (!$existingToken->canBeUsedBy($currentUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'Token ini bukan milik Anda dan tidak dapat digunakan.'
            ], 403);
        }

        // Mark token as used
        $existingToken->status = TokenEmergency::STATUS_USED;
        $existingToken->used_at = now();
        $existingToken->used_by = $currentUserId;
        $existingToken->save();

        // Store valid token in session
        session(['valid_emergency_token' => $existingToken->token]);

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil divalidasi.'
        ]);
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
        $tokenRequest = TokenRequest::create([
            'requested_by' => Auth::id(),
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'status' => TokenRequest::STATUS_PENDING
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
        $pendingRequests = TokenRequest::with('requester')
            ->where('status', TokenRequest::STATUS_PENDING)
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

        $tokenRequest = TokenRequest::findOrFail($id);

        if ($tokenRequest->status !== TokenRequest::STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Permintaan ini tidak dapat diproses.');
        }

        DB::beginTransaction();
        try {
            // Update the request record
            $tokenRequest->status = TokenRequest::STATUS_APPROVED;
            $tokenRequest->approved_by = Auth::id();
            $tokenRequest->approved_at = now();
            $tokenRequest->notes = $request->notes;
            $tokenRequest->save();

            // Generate tokens for the user
            TokenEmergency::generateMultipleTokens(
                $tokenRequest->quantity,
                6,
                $tokenRequest->requested_by,
                Auth::id()
            );

            DB::commit();

            return redirect()->route('token-emergency.monitoring')
                ->with('success', 'Permintaan token telah disetujui. ' .
                    $tokenRequest->quantity . ' token telah digenerate.');
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

        $tokenRequest = TokenRequest::findOrFail($id);

        if ($tokenRequest->status !== TokenRequest::STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Permintaan ini tidak dapat diproses.');
        }

        $tokenRequest->status = TokenRequest::STATUS_REJECTED;
        $tokenRequest->approved_by = Auth::id();
        $tokenRequest->approved_at = now();
        $tokenRequest->rejection_reason = $request->rejection_reason;
        $tokenRequest->save();

        return redirect()->route('token-emergency.monitoring')
            ->with('success', 'Permintaan token telah ditolak.');
    }

    /**
     * Show token monitoring dashboard.
     */
    public function monitoring()
    {
        $usersWithLowTokens = TokenEmergency::getUsersWithLowTokens(5);
        $pendingRequestsCount = TokenRequest::getPendingRequestsCount();
        $pendingRequests = TokenRequest::with('requester')
            ->where('status', TokenRequest::STATUS_PENDING)
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

        // Check if user has pending request
        $hasPendingRequest = TokenRequest::where('requested_by', $userId)
            ->where('status', TokenRequest::STATUS_PENDING)
            ->exists();

        // Get rejected requests with their details
        $rejectedRequests = TokenRequest::where('requested_by', $userId)
            ->where('status', TokenRequest::STATUS_REJECTED)
            ->orderBy('approved_at', 'desc')
            ->take(5) // Show last 5 rejected requests
            ->get();

        return view('token-emergency.my-tokens', compact('tokens', 'availableTokensCount', 'hasPendingRequest', 'rejectedRequests'));
    }

    /**
     * API endpoint to get all pending requests
     */
    public function apiPendingRequests()
    {
        $pendingRequests = TokenRequest::with('requester')
            ->where('status', TokenRequest::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedRequests = $pendingRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'requester' => [
                    'nama_lengkap' => $request->requester->nama_lengkap,
                    'username' => $request->requester->username
                ],
                'quantity' => $request->quantity,
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

        $tokens = TokenEmergency::with(['user', 'generator', 'usedBy'])
            ->where('status', TokenEmergency::STATUS_USED) // Only show used tokens
            ->orderBy('used_at', 'desc') // Order by when they were used
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

        $tokens = TokenEmergency::with(['user', 'generator', 'usedBy'])
            ->where('status', TokenEmergency::STATUS_AVAILABLE) // Only show available tokens
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
     * API endpoint to get request history
     */
    public function apiRequestHistory()
    {
        $perPage = request('per_page', 20);
        $page = request('page', 1);

        $requests = TokenRequest::with(['requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedRequests = $requests->getCollection()->map(function ($request) {
            return [
                'id' => $request->id,
                'requester' => [
                    'nama_lengkap' => $request->requester->nama_lengkap,
                    'username' => $request->requester->username
                ],
                'approver' => $request->approver ? [
                    'nama_lengkap' => $request->approver->nama_lengkap,
                    'username' => $request->approver->username
                ] : null,
                'quantity' => $request->quantity,
                'status' => $request->status,
                'status_badge' => $request->status_badge,
                'notes' => $request->notes,
                'rejection_reason' => $request->rejection_reason,
                'created_at_formatted' => $request->created_at->format('d/m/Y H:i'),
                'approved_at_formatted' => $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : null,
                'time_ago' => $request->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'requests' => new \Illuminate\Pagination\LengthAwarePaginator(
                $formattedRequests,
                $requests->total(),
                $requests->perPage(),
                $requests->currentPage(),
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                    'query' => request()->query(),
                ]
            )
        ]);
    }
}
