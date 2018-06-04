<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Transformers\ProductTransformer;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();  
    }
    /**
     * GET /products
     * 
     * @return array
     */
    public function index(){
        $paginator = Product::paginate();
        $products = $paginator->getCollection();
        $resource = new Collection($products, new ProductTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $this->fractal->createData($resource)->toArray();
    }

    public function show($id){
        $product = Product::find($id);
        $resource = new Item($product, new ProductTransformer);
        return $this->fractal->createData($resource)->toArray();
    }

    public function store(Request $request){

        //validate request parameters
        $this->validate($request, [
            'product_name' => 'bail|required|max:255',
            'product_description' => 'bail|required',
        ]);

        $product = Product::create($request->all());
        $resource = new Item($product, new ProductTransformer);
        return $this->fractal->createData($resource)->toArray();
    }

    public function update($id, Request $request){

        //validate request parameters
        $this->validate($request, [
            'product_name' => 'max:255',
        ]);

        //Return error 404 response if product was not found
        if(!Product::find($id)) return $this->errorResponse('product not found!', 404);

        $product = Product::find($id)->update($request->all());

        if($product){
            //return updated data
            $resource = new Item(Product::find($id), new ProductTransformer); 
            return $this->fractal->createData($resource)->toArray();
        }

        //Return error 400 response if updated was not successful        
        return $this->errorResponse('Failed to update product!', 400);
    }

    public function destroy($id){
        
        //Return error 404 response if product was not found
        if(!Product::find($id)) return $this->errorResponse('Product not found!', 404);

        //Return 410(done) success response if delete was successful
        if(Product::find($id)->delete()){
            return $this->customResponse('Product deleted successfully!', 410);
        }

        //Return error 400 response if delete was not successful
        return $this->errorResponse('Failed to delete product!', 400);
    }

    public function customResponse($message = 'success', $status = 200)
    {
        return response(['status' =>  $status, 'message' => $message], $status);
    }
}