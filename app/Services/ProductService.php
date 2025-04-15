<?php
    namespace App\Services;

    use App\Repositories\ProductRepositoryInterface;
    use App\Models\Product;
    use App\Notifications\PriceUpdatedNotification;
    use Illuminate\Support\Facades\Notification;
    use Illuminate\Support\Facades\Storage;
    use App\Jobs\SendPriceChangeNotification;
    use Exception;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    
    class ProductService
    {
        protected $productRepo;
    
        public function __construct(ProductRepositoryInterface $productRepo)
        {
            $this->productRepo = $productRepo;
        }
        public function all()
        {
            return $this->productRepo->all();
        }
        public function getProductById($id)
        {
            return $this->productRepo->find($id);
        }
        public function updateProduct($id, array $data)
        {
            $product = Product::find($id);
            DB::beginTransaction();
            try {
                $priceChanged = isset($data['price']) && $data['price'] !== $product->price;
                if (isset($data['image'])){
                    $filename=$data['image']->getClientOriginalExtension();
                    $data['image']->move(public_path('uploads'), $filename);
                    $data['image'] = 'uploads/' . $filename;
                }else{
                    $data['image'] ='product-placeholder.jpg';
                }
                // Update the product
                $updated = $this->productRepo->update($id, $data);
               

             // Send price update notification if the price was updated
             $notificationEmail = env('PRICE_NOTIFICATION_EMAIL', 'admin@example.com');

                     try {
                         SendPriceChangeNotification::dispatch(
                             $product,
                             $product->price,
                             $data['price'],
                             $notificationEmail
                         );
                     } catch (\Exception $e) {
                          Log::error('Failed to dispatch price change notification: ' . $e->getMessage());
                     }
                
                     DB::commit();
                     return $updated;
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception("Failed to update product: " . $e->getMessage());
            }
        }
        public function delete($id)
        {
            DB::beginTransaction();
            try{
                $this->productRepo->delete($id);
                DB::commit();
                return true;
            }catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        public function create(array $data)
        {
            DB::beginTransaction();
            try {
            if (isset($data['image'])){
                $filename=$data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('uploads'), $filename);
                $data['image'] = 'uploads/' . $filename;
            }else{
                $data['image'] ='product-placeholder.jpg';
            }
                $product = $this->productRepo->create($data);
                DB::commit();
                return $product;
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception("Failed to create product: " . $e->getMessage());
            }
        }
    }

?>