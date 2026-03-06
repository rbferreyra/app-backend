<?php

namespace App\Shared\Repositories;

use App\Shared\Contracts\RepositoryInterface;
use App\Shared\Exceptions\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    /**
     * Return the fully qualified model class name.
     */
    abstract protected function model(): string;

    public function findById(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByField(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): Model
    {
        $record = $this->findById($id);

        if (! $record) {
            throw new ModelNotFoundException("Record [{$id}] not found in " . $this->model());
        }

        $record->update($data);

        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findById($id);

        if (! $record) {
            throw new ModelNotFoundException("Record [{$id}] not found in " . $this->model());
        }

        return $record->delete();
    }

    public function exists(string $field, mixed $value): bool
    {
        return $this->model->where($field, $value)->exists();
    }

    /**
     * Apply filters dynamically.
     * Usage: $this->applyFilters(['status' => 'active', 'role' => 'admin'])
     */
    protected function applyFilters(array $filters): static
    {
        foreach ($filters as $field => $value) {
            if (! is_null($value)) {
                $this->model = $this->model->where($field, $value);
            }
        }

        return $this;
    }
}
