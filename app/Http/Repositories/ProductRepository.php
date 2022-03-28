<?php
namespace App\Http\Repositories;


use App\Http\Interfaces\ProductInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\email;
use Illuminate\Http\Request;

class ProductRepository implements ProductInterface
{
use ApiResponseTrait;

public function products()
{
$arr=Product::get();
return $this->apiResponse(200,"products",null,$arr);
}

public function productsForUser()
{
$arr=Product::where( 'status' , true )->get();
return $this->apiResponse(200,"products",null,$arr);
}

public function addproduct($request)
{


if (auth()->user()->auth == 'admin')
{

// dd($request);
$validations = Validator::make($request->all(),[
'name' => 'required|min:3',
'img' => 'required',
'desc' => 'required',
'discount' => 'required',
'status' => 'required',
'price' => 'required',
'stock' => 'required'
]);

if($validations->fails())
{
return $this->apiResponse(400, 'validation error', $validations->errors());
}


if ($request->img->extension() == "png" || $request->img->extension() == "jpg" || $request->img->extension() == "jpeg")
{
$imagePath = time() . '_product.' . $request->img->extension();
$request->img->move(public_path('images/product'), $imagePath);
Product::create([
'name' => $request->name,
'desc' => $request->desc,
'discount' => $request->discount,
'status' => $request->status,
'img' => asset('images/product/'.$imagePath),
'price' => $request->price,
'stock' => $request->stock
]);
return $this->apiResponse(200, 'account was created');

}else {
return $this->apiResponse(400, 'validation error', "extension not supported");
}

}
return $this->apiResponse(400,"you not admin ");
}




public function deleteproduct($request)
{
if (auth()->user()->auth == 'admin' )
{
$validations = Validator::make($request->all(),[
'id' => 'required|exists:products,id'
]);
// dd($request->id);
if($validations->fails())
{
return $this->apiResponse(400, 'validation error', $validations->errors());
}

$Product= Product::where( 'id' , $request->id )->first();
unlink(public_path('images/product'.explode('/product',$Product->img)[1]));
$Product->delete();

return $this->apiResponse(200, 'delete Product is done');
}
return $this->apiResponse(400,"you not admin ");
}




public function updateproductByAdmin($request)
{
if (auth()->user()->auth == 'admin' )
{

$validations = Validator::make($request->all(),[
'id' => 'required|exists:products,id',
'name' => 'required|min:3',
'img' => 'required',
'desc' => 'required',
'discount' => 'required',
'status' => 'required',
'price' => 'required',
'stock' => 'required'
]);
if($validations->fails())
{
return $this->apiResponse(400, 'validation error', $validations->errors());
}
// dd($request);


if ($request->img->extension() == "png" || $request->img->extension() == "jpg" || $request->img->extension() == "jpeg")
{
$imagePath = time() . '_product.' . $request->img->extension();
$request->img->move(public_path('images/product'), $imagePath);

$Product= Product::where( 'id' , $request->id )->first();
$Product->update([
'id' => $request->id,
'name' => $request->name,
'desc' => $request->desc,
'discount' => $request->discount,
'status' => $request->status,
'img' => asset('images/product/'.$imagePath),
'price' => $request->price,
'stock' => $request->stock
]);

return $this->apiResponse(200, 'account was update');


}else
{
return $this->apiResponse(400, 'validation error', "extension not supported");
}
}
}


}



