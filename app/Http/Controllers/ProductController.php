namespace App\Http\Controllers;

use App\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return $this->productService->listProduct($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->productService->createProduct($request->all());
    }
}

    public function update(Request $request, $id) {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->only(['name', 'price']));
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->productService->restoreProduct($uuid);
    }
}