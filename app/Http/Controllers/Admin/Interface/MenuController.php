<?php

namespace App\Http\Controllers\Admin\Interface;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Interface\Menu\MenuCreateRequest;
use App\Http\Requests\Interface\Menu\ItemCreateRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\RoleHasPermissions;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $menus = Menu::get();

        return view('admin/interface/menu/index', compact('menus'));
    }

    public function create()
    {
        $menus = Menu::get();

        return view('admin/interface/menu/create', compact('menus'));
    }

    public function store(MenuCreateRequest $requestMenu, ItemCreateRequest $requestItem)
    {
        $validatedMenu = $requestMenu->validated();
        $validatedItem = $requestItem->validated();

        $menu = Menu::create([
            'name' => $validatedMenu['name'],
        ]);

        // Проверяем, что данные есть и они не пустые
        if (isset($validatedItem['items']) && is_array($validatedItem['items'])) 
        {
            foreach ($validatedItem['items'] as $item) 
            {
                if (!empty($item['title']) && !empty($item['url'])) 
                {
                    MenuItem::create([
                        'menu_id' => $menu->id,
                        'title' => $item['title'],
                        'url' => $item['url'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.interface.menu.index')->with('success', 'Меню создано');
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function delete()
    {
        //
    }
}
