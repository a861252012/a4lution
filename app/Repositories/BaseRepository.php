<?php

namespace App\Repositories;

use App\Support\LaravelLoggerUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * @param
     * @return Model[]|Collection
     */
    public function all(): ?Collection
    {
        try {
            $models = $this->model->all();
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $models = Collection::make();
        }

        return $models;
    }

    /**
     * @param
     * @return LengthAwarePaginator|null
     */
    public function paginate(): ?LengthAwarePaginator
    {
        try {
            $paginator = $this->model->query()
                ->latest()
                ->paginate();

        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $paginator = null;
        }

        return $paginator;
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        try {
            $model = $this->model->find($id);
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $model = null;
        }

        return $model;
    }

    /**
     * @param array $attrs
     * @return Model|null
     */
    public function create(array $attrs): ?Model
    {
        try {
            $model = $this->model->create($attrs);
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $model = null;
        }

        return $model;
    }

    /**
     * @param integer $id
     * @param array $attrs
     * @return boolean
     */
    public function update(int $id, array $attrs): bool
    {
        try {
            $result = $this
                ->find($id)
                ->update($attrs);
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $result = false;
        }

        return $result;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $result = $this
                ->find($id)
                ->delete();
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $result = false;
        }

        return $result;
    }
}
