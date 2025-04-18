<?php
namespace App\Repositories;
use App\Models\Product;
class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        return Product::all();
    }
    public function find($id)
    {
        return Product::findOrFail($id);
    }
    public function update($id, array $data)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }
    public function delete($id)
    {
        $product = $this->find($id);
        $product->delete();
        return $product;
    }
    public function create(array $data)
    {
        return Product::create($data);
    }
}
?>