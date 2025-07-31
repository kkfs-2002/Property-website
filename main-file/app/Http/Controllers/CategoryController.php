<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CategoryModel;


class CategoryController extends Controller
{

public function Category_list(Request $request)
{
$data['getrecord'] = CategoryModel:: get_record($request);
return view('admin.category.list', $data);

}

public function Category_add(Request $request)
{
    return view('admin.category.add');
}
public function Category_store(Request $request)
{
    //dd($request->all());

    $validated = $request->validate([
        'name' => 'required|unique:category',
        
    ]);

    $save = new CategoryModel;
    $save->name = trim($request->name);
    $save->save();

    return redirect('admin/category/list')->with('success', 'Record successfully Create.');
}

public function Category_edit($id, Request $request )
{
    $data['getrecord'] = CategoryModel::get_single($id);
    return view('admin.category.edit',$data);
}

public function Category_update($id, Request $request )
{
    $validated = $request->validate([
        'name' => 'required|unique:category,name,'.$id,
    ]);

    $save = CategoryModel::get_single($id);
    $save->name = trim($request->name);
    $save->save();

    return redirect('admin/category/list')->with('success', 'Record successfully Update.');
}

public function Category_delete($id, Request $request )
{
    $save = CategoryModel::get_single($id);
    $save->is_delete = 1;
    $save->save();

    return redirect('admin/category/list')->with('success', 'Record successfully Delete.');
}
}