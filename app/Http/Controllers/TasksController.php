<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $data = [];
       if (\Auth::check()) { // 認証済みの場合
       $user = \Auth::user();
       $tasks = $user->tasks()->get();
       $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
       }
       // Welcomeビューでそれらを表示
        return view('welcome', $data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // バリデーション
        $this->validate($request, [
            'content' => 'required|max:191',
            'status' => 'required|max:10',   // 追加
        ]);
        // タスクを作成
        $task = new Task;
        $task->user_id = \Auth::id(); //追加2
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task ::findOrFail($id);

        //認証済みユーザ（閲覧者）がその投稿の所有者でない場合は、リダイレクトする
        if (\Auth::id() !== $task->user_id) {
            return redirect('/');
        }

        // タスク詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
        
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        //認証済みユーザ（閲覧者）がその投稿の所有者でない場合は、リダイレクトする
        if (\Auth::id() !== $task->user_id) {
             return redirect('/');
        }
        
        // タスク編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
        
         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         // バリデーション
        $this->validate($request, [
            'content' => 'required|max:191',
            'status' => 'required|max:10',   // 追加
        ]);
        
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスクを更新
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();
        
         //認証済みユーザ（閲覧者）がその投稿の所有者でない場合は、リダイレクトする
        if (\Auth::id() !== $task->user_id) {
            return redirect('/');
        }
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
       
      
        $task->delete();

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
