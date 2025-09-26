<?php
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Report 1: Low-Stock Products (Quantity < 5)
     */
    public function lowStock()
    {
        // Simple, efficient query
        $lowStockProducts = Product::where('quantity', '<', 5)
            ->orderBy('quantity', 'asc')
            ->get();

        return response()->json($lowStockProducts);
    }

    /**
     * Report 2: Sales Summary (Total Revenue, Total Orders, Top 3 Products)
     */
    public function salesSummary()
    {
        // We only care about completed sales for revenue
        $completedOrders = Order::where('status', 'completed');

        // Total Revenue and Order Count
        $totalRevenue = $completedOrders->sum('total_amount');
        $totalOrders = Order::count(); // Count all orders

        // Top 3 Selling Products (Uses the order_product pivot table)
        $topProducts = DB::table('order_product')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity_sold')
            ->take(3)
            ->pluck('product_id');

        // Fetch the actual product details for the top 3
        $topSellingProducts = Product::whereIn('id', $topProducts)->get();

        return response()->json([
            'total_revenue' => number_format($totalRevenue, 2),
            'total_orders' => $totalOrders,
            'top_3_selling_products' => $topSellingProducts,
        ]);
    }
}