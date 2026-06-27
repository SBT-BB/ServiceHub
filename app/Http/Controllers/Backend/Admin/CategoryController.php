<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::with('vehicle');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('vehicle_name', fn($row) => $row->vehicle ? $row->vehicle->vehicle_name : 'N/A')
                ->addColumn('status_badge', fn($row) => $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function($row) {
                    $editBtn = '<a href="' . route('admin.categories.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Category"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.categories.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.Category.Index');
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 1)->get();
        return view('Backend.Admin.Category.Form', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name'                => 'required|string|max:255',
            'vehicle_id'                   => 'required|exists:vehicles,id',
            'min_score'                    => 'required|integer|min:0',
            'max_score'                    => 'required|integer|gte:min_score',
            'base_fare'                    => 'required|numeric|min:0',
            'weekend_surcharge_percent'   => 'nullable|numeric|min:0|max:100',
            'month_end_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'peak_time_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'peak_time_start'              => 'nullable',
            'peak_time_end'                => 'nullable',
            'status'                       => 'required|boolean',
        ]);
        $category = Category::create($data);
        return response()->json(['success' => true, 'message' => 'Category created successfully.', 'data' => $category]);
    }

    public function show($id)
    {
        $category = Category::with('vehicle')->findOrFail($id);
        return response()->json($category);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $vehicles = Vehicle::where('status', 1)->get();
        return view('Backend.Admin.Category.Form', compact('category', 'vehicles'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'category_name'                => 'required|string|max:255',
            'vehicle_id'                   => 'required|exists:vehicles,id',
            'min_score'                    => 'required|integer|min:0',
            'max_score'                    => 'required|integer|gte:min_score',
            'base_fare'                    => 'required|numeric|min:0',
            'price_per_point'              => 'nullable|numeric|min:0',
            'weekend_surcharge_percent'   => 'nullable|numeric|min:0|max:100',
            'month_end_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'peak_time_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'peak_time_start'              => 'nullable',
            'peak_time_end'                => 'nullable',
            'status'                       => 'required|boolean',
        ]);
        $category->update($data);
        return response()->json(['success' => true, 'message' => 'Category updated successfully.', 'data' => $category]);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}
