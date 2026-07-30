<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    protected $model;
    protected $rels = ['category', 'user'];
    protected $tag = 'tasks';
    protected $ttl = 3600;

    public function __construct() {  
        $this->model = Task::class; 
    }
    protected function user(){
        return auth()->id();
    }

    public function allByUser(): Collection
    {
        $user = $this->user();
        return Cache::tags([$this->tag, "user.{$user}"])->remember(
            "{$this->tag}:user:{$user}", $this->ttl,
            function () use ($user) {
                return $this->model::with($this->rels)->where('id_user', $user)->get();
            }
        );
    }

    public function findById(int $id): ?Task
    {
        return $this->model::with($this->rels)->find($id);
    }

    public function create(array $data): Task
    {
        $act = $this->model::create($data);
        Cache::tags([$this->tag, "user.{$this->user()}"])->forget(
            "{$this->tag}:user:{$this->user()}"
        );

        return $act;
    }

    public function update(int $id, array $data): bool
    {
        $act = $this->model::find($id);
        if (! $act) {
            return false;
        }

        Cache::tags([$this->tag, "user.{$this->user()}"])->forget(
            "{$this->tag}:user:{$this->user()}"
        );

        return $act->update($data);
    }

    public function delete(int $id): bool
    {
        $act = $this->model::find($id);
        if (! $act) {
            return false;
        }

        Cache::tags([$this->tag, "user.{$this->user()}"])->forget(
            "{$this->tag}:user:{$this->user()}"
        );

        return $act->delete();
    }

    public function checkOwner(array $data)
    {
        $act = $this->model::whereIn('id', $data)
                    ->where('id_user', $this->user())
                    ->pluck('id')
                    ->toArray();
        if (! $act) {
            return false;
        }

        return $act;
    }

    public function bulkDelete(array $data): bool
    {
        $act = $this->model::whereIn('id', $data)->delete();
        if (! $act) {
            return false;
        }

        Cache::tags([$this->tag, "user.{$this->user()}"])->forget(
            "{$this->tag}:user:{$this->user()}"
        );

        return true;
    }

    public function bulkStatus(array $tasks, string $status): bool
    {
        $act = $this->model::whereIn('id', $tasks)->update(['status' => $status]);
        if (! $act) {
            return false;
        }

        Cache::tags([$this->tag, "user.{$this->user()}"])->forget(
            "{$this->tag}:user:{$this->user()}"
        );

        return true;
    }
}
