<?php

namespace App\Helpers\Menu;

use JeroenNoten\LaravelAdminLte\Helpers\MenuItemHelper;

class Builder
{
    /**
     * The set of filters applied to menu items.
     *
     * @var array
     */
    private $filters;

    /**
     * Constructor.
     *
     * @param  array  $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Transform the items by applying the filters.
     *
     * @param  array  $items  An array with items to be transformed
     * @return array Array with the new transformed items
     */
    public function transformItems($items)
    {
        return array_values(array_filter(
            array_map([$this, 'applyFilters'], $items),
            [MenuItemHelper::class, 'isAllowed']
        ));
    }

    /**
     * Apply all the available filters to a menu item.
     *
     * @param  mixed  $item  A menu item
     * @return mixed A new item with all the filters applied
     */
    protected function applyFilters($item)
    {
        // Filters are only applied to array type menu items.

        if (! is_array($item)) {
            return $item;
        }
        
        // Now, apply all the filters on the item.

        foreach ($this->filters as $filter) {

            // If the item is not allowed to be shown, there is no sense to
            // continue applying the filters.

            if (! MenuItemHelper::isAllowed($item)) {
                return $item;
            }

            $item = $filter->transform($item);
        }
        // If the item is a submenu, transform all the submenu items first.
        // These items need to be transformed first because some of the submenu
        // filters (like the ActiveFilter) depends on these results.

        if (MenuItemHelper::isSubmenu($item)) {
            $item['submenu'] = $this->transformItems($item['submenu']);
        }


        return $item;
    }
}