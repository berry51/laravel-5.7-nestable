Laravel 5.7 Nestable
========

Laravel Nestable to work with recursive logic. Menu level there is no limit but
this may vary depending on your server performance.

[![Build Status](https://github.com/berry51/laravel-5.7-nestable?branch=master)](https://github.com/berry51/laravel-5.7-nestable)

Install
---

```ssh
composer require berry51/laravel-5.7-nestable
```

Then

Add to **app.php** the Service Provider file.
```ssh
Nestable\NestableServiceProvider::class
```

Then add **app.php** Facade file again.
```ssh
'Nestable' => Nestable\Facades\NestableService::class
```

Finally run the artisan command:
```ssh
php artisan vendor:publish --provider="Nestable\NestableServiceProvider"
```
That's it!

Basic Usage with Eloquent
---

Suppose that the data came from a database as follows.

Menu table:

id | pid | name           | slug
---| --------- | -------------- | -------
1  | 0         | T-shirts       | t-shirts
2  | 1         | Red T-shirts   | red-t-shirts
3  | 1         | Black T-shirts | black-t-shirts
4  | 0         | Sweaters       | sweaters
5  | 4         | Red Sweaters   | red-sweaters
6  | 4         | Blue Sweaters  | blue-sweaters

Example 1:

```php
<?php

use Nestable\NestableTrait;

class Menu extends \Eloquent {

    use NestableTrait;

    protected $parent = 'pid';

}

```

>**Note**: **$parent** variable refers to the parent  Menu (Default pid)


```php
<?php

$categories = Menu::nested()->get();
```
Query result:

```php
<?php

array:6 [
      0 => array:5 [
        "id" => 1
        "name" => "T-shirts"
        "slug" => "t-shirts"
        "child" => array:2 [
          0 => array:5 [
            "id" => 2
            "name" => "Red T-shirts"
            "slug" => "red-t-shirts"
            "child" => []
            "pid" => 1
          ]
          1 => array:5 [
            "id" => 3
            "name" => "Black T-shirts"
            "slug" => "black-t-shirts"
            "child" => []
            "pid" => 1
          ]
        ]
        "pid" => 0
      ]
      1 => array:5 [
        "id" => 4
        "name" => "Sweaters"
        "slug" => "sweaters"
        "child" => array:2 [
          0 => array:5 [
            "id" => 5
            "name" => "Red Sweaters"
            "slug" => "red-sweaters"
            "child" => []
            "pid" => 4
          ]
          1 => array:5 [
            "id" => 6
            "name" => "Blue Sweaters"
            "slug" => "blue-sweaters"
            "child" => []
            "pid" => 4
          ]
        ]
        "pid" => 0
    ]
]
```
For html tree output:

```php
<?php

Menu::renderAsHtml();
```

Output:

```html

<ul>
    <li><a href="">T-shirts
        <ul>
            <li><a href="red-t-shirt">Red T-shirt</a></li>
            <li><a href="black-t-shirts">Black T-shirts</a></li>
        </ul>
    </li>

    <li><a href="">Sweaters
        <ul>
            <li><a href="red-sweaters">Red Sweaters</a></li>
            <li><a href="blue-sweaters">Blue Sweaters</a></li>
        </ul>
    </li>
</ul>
```

For dropdown output:

```php
<?php

Menu::attr(['name' => 'categories'])
    ->selected(2)
    ->renderAsDropdown();
```

Output:

```html
<select name="categories">
    <option value="1">T-shirts</option>
    <option value="2" selected="selected">  Red T-shirts</option>
    <option value="3">  Black T-shirts</option>

    <option value="4">Sweaters</option>
    <option value="5">  Red Sweaters</option>
    <option value="6">  Blue Sweaters</option>
</select>
```

Selected for multiple list box:
```php
->selected([1,2,3])
```

Output methods
---

name                  | Parameter |output    
----------------------| --------- | -------
renderAsArray()       | none      | array   
renderAsJson()        | none      | json    
renderAsHtml()        | none      | html    
renderAsDropdown()    | none      | dropdown
renderAsMultiple()    | none      | Listbox

Usable methods with output methods
---

renderAsArray()
---
name                  | paremeter| description                      
----------------------| -------- | --------------------------------
[parent()](#parent)   | int      | Get childs of the defined parent

renderAsJson()
---
name                  | paremeter| description                      
----------------------| -------- | --------------------------------
[parent()](#parent)   | int      | Get childs of the defined parent

renderAsHtml()
---
name                  | paremeter         | description                      
----------------------| ----------------- | --------------------------------
[parent()](#parent)   | int               | Get childs of the defined parent
[active()](#active)   | callback/array/int| Selected item(s) for html output
[ulAttr()](#ulAttr)   | array/string      | Add attribute to parent ul element
[firstUlAttr()](#firstUlAttr)   | array/string      | Add attribute to parent ul element
[route()](#route)     | callback/array    | Generate url by route name
[customUrl()](#custom-url)     | string    | Generate custom url 

renderAsDropdown()/renderAsMultiple()
---
name                  | paremeter         | description                      
----------------------| ----------------- | --------------------------------
[parent()](#parent)   | int               | Get childs of the defined parent
[selected()](#selected)| callback/array/int| Selected item(s) for dropdown  
[attr()](#attr)        | array             | Dropdown/listbox attributes



parent()
---
Get childs of the defined parent.

```php
<?php

Menu::parent(2)->renderAsArray();
```
>**Note:** This methods usable all with output methods

active()
---
Selected item(s) for html output.

Example 1:
```php
<?php

Menu::active('t-shirts')->renderAsHtml();
```

Example 2:
```php
<?php

Menu::active('t-shirts', 'black-t-shirts')->renderAsHtml();
```

Example 3:
```php
<?php

Menu::active(['t-shirts', 'black-t-shirts'])->renderAsHtml();
```

Example 4:
```php
<?php

Menu::active(function($li, $href, $label) {

    $li->addAttr('class', 'active')->addAttr('data-label', $label);

})->renderAsHtml();
```

Example 5:
```php
<?php

Menu::active(function($li, $href, $label) {

    $li->addAttr(['class' => 'active', 'data-label' => $label]);

})->renderAsHtml();
```

firstUlAttr()
---
Add attribute to first ul element

Example 1:
```php
<?php

Menu::firstUlAttr('class', 'first-ul')->renderAsHtml();
```

Example 2:
```php
<?php

Menu::firstUlAttr(['class' => 'first-ul'])->renderAsHtml();
```

ulAttr()
---
Add attribute to parent ul element

Example 1:
```php
<?php

Menu::ulAttr('class', 'nav-bar')->renderAsHtml();
```

Example 2:
```php
<?php

Menu::ulAttr(['t-shirts' => 'black-t-shirts'])->renderAsHtml();
```

Example 3:
```php
<?php

Menu::ulAttr(function($ul, $pid) {

    if($pid == 10) {
        $ul->ulAttr('class', 'nav-bar');
    }

})->renderAsHtml();
```

route()
---

Generate url by route name

Example 1:
```php
<?php

Menu::route(['product' => 'slug'])->renderAsHtml();

```
>**Note:** **product** refer to route name and **slug** refer to paremeter name.

```php
<?php

Route::get('product/{slug}', 'ProductController@show');
```

Example 2:
```php
<?php

Menu::route(function($href, $label, $parent) {

    return \URL::to($href);

})->renderAsHtml();
```

customUrl()
---
Generate custom url with slug


Example 1:
```php
<?php

Menu::customUrl('product/detail/{slug}')->renderAsHtml();
```

Example 1:
```php
<?php

Menu::customUrl('product/{slug}/detail')->renderAsHtml();
```

>**Note:** **slug** keyword belongs to html > href in config file.

selected()
---
Selected item(s) for dropdown.

Example 1:
```php
<?php

Menu::selected(1)->renderAsDropdown();
```

Example 2:
```php
<?php

Menu::selected(1,5)->renderAsMultiple();
```

Example 3:
```php
<?php

Menu::selected([1,3])->renderAsMultiple();
```

Example 4:
```php
<?php

Menu::selected(function($option, $value, $label) {

    $option->addAttr('selected', 'true');
    $option->addAttr(['data-item' => $label]);

})->renderAsMultiple();
```

attr()
---
Dropdown/listbox attributes.

```php
<?php

Menu::attr(['name' => 'categories', 'class' => 'red'])->renderAsDropdown();
```

Configuration
---

The above examples were performed with default settings.
Config variables in **config/nestable.php** file.

name                  | type    | description                      
----------------------| ------- | --------------------------------
parent                | string  | Parent Menu column name      
primary_key           | string  | Table primary key                
generate_url          | boolean | Generate the url for html output
childNode             | string  | Child node name                  
[body](#body)         | array   | Array output (default)           
[html](#html)         | array   | Html output columns              
[dropdown](#dropdown) | array   | Dropdown/Listbox output          

body
---

The body variable should be an array and absolutely customizable.

Example:
```php
<?php

'body' => [
    'id',
    'Menu_name',
    'Menu_slug'
]
```

html
----

Configuration for html output.

name         | description       
-------------| -----------------
label        | Label column name
href         | Url column name   

Example:
```php
<?php

'html' => [
    'label' => 'name',
    'href'  => 'slug',
]
```

dropdown
----

Configuration for dropdown/listbox output.

name   | description         
------- | -------------------
prefix | Label prefix        
label  | Label column name   
value  | Value column name   

Example:

```php
<?php

'dropdown' => [
    'prefix' => '-',
    'label'  => 'name',
    'value'  => 'id'
]
```

Using Independent Models
---

Include the Nestable facade.

```php
<?php

use Nestable;

$result = Nestable::make([
    [
        'id' => 1,
        'pid' => 0,
        'name' => 'T-shirts',
        'slug' => 't-shirts'
    ],
    [
        'id' => 2,
        'pid' => 1,
        'name' => 'Red T-shirts',
        'slug' => 'red-t-shirts'
    ],
    [
        'id' => 3,
        'pid' => 1,
        'name' => 'Black T-shirts',
        'slug' => 'black-t-shirts'
    ]
    // and more...
]);
```

For array output:
```php
$result->renderAsArray();
```

Validators
---
It controls the structure of the data. They also made the rendering process with a second parameter control after they.

name    | Parameters
------- | -----------
isValidForArray | boolean
isValidForJson  | boolean
isValidForHtml  | boolean
isValidForDropdown| boolean
isValidForMultiple | boolean

**Example 1:**
```php
<?php

Menu::make($categories)->isValidForHtml();

// return true or false
```

**Example 2:**
```php
<?php

Menu::make($categories)->isValidForHtml(true);

// return html string if data valid
```

Macros
---

```php
<?php

Nestable::macro('helloWorld', function($nest, $categories) {

    return $nest->make($categories)->active('sweater')->route(['tests' => 'slug'])->renderAsHtml();

});
```

Call the above macro:

```php
<?php

$categories = [

    [
        'id'        => 1,
        'pid' => 0,
        'name'      => 'T-shirt',
        'slug'      => 'T-shirt'
    ],
    [
        'id'        => 2,
        'pid' => 0,
        'name'      => 'Sweater',
        'slug'      => 'sweater'
    ]

];

Nestable::helloWorld($categories);
```

Helper
---

```php
<?php

nestable($data)->renderAsHtml();
```

```php
<?php

nestable()->make($data)->renderAsHtml();
```

```php
<?php

nestable()->macro('helloWorld', function() {
    return 'Hello Laravel';
});

// run
nestable()->helloWorld();
```
