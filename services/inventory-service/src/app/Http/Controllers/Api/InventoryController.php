<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function all()
    {
        try {
            $inventories = Inventory::paginate(10); 
            return $this->successResponse($inventories, 'Inventories fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch inventories', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
