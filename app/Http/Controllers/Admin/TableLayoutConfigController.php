<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableLayoutConfig;
use App\Models\Table;
use App\Models\TableLayoutSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TableLayoutConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request('cancel')) {
            return redirect()->route('admin.table-layout-config.index');
        }

        // Fetch all active tables with their layout positions (fresh query, no cache)
        $tables = Table::where('is_active', true)->orderBy('id')->get()->fresh();

        // Get saved layout dimensions
        $layoutSetting = TableLayoutSetting::getOrCreate('main_layout', 1200, 600);

        return view('admin.table-layout-config.index', compact('tables', 'layoutSetting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $layout = new TableLayoutConfig;
        
        return view('admin.table-layout-config.form', compact('layout'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'layout_name' => 'required|string|max:255|unique:table_layout_configs,layout_name',
            'canvas_width' => 'required|integer|min:400|max:2000',
            'canvas_height' => 'required|integer|min:300|max:1500',
            'is_active' => 'required|boolean',
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'layout_name.required' => 'Layout name is required.',
            'layout_name.unique' => 'A layout with this name already exists.',
            'canvas_width.required' => 'Canvas width is required.',
            'canvas_width.min' => 'Canvas width must be at least 400 pixels.',
            'canvas_width.max' => 'Canvas width cannot exceed 2000 pixels.',
            'canvas_height.required' => 'Canvas height is required.',
            'canvas_height.min' => 'Canvas height must be at least 300 pixels.',
            'canvas_height.max' => 'Canvas height cannot exceed 1500 pixels.',
            'floor_plan_image.image' => 'Floor plan must be an image file.',
            'floor_plan_image.mimes' => 'Floor plan must be a JPEG, PNG, JPG, or GIF file.',
            'floor_plan_image.max' => 'Floor plan image cannot exceed 2MB.',
        ]);

        $layout = new TableLayoutConfig;
        $layout->fill($request->except(['floor_plan_image']));

        // Handle floor plan image upload
        if ($request->hasFile('floor_plan_image')) {
            $layout->floor_plan_image = $this->handleImageUpload($request->file('floor_plan_image'));
        }

        $layout->save();

        return redirect()->route('admin.table-layout-config.index')
                        ->with('message', 'Layout configuration has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TableLayoutConfig $tableLayoutConfig)
    {
        $layout = $tableLayoutConfig;
        return view('admin.table-layout-config.show', compact('layout'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableLayoutConfig $tableLayoutConfig)
    {
        $layout = $tableLayoutConfig;
        
        return view('admin.table-layout-config.form', compact('layout'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableLayoutConfig $tableLayoutConfig)
    {
        $this->validate($request, [
            'layout_name' => 'required|string|max:255|unique:table_layout_configs,layout_name,' . $tableLayoutConfig->id,
            'canvas_width' => 'required|integer|min:400|max:2000',
            'canvas_height' => 'required|integer|min:300|max:1500',
            'is_active' => 'required|boolean',
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'layout_name.required' => 'Layout name is required.',
            'layout_name.unique' => 'A layout with this name already exists.',
            'canvas_width.required' => 'Canvas width is required.',
            'canvas_width.min' => 'Canvas width must be at least 400 pixels.',
            'canvas_width.max' => 'Canvas width cannot exceed 2000 pixels.',
            'canvas_height.required' => 'Canvas height is required.',
            'canvas_height.min' => 'Canvas height must be at least 300 pixels.',
            'canvas_height.max' => 'Canvas height cannot exceed 1500 pixels.',
            'floor_plan_image.image' => 'Floor plan must be an image file.',
            'floor_plan_image.mimes' => 'Floor plan must be a JPEG, PNG, JPG, or GIF file.',
            'floor_plan_image.max' => 'Floor plan image cannot exceed 2MB.',
        ]);

        $oldImage = $tableLayoutConfig->floor_plan_image;

        $tableLayoutConfig->fill($request->except(['floor_plan_image']));

        // Handle floor plan image upload
        if ($request->hasFile('floor_plan_image')) {
            // Delete old image if exists
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            
            $tableLayoutConfig->floor_plan_image = $this->handleImageUpload($request->file('floor_plan_image'));
        }

        $tableLayoutConfig->save();

        return redirect()->route('admin.table-layout-config.index')
                        ->with('message', 'Layout configuration has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableLayoutConfig $tableLayoutConfig)
    {
        // Delete associated image if exists
        if ($tableLayoutConfig->floor_plan_image && Storage::disk('public')->exists($tableLayoutConfig->floor_plan_image)) {
            Storage::disk('public')->delete($tableLayoutConfig->floor_plan_image);
        }

        $tableLayoutConfig->delete();
        
        return redirect()->route('admin.table-layout-config.index')
                        ->with('message', 'Layout configuration has been deleted successfully!');
    }

    /**
     * Toggle the active status of a layout configuration
     */
    public function toggleStatus(TableLayoutConfig $tableLayoutConfig)
    {
        $tableLayoutConfig->is_active = !$tableLayoutConfig->is_active;
        $tableLayoutConfig->save();

        $status = $tableLayoutConfig->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                        ->with('message', "Layout configuration has been {$status} successfully!");
    }

    /**
     * Duplicate a layout configuration
     */
    public function duplicate(TableLayoutConfig $tableLayoutConfig)
    {
        $newLayout = $tableLayoutConfig->replicate();
        
        // Generate a unique name for the duplicate
        $baseName = $tableLayoutConfig->layout_name;
        $counter = 1;
        
        do {
            $newName = $baseName . ' (Copy' . ($counter > 1 ? ' ' . $counter : '') . ')';
            $exists = TableLayoutConfig::where('layout_name', $newName)->exists();
            $counter++;
        } while ($exists);
        
        $newLayout->layout_name = $newName;
        $newLayout->is_active = false; // Duplicates start as inactive
        
        // Handle image duplication
        if ($tableLayoutConfig->floor_plan_image && Storage::disk('public')->exists($tableLayoutConfig->floor_plan_image)) {
            $originalPath = $tableLayoutConfig->floor_plan_image;
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newImagePath = 'floor-plans/' . Str::random(40) . '.' . $extension;
            
            if (Storage::disk('public')->copy($originalPath, $newImagePath)) {
                $newLayout->floor_plan_image = $newImagePath;
            }
        }
        
        $newLayout->save();

        return redirect()->route('admin.table-layout-config.edit', $newLayout->id)
                        ->with('message', 'Layout configuration has been duplicated successfully! You can now modify the copy.');
    }

    /**
     * Get active layouts for API or AJAX requests
     */
    public function getActiveLayouts()
    {
        $layouts = TableLayoutConfig::where('is_active', true)
                                   ->select('id', 'layout_name', 'canvas_width', 'canvas_height', 'floor_plan_image')
                                   ->orderBy('layout_name')
                                   ->get();

        return response()->json($layouts);
    }

    /**
     * Get layout details for canvas rendering
     */
    public function getLayoutDetails(TableLayoutConfig $tableLayoutConfig)
    {
        if (!$tableLayoutConfig->is_active) {
            return response()->json(['error' => 'Layout is not active'], 422);
        }

        $layoutData = [
            'id' => $tableLayoutConfig->id,
            'layout_name' => $tableLayoutConfig->layout_name,
            'canvas_width' => $tableLayoutConfig->canvas_width,
            'canvas_height' => $tableLayoutConfig->canvas_height,
            'floor_plan_image' => $tableLayoutConfig->floor_plan_image ? Storage::url($tableLayoutConfig->floor_plan_image) : null,
        ];

        return response()->json($layoutData);
    }

    /**
     * Bulk operations for multiple layouts
     */
    public function bulkAction(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|in:activate,deactivate,delete',
            'layout_ids' => 'required|array|min:1',
            'layout_ids.*' => 'exists:table_layout_configs,id',
        ]);

        $layouts = TableLayoutConfig::whereIn('id', $request->layout_ids);
        $count = $layouts->count();

        switch ($request->action) {
            case 'activate':
                $layouts->update(['is_active' => true]);
                $message = "{$count} layout(s) have been activated successfully!";
                break;
                
            case 'deactivate':
                $layouts->update(['is_active' => false]);
                $message = "{$count} layout(s) have been deactivated successfully!";
                break;
                
            case 'delete':
                // Delete associated images
                $layoutsToDelete = $layouts->get();
                foreach ($layoutsToDelete as $layout) {
                    if ($layout->floor_plan_image && Storage::disk('public')->exists($layout->floor_plan_image)) {
                        Storage::disk('public')->delete($layout->floor_plan_image);
                    }
                }
                $layouts->delete();
                $message = "{$count} layout(s) have been deleted successfully!";
                break;
        }

        return redirect()->route('admin.table-layout-config.index')->with('message', $message);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file)
    {
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('floor-plans', $fileName, 'public');
        
        return $path;
    }

    /**
     * Validate canvas dimensions
     */
    private function validateCanvasDimensions($width, $height)
    {
        $errors = [];

        // Check minimum dimensions
        if ($width < 400) {
            $errors[] = 'Canvas width must be at least 400 pixels.';
        }
        if ($height < 300) {
            $errors[] = 'Canvas height must be at least 300 pixels.';
        }

        // Check maximum dimensions
        if ($width > 2000) {
            $errors[] = 'Canvas width cannot exceed 2000 pixels.';
        }
        if ($height > 1500) {
            $errors[] = 'Canvas height cannot exceed 1500 pixels.';
        }

        // Check aspect ratio warnings (optional)
        $aspectRatio = $width / $height;
        if ($aspectRatio > 3 || $aspectRatio < 0.33) {
            $errors[] = 'Canvas dimensions have an unusual aspect ratio. Consider adjusting for better usability.';
        }

        return $errors;
    }

    /**
     * Get layout statistics for dashboard
     */
    public function getStatistics()
    {
        $stats = [
            'total_layouts' => TableLayoutConfig::count(),
            'active_layouts' => TableLayoutConfig::where('is_active', true)->count(),
            'inactive_layouts' => TableLayoutConfig::where('is_active', false)->count(),
            'layouts_with_images' => TableLayoutConfig::whereNotNull('floor_plan_image')->count(),
            'most_used_dimensions' => TableLayoutConfig::select('canvas_width', 'canvas_height')
                                                      ->selectRaw('COUNT(*) as count')
                                                      ->groupBy('canvas_width', 'canvas_height')
                                                      ->orderByDesc('count')
                                                      ->limit(5)
                                                      ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Save table layout positions
     */
    public function saveLayout(Request $request)
    {
        $request->validate([
            'tables' => 'required|array',
            'tables.*.id' => 'required|exists:tables,id',
            'tables.*.x' => 'required|numeric',
            'tables.*.y' => 'required|numeric',
            'container_width' => 'nullable|integer|min:400|max:3000',
            'container_height' => 'nullable|integer|min:300|max:2000',
        ]);

        try {
            foreach ($request->tables as $tableData) {
                $table = Table::find($tableData['id']);
                if ($table) {
                    // Update coordinates as JSON
                    $table->coordinates = [
                        'x' => (int) $tableData['x'],
                        'y' => (int) $tableData['y']
                    ];
                    $table->save();

                    // Force a fresh query to verify save
                    $table->refresh();
                }
            }

            // Save container dimensions if provided
            if ($request->has('container_width') && $request->has('container_height')) {
                $layoutSetting = TableLayoutSetting::getOrCreate('main_layout');
                $layoutSetting->container_width = (int) $request->container_width;
                $layoutSetting->container_height = (int) $request->container_height;
                $layoutSetting->save();
            }

            // Clear any caching
            \Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Layout saved successfully',
                'saved_count' => count($request->tables)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving layout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new table to layout
     */
    public function addTable(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'table_type' => 'required|in:indoor,outdoor,vip',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
        ]);

        $table = new Table();
        $table->table_number = $request->table_number;
        $table->capacity = $request->capacity;
        $table->table_type = $request->table_type;
        $table->status = 'available';
        $table->qr_code = null; // Will be generated later if needed
        $table->coordinates = [
            'x' => $request->x,
            'y' => $request->y
        ];
        $table->is_active = true;
        $table->save();

        return response()->json([
            'success' => true,
            'message' => 'Table added successfully',
            'table' => $table
        ]);
    }

    /**
     * Update table details
     */
    public function updateTable(Request $request, $tableId)
    {
        $table = Table::findOrFail($tableId);
        
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:50',
            'table_type' => 'required|in:indoor,outdoor,vip',
        ]);

        $table->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'table_type' => $request->table_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'table' => $table
        ]);
    }

    /**
     * Delete table from layout
     */
    public function deleteTable($tableId)
    {
        $table = Table::findOrFail($tableId);
        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully'
        ]);
    }
}