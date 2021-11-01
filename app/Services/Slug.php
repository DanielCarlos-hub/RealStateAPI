<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{

    protected $string;
    protected $model;

    /**
     * @param String $string
     * @param Model $table
     */
    public function __construct(String $string, Model $model)
    {
        $this->string = $string;
        $this->model = $model;

    }

    /**
     * @param String $string
     * @param String $table
     *
     * @return [type]
     */

    public function createSlug()
    {
        // Normalize the title
        $slug = Str::slug($this->string);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($slug, $this->model);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Esse título já foi usado muitas vezes!');
    }

    /**
     * @param String $slug
     * @param Model $table
     *
     * @return [type]
     */
    protected function getRelatedSlugs(String $slug, Model $model)
    {

        return $model::select('slug')->where('slug', 'like', $slug.'%')
            ->get();
    }
}
