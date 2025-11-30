<?php
namespace App\Services;

use App\Exceptions\ValidationException;

class ProductValidator implements ValidatorInterface {
    public function validate(array $data): array {
        $errors = [];
        
        if (empty($data['sku'])) {
            $errors['sku'] = 'SKU wajib diisi';
        } elseif (strlen($data['sku']) > 50) {
            $errors['sku'] = 'SKU maksimal 50 karakter';
        }
        
        if (empty($data['name'])) {
            $errors['name'] = 'Nama barang wajib diisi';
        } elseif (strlen($data['name']) > 200) {
            $errors['name'] = 'Nama barang maksimal 200 karakter';
        }
        
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Kategori wajib dipilih';
        } elseif (!is_numeric($data['category_id'])) {
            $errors['category_id'] = 'Kategori tidak valid';
        }
        
        if (!isset($data['quantity']) || !is_numeric($data['quantity'])) {
            $errors['quantity'] = 'Jumlah harus berupa angka';
        } elseif ($data['quantity'] < 0) {
            $errors['quantity'] = 'Jumlah tidak boleh negatif';
        }
        
        if (!isset($data['price']) || !is_numeric($data['price'])) {
            $errors['price'] = 'Harga harus berupa angka';
        } elseif ($data['price'] < 0) {
            $errors['price'] = 'Harga tidak boleh negatif';
        }
        
        if (!isset($data['minimum_stock']) || !is_numeric($data['minimum_stock'])) {
            $errors['minimum_stock'] = 'Stok minimum harus berupa angka';
        } elseif ($data['minimum_stock'] < 0) {
            $errors['minimum_stock'] = 'Stok minimum tidak boleh negatif';
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return $data;
    }
}
