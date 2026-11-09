<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = Menu::with(['category', 'activeRecipe.items.ingredient.unit', 'activeRecipe.items.unit']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();
        $categories = Category::forMenus()->orderBy('name')->get();
        $ingredients = Ingredient::with('unit')->active()->orderBy('name')->get();

        return view('menus.index', compact('menus', 'categories', 'ingredients', 'search', 'categoryId'));
    }

    public function store(Request $request)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:menus,code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'code.required' => 'Kode menu wajib diisi.',
            'code.unique' => 'Kode menu sudah digunakan.',
            'name.required' => 'Nama menu wajib diisi.',
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'photo.image' => 'File foto harus berupa format gambar (jpg, png, webp).',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $validated['price'] = $validated['price'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/menus');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $validated['photo'] = 'images/menus/' . $filename;
        }

        $menu = Menu::create($validated);

        // Buat resep awal v1
        Recipe::create([
            'menu_id' => $menu->id,
            'version' => 1,
            'is_active' => true,
            'notes' => 'Resep standar v1',
        ]);

        return redirect()->route('menus.recipe', $menu)->with('success', "Menu '{$menu->name}' berhasil dibuat. Silakan tambahkan komposisi bahan resep.");
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('menus', 'code')->ignore($menu->id)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'code.required' => 'Kode menu wajib diisi.',
            'code.unique' => 'Kode menu sudah digunakan.',
            'name.required' => 'Nama menu wajib diisi.',
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'photo.image' => 'File foto harus berupa format gambar (jpg, png, webp).',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $validated['price'] = $validated['price'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/menus');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);

            if ($menu->photo && file_exists(public_path($menu->photo)) && str_contains($menu->photo, 'images/menus/')) {
                @unlink(public_path($menu->photo));
            }

            $validated['photo'] = 'images/menus/' . $filename;
        }

        $menu->update($validated);

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    public function destroy(Menu $menu)
    {
        $this->authorizeOwner();

        if ($menu->orderRecapItems()->exists()) {
            return redirect()->back()->with('error', "Menu '{$menu->name}' tidak dapat dihapus karena sudah memiliki histori transaksi.");
        }

        $name = $menu->name;
        if ($menu->photo && file_exists(public_path($menu->photo)) && str_contains($menu->photo, 'images/menus/')) {
            @unlink(public_path($menu->photo));
        }
        $menu->delete();

        return redirect()->route('menus.index')->with('success', "Menu '{$name}' berhasil dihapus.");
    }

    public function recipeIndex()
    {
        $firstMenu = Menu::orderBy('name')->first();
        if ($firstMenu) {
            return redirect()->route('menus.recipe', $firstMenu);
        }

        return redirect()->route('menus.index')->with('info', 'Silakan tambahkan menu terlebih dahulu untuk mengelola komposisi resep.');
    }

    public function recipe(Menu $menu)
    {
        $menu->load(['category', 'activeRecipe.items.ingredient.unit', 'recipes.items.ingredient.unit']);
        $ingredients = Ingredient::with('unit')->active()->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $allMenus = Menu::with('category')->orderBy('name')->get();

        return view('menus.recipe', compact('menu', 'ingredients', 'units', 'allMenus'));
    }

    public function updateRecipe(Request $request, Menu $menu)
    {
        $this->authorizeOwner();

        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Komposisi resep minimal memiliki 1 bahan baku.',
            'items.*.quantity.gt' => 'Kuantitas per porsi harus lebih besar dari 0.',
        ]);

        DB::transaction(function () use ($request, $menu) {
            $latestVersion = (int) $menu->recipes()->max('version') ?: 0;
            $newVersion = $latestVersion + 1;

            // Nonaktifkan resep sebelumnya
            $menu->recipes()->update(['is_active' => false]);

            // Buat versi resep baru
            $recipe = Recipe::create([
                'menu_id' => $menu->id,
                'version' => $newVersion,
                'is_active' => true,
                'notes' => $request->notes ?: "Resep versi {$newVersion}",
            ]);

            // Ambil bahan baku
            $ingredientIds = array_column($request->items, 'ingredient_id');
            $ingredients = Ingredient::whereIn('id', $ingredientIds)->get()->keyBy('id');

            foreach ($request->items as $itemData) {
                $ing = $ingredients->get($itemData['ingredient_id']);
                if ($ing) {
                    RecipeItem::create([
                        'recipe_id' => $recipe->id,
                        'ingredient_id' => $ing->id,
                        'quantity' => (float) $itemData['quantity'],
                        'unit_id' => $ing->unit_id,
                    ]);
                }
            }
        });

        return redirect()->route('menus.recipe', $menu)->with('success', "Resep untuk '{$menu->name}' berhasil diperbarui ke versi baru.");
    }

    private function authorizeOwner(): void
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang mengelola menu dan resep.');
        }
    }
}
