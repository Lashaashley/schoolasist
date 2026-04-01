<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class LpoController extends Controller
{
    public function create()
    {
        // Since parent_id is removed, fetch all categories
        $suppliers = Supplier::all(); 
        $categories = Category::all(); 
        $lpos = Lpo::with(['supplier'])->latest()->get();

        return view('lpos.create_lpo', compact('suppliers', 'categories','lpos'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:supply_categories,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Generate LPO number
            $yearMonth = now()->format('Ym'); 
            $sequence = Lpo::whereYear('created_at', now()->year)
                           ->whereMonth('created_at', now()->month)
                           ->count() + 1;
            $lpoNumber = 'LPO-' . $yearMonth . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            // Create LPO header
            $lpo = Lpo::create([
                'lpo_number' => $lpoNumber,
                'supplier_id' => $validatedData['supplier_id'],
            ]);

            $grandTotal = 0;

            // Attach items
            foreach ($validatedData['items'] as $item) {
                $total = $item['quantity'] * $item['price'];
                $grandTotal += $total;

                $lpo->items()->create([
                    'category_id' => $item['category_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $total,
                ]);
            }

            $lpo->update(['grand_total' => $grandTotal]);

            DB::commit();

            return response()->json([
                'message' => 'LPO created successfully',
                'lpo_number' => $lpoNumber,
                'lpo' => $lpo->load('items.category', 'supplier')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating LPO',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateNumber()
    {
        $yearMonth = now()->format('Ym');

        $sequence = Lpo::whereYear('created_at', now()->year)
                       ->whereMonth('created_at', now()->month)
                       ->count() + 1;

        $lpoNumber = 'LPO-' . $yearMonth . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return response()->json(['lpo_number' => $lpoNumber]);
    }

    public function categoryItems($categoryId)
    {
        $items = \App\Models\SupplyItem::where('category_id', $categoryId)
                                        ->get(['id','name']);
        return response()->json($items);
    }

    // Removed subCategories() method because parent_id no longer exists


    
public function details($id)
{
    $lpo = Lpo::with(['supplier', 'items.category'])->findOrFail($id);

    $items = [];

    foreach ($lpo->items as $item) {
        $items[] = [
            'product' => $item->product_name,
            'quantity' => $item->quantity,
            'price' => $item->unit_price,
            'total' => $item->total,
        ];
    }

    // Get unique category names for the LPO items
    $categories = $lpo->items
                      ->filter(fn($item) => $item->category) // only items with category
                      ->pluck('category.name')              // get category names
                      ->unique()                             // remove duplicates
                      ->implode(', ');                       // join as string

    return response()->json([
        'supplier_name' => $lpo->supplier->name,
        'company'       => $lpo->supplier->company,
        'supplier_id'   => $lpo->supplier->id,
        'category_name' => $categories ?: 'N/A', // default to N/A if empty
        'unit_price'    => $lpo->items->first()->unit_price ?? 0,
        'quantity'      => $lpo->items->sum('quantity'),
        'amount'        => $lpo->items->sum('total'),
        'items'         => $items,
    ]);
}

    public function edit($id)
{
    $lpo = Lpo::with('items', 'supplier')->findOrFail($id);
    $suppliers = Supplier::all();
    $categories = Category::all(); // or your Category model
    return view('lpos.edit_lpo', compact('lpo', 'suppliers', 'categories'));
}

public function update(Request $request, $id)
{
    $lpo = Lpo::findOrFail($id);
    $lpo->update($request->only('lpo_number', 'supplier_id', 'status'));

    // Optionally update items...
    return redirect()->route('lpo.create')->with('success', 'LPO updated successfully.');
}

public function destroy($id)
{
    $lpo = Lpo::findOrFail($id);
    $lpo->items()->delete(); // delete related items
    $lpo->delete();

    return redirect()->route('lpo.create')->with('success', 'LPO deleted successfully.');
}
}