<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule("required|min:3|max:40")]
    public $name;
    public $search;

    public function create()
    {
        $validate = $this->validateOnly('name');

        Todo::create($validate);

        $this->reset(["name"]);

        session()->flash("success", "Saved.");
    }

    public function delete($todoListId)
    {
        Todo::find($todoListId)->delete();
    }

    public function render()
    {
        $todoList = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(2);
        return view('livewire.todo-list', compact('todoList'));
    }
}
