<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Get searchable fields array
     */
    abstract public function getFieldsSearchable(): array;

    /**
     * Configure the Model
     */
    abstract public function model(): string;

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Paginate records for scaffold.
     */
    public function paginate(int $perPage, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Build a query for retrieving all records.
     */
    public function allQuery(array $search = [], int $skip = null, int $limit = null): Builder
    {
        $query = $this->model->newQuery();
        $translatedAttributes = $this->model->translatedAttributes ?? [];

        if (count($search)) {

            foreach ($search as $key => $value) {
                if (in_array($key, $this->getFieldsSearchable()) && $value) {

                    if (in_array($key, $translatedAttributes)) {
                        $query->whereTranslationLike($key, '%' . $value . '%');
                    } else {

                        switch ($key) {
                            case 'name':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                            case 'first_name':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                            case 'last_name':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                                break;
                            case 'email':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                                break;
                            case 'created_at':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                                break;
                            case 'full_name':
                                $query->where($key, 'LIKE', $value . '%');
                                break;
                            case 'number':
                                $query->where($key, 'LIKE', '%' . $value . '%');
                                break;
                            case 'from_date':
                                $query->whereDate('created_at', '>=', $value);
                                break;
                            case 'to_date':
                                $query->whereDate('created_at', '<=', $value);
                                break;
                            case 'start_at_from':
                                $query->whereDate('start_at', '>=', $value);
                                break;
                            case 'start_at_to':
                                $query->whereDate('start_at', '<=', $value);
                                break;
                            case 'label':
                                $query->whereHas('labels', function (Builder $q) use ($value) {
                                    $q->whereIn('labels.id', $value);
                                });
                                break;

                            case 'tags':
                                $query->whereHas('tags', function (Builder $q) use ($value) {
                                    $q->whereIn('tags.id', $value);
                                });
                                break;
                            case 'group':
                                if (request('group') == 'group') {
                                    $query->whereNotNull('group_id');
                                } else {
                                    $query->whereNull('group_id');
                                }
                                break;
                            default:
                                $query->where($key, $value);
                        }
                    }
                }
            }
        }

        if (count($translatedAttributes)) {
            $query->with('translations');
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        // Universal Dynamic Sorting
        $sortBy = request('sort_by');
        $sortDir = request('sort_dir', 'desc');
        if ($sortBy) {
            $sortDir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';
            if (in_array($sortBy, $translatedAttributes)) {
                if (method_exists($query, 'orderByTranslation')) {
                    $query->orderByTranslation($sortBy, $sortDir);
                } else {
                    $query->orderBy($sortBy, $sortDir);
                }
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Get the repository model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Retrieve all records with given filter criteria
     */
    public function all(array $search = [], int $skip = null, int $limit = null, array $columns = ['*']): Collection
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Create model record
     */
    public function create(array $input, bool $withLog = true): Model
    {
        $model = $this->model->newInstance($input);
        $model->save();
        if ($withLog) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties($input)
                ->on($model)
                ->log('created');
        }

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find(int $id, array $columns = ['*'])
    {
        $query = $this->model->newQuery();
        $model = $query->find($id, $columns);
        // activity()
        //     ->causedBy(auth()->user())
        //     ->on($model)
        //     ->event('opened')
        //     ->log('opened');
        return $model;
    }

    /**
     * Update model record for given id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update(array $input, int $id, bool $withLog = true)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);
        $model->fill($input);
        $model->save();
        if ($withLog) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties($input)
                ->on($model)
                ->event('updated')
                ->log('updated');
        }
        return $model;
    }

    /**
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete(int $id, bool $withLog = true)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);
        if ($withLog) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties($model->getDirty())
                ->on($model)
                ->event('deleted')
                ->log('deleted');
        }

        return $model->delete();
    }
}
