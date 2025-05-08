<?php

namespace App\Exports;

use App\Models\Admin\Product; // Sửa namespace cho đúng
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;
    
    public function __construct($products = null)
    {
        $this->products = $products;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->products ?: Product::with('category')->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tên sản phẩm',
            'Danh mục',
            'Giá',
            'Số lượng',
            'Trạng thái',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }
    
    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category ? $product->category->name : 'N/A',
            $product->base_price,
            $product->stock,
            $product->stock > 0 ? 'Còn hàng' : 'Hết hàng',
            $product->created_at->format('d/m/Y H:i:s'),
            $product->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
