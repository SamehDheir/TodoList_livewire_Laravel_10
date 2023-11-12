<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule("required|min:3|max:40")]
    public $name;
    public $search;
    public $editingTodoID;

    #[Rule("required|min:3|max:40")]
    public $editingTodoName;


    public function create()
    {
        $validate = $this->validateOnly('name');

        Todo::create($validate);

        $this->reset(["name"]);

        session()->flash("success", "Saved.");
    }

    public function delete($todoListId)
    {
        Todo::findOrfail($todoListId)->delete();
    }

    public function toggle($todoListId)
    {
        $todo = Todo::findOrfail($todoListId);
        $todo->complated = !$todo->complated;
        $todo->save();
    }

    public function edit($todoListId)
    {
        $this->editingTodoID = $todoListId;
        $this->editingTodoName = Todo::findOrfail($todoListId)->name;
    }

    public function cancleEdit()
    {
        $this->reset(["editingTodoID", "editingTodoName"]);
    }

    public function update()
    {
        $this->validateOnly('editingTodoName');
        Todo::findOrfail($this->editingTodoID)->update(
            [
                'name' => $this->editingTodoName,
            ]
        );

        $this->cancleEdit();
    }



    public function render()
    {
        // Live search to DB
        $todoList = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(2);
        $emptyMessage = '';
        if ($todoList->isEmpty()) {
            $emptyMessage = 'No Result';
        }
        // Live search to DB

        return view('livewire.todo-list', compact('todoList', 'emptyMessage'));
    }
}
