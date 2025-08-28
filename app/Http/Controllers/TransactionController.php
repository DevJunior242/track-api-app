<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Budget;
use App\Mail\BudgetMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();

        return response()->json($transactions);
    }

    public function search(Request $request)
    {

        $query = Transaction::where('user_id', Auth::id());

        if ($request->has("query")) {
            $search = $request->query("query");
            $query->where(function ($q) use ($search) {
                $q->where('category', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween("transaction_date", [$request->start_date, $request->end_date]);
        }
        $transactions = $query->latest()->paginate(10);

        return response()->json([
            'message' => 'Transactions retrieved successfully',
            'transactions' => $transactions
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        try {
            $request->validate([
                'type' => 'required|string|in:revenue,depense',
                'category' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'transaction_date' => 'required|date',
            ]);
           

            $budget = Budget::where('category', $request->category)
                ->whereMonth('month', date('m', strtotime($request->transaction_date)))
                ->whereYear('month', date('Y', strtotime($request->transaction_date)))
                ->first();
            if (!$budget) {
                return response()->json([
                    'message' => "Aucun budget défini pour la catégorie {$request->category}. Veuillez en ajouter si vous voulez."
                ], 400);
            }

            $totalDepenses = Transaction::where('type', 'depense')
                ->where('category', $request->category)
                ->whereMonth('transaction_date', date('m', strtotime($request->transaction_date)))
                ->whereYear('transaction_date', date('Y', strtotime($request->transaction_date)))
                ->sum('amount');

            if ($totalDepenses + $request->amount > $budget->amount) {

                Mail::to(Auth::user()->email)->send(new BudgetMail(
                    $request->category, 
                    $totalDepenses,
                     $budget->amount - $totalDepenses,
                      $budget->amount));
                
                return response()->json([
                    'message' => "Budget dépassé pour la catégorie {$request->category}.",
                    'budget_limit' => $budget->amount,
                    'total_dep' => $totalDepenses,
                    'remaining' => $budget->amount - $totalDepenses
                ], 400);
            }
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,

            ]);
            return response()->json([
                'message' => 'Transaction created successfully',
                'transaction' => $transaction
            ], 201);
        } catch (Exception $e) {
            return response()->json(
                [
                    'ok' => false,
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
          if ($transaction->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'forbidden',

            ], 403);
        }
        return response()->json([
            'message' => 'transaction found',
            'transaction' => $transaction
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {

        if ($transaction->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'forbidden',

            ], 403);
        }
        $request->validate([
            'type' => 'required|string|in:revenue,depense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);
        $transaction->update(
            $request->all()
        );
        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'forbidden',

            ], 403);
        }
        $transaction->delete();
        return response()->json([
            'message' => 'Transaction deleted successfully',

        ]);
    }
}
