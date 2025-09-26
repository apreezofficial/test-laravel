<?php
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithHeadingRow
{
    private $validCount = 0;
    private $errorRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Validation rules based on requirements
            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'price' => 'required|numeric|min:0.01',
                'quantity' => 'required|integer|min:0',
                'image_url' => 'nullable|url|max:255',
            ]);

            if ($validator->fails()) {
                // Reject rows with missing/invalid data
                $this->errorRows[] = ['row' => $index + 2, 'errors' => $validator->errors()->all()];
                continue;
            }

            //Handle Image Storage 
            $imagePath = null;
            if (!empty($row['image_url'])) {
                $contents = file_get_contents($row['image_url']);
                $filename = 'products/' . $row['sku'] . '-' . time() . '.jpg';
                // Store in a secure path 
                Storage::disk('public')->put($filename, $contents);
                $imagePath = $filename;
            }

            // Insert valid products into the database 
            Product::create([
                'name' => $row['name'],
                'sku' => $row['sku'],
                'price' => $row['price'],
                'quantity' => $row['quantity'],
                'image_path' => $imagePath,
            ]);

          
            $this->validCount++;
        }
    }

    public function getValidCount()
    {
        return $this->validCount;
    }

    public function getErrorRows()
    {
        return $this->errorRows;
    }
}