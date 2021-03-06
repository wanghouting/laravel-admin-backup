<?php


namespace  LTBackup\Extension\Support;



use LTBackup\Extension\Entities\Setting;

/**
 * Class SettingSupport
 * @author wanghouting
 * @package LTBackup\Extension\Support;
 */
class SettingSupport
{
    protected $repository;
    public function __construct()
    {
        $this->repository = new Setting();
    }

    /**
     * Getting the setting
     * @param  string $name
     * @param  string   $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $setting = $this->repository->where('name',$name)->first();
        return $setting !== null ?  $setting->plainValue : $default;
    }
    /**
     * Getting the setting with type
     * @param  string $type
     * @return mixed
     */
    public function getWithType($type)
    {
        return $this->repository->where('type',$type)->get();
    }
    /**
     * Getting the setting
     * @param  string $name
     * @return mixed
     */
    public function getFull($name)
    {
        return $this->repository->where('name',$name)->first();
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        $default = microtime(true);

        return $this->get($name,  $default) !== $default;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return \Modules\Setting\Entities\Setting
     */
    public function set($key, $value)
    {
        return $this->repository->where('name' , $key)->update(['plainValue' => $value]);
    }
    

    /**
     * Set a given configuration value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return \Modules\Setting\Entities\Setting
     */
    public function add($key, $value)
    {
        return $this->repository->create([
            'name' => $key,
            'plainValue' => $value,
        ]);
    }




}
