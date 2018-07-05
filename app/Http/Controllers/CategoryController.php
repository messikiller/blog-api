<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $parents = Category::with('children')->where('pid', '=', 0)->get();

        $data = [];
        foreach ($parents as $parent)
        {
            $children = $parent->children->toArray();

            $data[] = [
                'id'         => $parent->id,
                'title'      => $parent->title,
                'pid'        => $parent->pid,
                'sort'       => $parent->sort,
                'created_at' => $parent->created_at,
                'children'   => $children
            ];
        }

        return $this->success($data, 'ok');
    }
}
