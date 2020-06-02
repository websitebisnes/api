<?php

namespace App\Observers;

use App\Category;

class CategoryObserver
{
    /**
     * Handle the category "created" event.
     *
     * @param  \App\Category  $category
     * @return void
     */
    public function saving(Category $category)
    {
        $category->user_id = request()->user()->id;
    }
}
