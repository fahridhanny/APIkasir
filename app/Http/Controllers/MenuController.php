<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;

class MenuController extends Controller
{
    public function category(){
        $category = Category::all();

        return response()->json([
            'category' => $category
        ], 200);
    }
    public function menu(){
        $menu = Menu::all();

        return response()->json([
            'menu' => $menu
        ], 200);
    }
    public function menuByCategory($id_category){
        $menuByCategory = Menu::where('id_category', $id_category)->get();
        $category = Category::where('id', $id_category)->first();
        if(empty($category)){
            return response()->json([
                'message' => 'Category dengan id '.$id_category.' tidak ada'
            ], 401);
        }else{
            return response()->json([
                'menuByCategory' => $menuByCategory
            ], 200);
        }
    }
}
