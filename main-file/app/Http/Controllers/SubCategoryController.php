<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SubCategoryModel;
use App\Models\CategoryModel;

class SubCategoryController extends Controller
{

public function sub_category_list(Request $request)
{
   $data['getrecord'] = SubCategoryModel::get_record($request);
   return view('admin.sub_category.list', $data);
}
public function sub_category_add(Request $request)
{
   $data['getCategory'] = CategoryModel::get_record($request);
  return view('admin.sub_category.add', $data);
}

public function sub_category_store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required',
            'name'        => 'required|string|max:255',
        ]);

        $store = new SubCategoryModel;
        $store->category_id = trim($request->category_id);
        $store->name = trim($request->name);
        $store->save();

        return redirect('admin/sub_category/list')->with('success', 'Sub Category successfully created.');
    }
    public function sub_category_edit($id, Request $request)
    {
        $data['getCategory'] = CategoryModel::get_record($request);
        $data['getSubCategory'] = SubCategoryModel::get_single($id);
        return view('admin.sub_category.edit',  $data);
    }

    public function sub_category_update($id, Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required',
            'name'        => 'required|string|max:255',
        ]);

        $store = SubCategoryModel::get_single($id);
        $store->category_id = trim($request->category_id);
        $store->name = trim($request->name);
        $store->save();

        return redirect('admin/sub_category/list')->with('success', 'Sub Category successfully update.');
     
    }

    public function sub_category_delete($id, Request $request)
    {
        $delete = SubCategoryModel::get_single($id);
        $delete->is_delete = 1;
        $delete->save();

        return redirect('admin/sub_category/list')->with('success', 'Sub Category successfully Delete.');
    }
}