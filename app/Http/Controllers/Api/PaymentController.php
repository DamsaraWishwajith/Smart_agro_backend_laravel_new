<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    /**
     * Submit a new payment slip.
     * App eken call karanna (POST /api/submit-payment)
     */
    public function submitPayment(Request $request)
    {
        $this->ensureTableExists();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'slip_image' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:5120', // 5MB max (supports pdf)
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'remark' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $month = $request->month;

        // Check if payment already submitted for this month
        $existing = Payment::where('user_id', $request->user_id)
            ->where('month', $month)
            ->first();

        if ($existing && $existing->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending payment for this month.'
            ], 409);
        }

        if ($existing && $existing->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Payment for this month is already approved.'
            ], 409);
        }

        // Store the uploaded slip
        $slipPath = $request->file('slip_image')->store('payment_slips', 'public');

        // Create or update payment record
        $payment = Payment::updateOrCreate(
            ['user_id' => $request->user_id, 'month' => $month],
            [
                'amount' => 1000.00,
                'slip_image' => $slipPath,
                'remark' => $request->remark,
                'status' => 'pending',
                'admin_notes' => null,
                'approved_at' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment slip uploaded successfully. Waiting for admin approval.',
            'payment' => [
                'id' => $payment->id,
                'month' => $payment->month,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'remark' => $payment->remark,
            ]
        ]);
    }

    /**
     * Get payment status/history for a user.
     * App eken call karanna (POST /api/get-payment-status)
     */
    public function getPaymentStatus(Request $request)
    {
        $this->ensureTableExists();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payments = Payment::where('user_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'month' => $p->month,
                    'amount' => $p->amount,
                    'status' => $p->status,
                    'admin_notes' => $p->admin_notes,
                    'remark' => $p->remark,
                    'slip_url' => $p->slip_image ? asset('storage/' . $p->slip_image) : null,
                    'submitted_at' => $p->created_at?->toISOString(),
                    'approved_at' => $p->approved_at?->toISOString(),
                ];
            });

        $currentMonth = now('Asia/Colombo')->format('Y-m');
        $currentPayment = Payment::where('user_id', $request->user_id)
            ->where('month', $currentMonth)
            ->first();

        return response()->json([
            'success' => true,
            'current_month' => $currentMonth,
            'current_status' => $currentPayment?->status ?? 'not_submitted',
            'payments' => $payments,
        ]);
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('payments')) {
            try {
                \Illuminate\Support\Facades\DB::table('migrations')
                    ->where('migration', 'like', '%create_payments_table%')
                    ->delete();
            } catch (\Exception $e) {}

            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_07_10_100000_create_payments_table.php',
                '--force' => true,
            ]);
        } elseif (!Schema::hasColumn('payments', 'remark')) {
            // Drop and migrate again to add the new remark column
            Schema::dropIfExists('payments');

            try {
                \Illuminate\Support\Facades\DB::table('migrations')
                    ->where('migration', 'like', '%create_payments_table%')
                    ->delete();
            } catch (\Exception $e) {}

            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_07_10_100000_create_payments_table.php',
                '--force' => true,
            ]);
        }
    }
}
