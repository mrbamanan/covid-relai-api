<?php


namespace App\Http\QueryFilters;


class Title
{
    public function handle($query, $next){
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

     //   $next($builder);
    }
}
