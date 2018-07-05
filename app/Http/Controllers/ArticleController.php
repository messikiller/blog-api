<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends Controller
{
    public function list(Request $request)
    {
        $pagesize = $request->input('pagesize', 20);
        $pageno   = $request->input('pageno', 1);
        $offset   = ($pageno - 1) * $pagesize;

        $query = Article::query()->with('cate', 'tags');

        if (($cate_pid = $request->input('cate_pid', 0)) > 0) {
            $cate_ids = Category::where('pid', '=', $cate_pid)->get()->pluck('id')->unique()->toArray();
            count($cate_ids) > 0 && $query->whereIn('cate_id', $cate_ids);
        }

        $list = $query->published()
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($pagesize)
            ->get();

        $data = [];

        foreach ($list as $article)
        {
            $cate = optional($article->category);
            $tags = [];
            foreach ($article->tags as $tag)
            {
                $tags[] = [
                    'tag_id'    => $tag->id,
                    'tag_title' => $tag->title,
                    'color'     => $tag->color
                ];
            }
            $data[] = [
                'id'           => $article->id,
                'title'        => $article->title,
                'summary'      => $article->summary,
                'cate_id'      => $article->cate_id,
                'cate_title'   => strval(optional($article->cate)->title),
                'tags'         => $tags,
                'read_num'     => $article->read_num,
                'published_at' => $article->published_at
            ];
        }

        $ext = [
            'pagesize' => $pagesize,
            'pageno'   => $pageno
        ];

        return $this->success($data, $ext);
    }

    public function view(Request $request)
    {
        $id = intval($request->input('id', 0));
        if ($id <= 0) {
            return $this->failed();
        }

        $article = Article::find($id);

        $tags = [];
        foreach ($article->tags as $tag)
        {
            $tags[] = [
                'tag_id'    => $tag->id,
                'tag_title' => $tag->title,
                'color'     => $tag->color
            ];
        }

        $data = [
            'id'               => $article->id,
            'title'            => $article->title,
            'summary'          => $article->summary,
            'summary_original' => $article->summary_original,
            'cate_id'          => $article->cate_id,
            'cate_title'       => strval(optional($article->cate)->title),
            'tags'             => $tags,
            'read_num'         => $article->read_num,
            'published_at'     => $article->published_at,
            'content'          => $article->content,
            'content_original' => $article->content_original
        ];

        return $this->success($data);
    }
}
