<?php

namespace LTBackup\Extension\Facades;

use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Support\SettingSupport;

/**
 * Class SettingFacade
 * @method  static mixed get($name, $default = null);
 * @method static Model find($id)
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static Builder allWithBuilder()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate($perPage = 15)
 * @method static mixed create(array $data)
 * @method static Model update($model, array $data)
 * @method static bool  destroy($model)
 * @method static mixed findBySlug($slug)
 * @method static Builder|Model|object|null findByAttributes(array $attributes)
 * @method static Builder[]|\Illuminate\Database\Eloquent\Collection getByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
 * @method static Builder getByAttributesQuery(array $attributes, $orderBy = null, $sortOrder = 'asc');
 * @method static mixed getPluck(array $attributes, $column)
 * @method static mixed getPluckWithIds(array $ids, $column)
 * @method static Builder[]|\Illuminate\Database\Eloquent\Collection findByMany(array $ids)
 * @method static bool clearCache()
 * @method static mixed|void deleteOrFailed($id)
 * @method static set($key, $value)
 * @method static add($key, $value)
 * @package LTBackup\Extension\Facades
 */
class SettingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SettingSupport::class;
    }
}
