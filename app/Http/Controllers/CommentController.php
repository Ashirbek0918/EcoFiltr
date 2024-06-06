<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentAddRequest;
use App\Models\Comment;
use App\Models\Filter;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(CommentAddRequest $request){
        $data = $request->validated();
        $filter = Filter::findOrFail($data['filter_id']);
        $filter->comments()->create([
            'filter_id' => $filter->id,
            'comment' => $data['comment'],
        ]);
        return response()->json([
           'success' => true,
           'message' => 'Comment added successfully.'
        ],201);
    }
    
    public function update(Request $request,Comment $comment){
        $comment->update(['comment' => $request->input('comment')]);
        return response()->json([
           'success' => true,
           'message' => 'Comment updated successfully.'
        ],200);
    }

    public function delete($id){
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
           'success' => true,
           'message' => 'Comment deleted successfully.'
        ],200);
    }
}
