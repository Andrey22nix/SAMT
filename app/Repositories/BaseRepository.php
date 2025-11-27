<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as PrettusBaseRepository;

abstract class BaseRepository extends PrettusBaseRepository
{
    protected bool $skipEvents = false;

    public function skipEvents(bool $status = true)
    {
        $this->skipEvents = $status;
        return $this;
    }

    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (! $this->skipEvents) {
            $this->fireEvents($model->events ?? null, 'created', $model);
        }

        return $model;
    }

    public function update(array $attributes, $id)
    {
        $model = parent::update($attributes, $id);

        if (! $this->skipEvents) {
            $this->fireEvents($model->events ?? null, 'updated', $model);
        }

        return $model;
    }

    public function delete($id)
    {
        $model = clone $this->find($id);

        $deleted = parent::delete($id);

        if (! $this->skipEvents) {
            $this->fireEvents($model->events ?? null, 'deleted', $model);
        }

        return $deleted;
    }

    public function fireEvents($events, string $method, $model): void
    {
        if (is_null($events)) {
            return;
        }

        if (! isset($events[$method])) {
            return;
        }

        foreach ($events[$method] as $event) {
            event(new $event($model));
        }
    }

    public function updateNotFillableFields($model, array $data)
    {
        foreach ($data as $field => $value) {
            $model->{$field} = $value;
        }

        $model->save();

        return $model;
    }

    public function getDataByField(string $field, $value, bool $withTrashed = false)
    {
        $model = $this->model;

        if ($withTrashed && method_exists($model, 'withTrashed')) {
            $model = $model->withTrashed();
        }

        return $model->where($field, $value)->first();
    }
}


