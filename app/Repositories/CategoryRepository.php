<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;
    // protected $user;
    protected $rels = ['user'];
    protected $tag = 'categories';
    protected $ttl = 3600;

    public function __construct() {  
        $this->model = Category::class; 
        // $this->user = auth()->id(); 
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

    public function findById(int $id): ?Category
    {
        return $this->model::with($this->rels)->find($id);
    }

    public function create(array $data): Category
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
}
