<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Branch;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment; 
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExport implements WithMultipleSheets
{
    protected $products;
    protected $branchId;
    
    public function __construct($products = null, $branchId = null)
    {
        $this->products = $products;
        $this->branchId = $branchId;
    }
    
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        if ($this->branchId) {
            // Nếu có chọn chi nhánh cụ thể, chỉ tạo sheet cho chi nhánh đó
            $branch = Branch::find($this->branchId);
            if ($branch) {
                $sheets[] = new BranchProductSheet($branch, $this->products);
            }
        } else {
            // Lấy tất cả chi nhánh
            $branches = Branch::where('active', true)->get();
            
            foreach ($branches as $branch) {
                $sheets[] = new BranchProductSheet($branch, $this->products);
            }
            
            // Thêm sheet cho sản phẩm không có chi nhánh
            $sheets[] = new UnassignedProductSheet($this->products);
        }
        
        return $sheets;
    }
}

class BranchProductSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $branch;
    protected $products;
    
    public function __construct($branch, $products = null)
    {
        $this->branch = $branch;
        $this->products = $products;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $products = $this->products ?: Product::with(['category', 'branchStocks.branch'])->get();
        
        $exportData = collect();
        
        foreach ($products as $product) {
            // Lọc chỉ lấy stock của chi nhánh hiện tại
            $branchStock = $product->branchStocks->where('branch_id', $this->branch->id)->first();
            
            if ($branchStock) {
                $exportData->push([
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : 'N/A',
                    'base_price' => number_format($product->base_price, 0, ',', '.') . ' VNĐ',
                    'stock_quantity' => $branchStock->stock_quantity,
                    'status' => $branchStock->stock_quantity > 0 ? 'Còn hàng' : 'Hết hàng',
                    'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
                ]);
            }
        }
        
        return $exportData;
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Tên sản phẩm',
            'Danh mục',
            'Giá',
            'Số lượng tồn kho',
            'Trạng thái',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return $this->branch->name;
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Tự động điều chỉnh độ rộng cột
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Style cho header
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Style cho dữ liệu
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Tô màu xen kẽ cho các dòng
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F2F2F2']
                        ]
                    ]);
                }
            }
        }
        
        return [];
    }
}

class UnassignedProductSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        $products = $this->products ?: Product::with(['category', 'branchStocks.branch'])->get();
        
        $exportData = collect();
        
        foreach ($products as $product) {
            // Chỉ lấy sản phẩm không có branch stock
            if ($product->branchStocks->isEmpty()) {
                $exportData->push([
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : 'N/A',
                    'base_price' => number_format($product->base_price, 0, ',', '.') . ' VNĐ',
                    'stock_quantity' => 0,
                    'status' => 'Chưa phân bổ',
                    'created_at' => $product->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $product->updated_at->format('d/m/Y H:i:s'),
                ]);
            }
        }
        
        return $exportData;
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Tên sản phẩm',
            'Danh mục',
            'Giá',
            'Số lượng tồn kho',
            'Trạng thái',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Chưa phân bổ chi nhánh';
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Tự động điều chỉnh độ rộng cột
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Style cho header
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Style cho dữ liệu
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Tô màu xen kẽ cho các dòng
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA']
                        ]
                    ]);
                }
            }
        }
        
        return [];
    }
    

}
