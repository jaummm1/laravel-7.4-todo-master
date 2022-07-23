<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $todos = Todo::where('user_id', $user->id)->get();
        $ativos = Todo::where('user_id', $user->id)->where('is_complete', '=', false)->get();


        return view('dashboard', compact('user', 'todos', 'ativos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $attributes = $request->only([
                'title',
                'color'
            ]);
            //dd($attributes);

            $attributes['user_id'] = $user->id;

            $todo = Todo::create($attributes);
        } catch (\Throwable $th) {
            logger()->error($th);
            return redirect('/todos/create')->with('error', 'Erro ao criar TODO');
        }

        return redirect('/dashboard')->with('success', 'TODO criado com sucesso');
    }
    /**
     * Complete the specified resource in storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function complete(Todo $todo)
    {
        try {
            $user = auth()->user();

            // Verificar se TODO é do usuário
            if ($todo->user_id !== $user->id) {
                return response('', 403);
            }

            $todo->update(['is_complete' => true]);
        } catch (\Throwable $th) {
            logger()->error($th);
            return redirect('/dashboard')->with('error', 'Erro ao completar TODO');
        }

        return redirect('/dashboard')->with('success', 'TODO completado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        try {
             $user = auth()->user();
            // Verificar se TODO é do usuário
            if ($todo->user_id !== $user->id) {
                return response('', 403);
            }

            $todo->delete();
        } catch (\Throwable $th) {
            logger()->error($th);
            return redirect('/dashboard')->with('error', 'Erro ao deletar TODO');
        }

        return redirect('/dashboard')->with('success', 'TODO deletado com sucesso');
    }


    public function edit($id,)
    {
        
        $todo = Todo::where('id', '=', $id)->first();
        //return $todo;
        $user = auth()->user();
        if($todo->user_id == $user->id){
            return view('edit', compact('todo'));
        }
        return response('', 404);
    }

    public function update($tod,  Request $request)
    {
        //dd($tod);
        //dd($request->id);
        $user = auth()->user();
        try {
            $user = auth()->user();

            $attributes = $request->only([
                'title',
                'color'
            ]);
            //dd($request->title);

            $attributes['user_id'] = $user->id;
            //Todo::UPDATED_AT()
            
            $to = Todo::find($tod);
            if($to->user_id == $user->id){
                $to->title = $request->title;
                $to->color = $request->color;
                $to->save();
            }else{
                return response('', 403);
            }
            

            //$todo = Todo::updated($attributes);
        } catch (\Throwable $th) {
            logger()->error($th);
            return redirect('/todos/create')->with('error', 'Erro ao criar TODO');
        }

        return redirect('/dashboard')->with('success', 'TODO criado com sucesso');

    }
}