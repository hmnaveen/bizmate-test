<?php
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ReadableNumber implements CastsAttributes
{
  
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        return number_format(abs($value), 2); 
    }

    public function set($model, $key, $value, $attributes)
    {
        return str_replace(',', '', $value);
    }
}
