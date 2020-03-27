<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductsModel as Products;
use App\Models\TypesModel as Types;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
// use App\Http\Controllers\Auth;

class ProductsController extends Controller
{
    public function submit() {
        return view('products/submit');
    }



    public function submitconfirm(Request $request) {
        //bikin folder temp, kalo belum ada, bikin pake mkdir > cek dulu pake if file exists
        $path = public_path().'\upload\temp';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $input = $request->input();
        $filename = $request->product_name.'.'.$request->product_image->getClientOriginalExtension();
        $product_image = $request->file('product_image')->move($path, $filename);
        $laravelpath = 'upload/temp'.'/'.$filename;
        return view('products/submitconfirm', ['input'=>$input, 'product_image'=>$product_image, 'pathlaravel'=> $laravelpath, 'product_image_name'=>$filename]);
    }


    public function submitsuccess(Request $request){
            $products = new Products();
            $products->product_name = $request->product_name;
            $products->product_image = $request->product_image_name;
            $products->product_type = $request->product_type;
            $products->created_by_user_id = auth()->user()->user_id;
            $products->created_by_user_name = auth()->user()->user_name;
            $products->save();
            // ^ disave dulu baru dapet product id nya
            $product_id = $products->product_id;
            //tentuin path2nya
            $oldpath = public_path('upload\temp\\'.$request->product_image_name);
            $path =  public_path('upload\\'.$product_id.'\\'.$request->product_image_name);
            $folder =  public_path('upload\\'.$product_id);
            //bikin folder dlu
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            //move file dari old path ke path baru
            rename($oldpath, $path);
            return view('products/submitsuccess');
        // };
    }
    //product type nya kalopun ga ada ga masalah, ketampil smua
    public function allproducts(Request $request, $product_type=NULL) {
        $products = new Products();
        $data = $products->getProductlist();
        return view('products/all', ['productlist'=>$data]);
    }

    public function editproduct(Request $request, $product_id){
        if($request->isMethod('get')) {
            $product_data = Products::where('product_id', $product_id)->get()->toArray();
            return view('products/editproduct', ['product_id'=>$product_id, 'product_data'=>$product_data]);
        }
        if($request->isMethod('post')) {
            $path = public_path('upload/temp');
            $input = $request->input();
            if(request()->new_product_image == ''){
                $filename = request()->old_product_image;
                $new_product_image = $request->file('new_product_image');
                $product_data = Products::where('product_id', 
                $product_id)->get()->toArray();
                return view('products/editconfirm', ['product_id'=>$product_id, 'input'=>$input, 'new_product_image'=>$new_product_image, 'product_data'=>$product_data, 'old_product_image_name'=>$filename]);
            }else{
                // $filename = request()->new_product_image->getClientOriginalName();
                $filename = $request->product_name.'.'.$request->new_product_image->getClientOriginalExtension();
                $oldfilename = request()->old_product_image;
                $new_product_image = $request->file('new_product_image')->move($path, $filename);
                $laravelpath = 'upload/temp/'.$filename;
                $product_data = Products::where('product_id', $product_id)->get()->toArray();
                return view('products/editconfirm', ['product_id'=>$product_id, 'input'=>$input,  'new_product_image'=>$new_product_image, 'product_data'=>$product_data, 'pathlaravel'=> $laravelpath, 'product_image_name'=>$filename, 'old_product_image_name'=>$oldfilename]);
            }
        }
    }

    public function editsuccess(Request $request, $product_id) {
        $product = Products::find($product_id);
        $product->product_name=$request->product_name;
        $product->product_type=$request->product_type;
        $product->updated_by_user_id = auth()->user()->user_id;
        $product->updated_by_user_name = auth()->user()->user_name;
        if($request->new_product_image != ''){
            $product->product_image = $request->product_image_name;
            $oldpath = public_path('upload\temp\\'.$request->product_image_name);
            $path =  public_path('upload\\'.$product_id.'\\'.$request->product_image_name);
            $folder =  public_path('upload\\'.$product_id);
            //bikin folder dlu
            // if (!file_exists($folder)) {
            //     mkdir($folder, 0777, true);
            // }
            //move file dari old path ke path baru
            rename($oldpath, $path);
            //foto lama masuk delete folder
            $imageOld = $request->old_product_image_name;
            $oldpath2 = public_path('upload\\'.$product_id.'\\'. $imageOld); //image yang diganti
            $delete = public_path('upload\delete');
        
            if (!file_exists($delete)) {
                mkdir($delete, 0777, true);
            }
            rename($oldpath2 , $delete.'\\'.$imageOld);
        }else{
            $product->product_image = $request->old_product_image_name;
        }
        $product->save();
        return redirect()->route('allproducts')->with('alert', '編集完了')->with('type', '編集');
        // return redirect()->route('editsuccess');
    }
    

    public function confirmdeleteproduct(Request $request){
        $deleteproduct = Products::find($request->product_id);
        $deleteproduct->delete_flag=1;
        $deleteproduct->save();
        //with itu ngesave ke session (sessionflash) untuk sekali aja (direfresh ilang)
        return redirect()->route('allproducts')->with('alert', '削除完了')->with('type', '削除');
    }

    public function yourproducts(Request $user_id) {
        $products = Products::where('created_by_user_id', auth()->user()->user_id)->get()->toArray();
        $products = new Products();
        $data = $products->getProductlist();
        return view('products/your', ['productlist'=>$data]);
        // return view('products/your');
    }

    public function productsdisplay(Request $request, $product_type=NULL){
        $type = $request->type;
        $products = new Products();
        $data = $products->getProductlist();
        $products2 = Products::where('product_type',$type)->where('delete_flag',0)->get()->toArray(); 
        $types = new Types();
        $datatype =$types->getTypeslist();
        if($type == 0 ) {
            return view('home', ['productlist'=>$data, 'typelist'=>$datatype]);
        }else{
            return view('home',['productlist'=>$products2, 'typelist'=>$datatype]);
        }
    }
}