<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Budget::where('user_id', Auth::id())->get();

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m-d',
        ]);

        $budget = Budget::create([
            'user_id' => Auth::id(),
            'category' => $request->category,
            'amount' => $request->amount,
            'month' => $request->month,
        ]);
        
        return response()->json(['message' => 'Budget ajouté avec succès', 'budget' => $budget], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::where('user_id', Auth::id())->find($id);

        if (!$budget) {
            return response()->json(['message' => 'Budget introuvable'], 404);
        }

        $budget->delete();
        return response()->json(['message' => 'Budget supprimé avec succès'], 200);
    }
}
