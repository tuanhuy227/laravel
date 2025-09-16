<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name' =>$row['name'],
            'description' =>$row['description'],
            'price' =>$row['price'],
            'stock' =>$row['stock'],
        ]);
    }

     public function rules(): array
    {
        return [
            '*.name'        => ['required'],
            '*.description' => ['required'],
            '*.price'       => ['required', 'numeric'],
            '*.stock'       => ['required', 'integer'],
        ];
    }
}
