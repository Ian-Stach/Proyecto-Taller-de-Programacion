# Catalogo Manual

Puedes cargar productos reales sin escribir arrays PHP dentro de `catalog.php`.

## Taxonomia de categorias

Las categorias del archivo `catalog.php` ahora se definen como un arbol real usando `children`.

Ejemplo:

```php
[
  [
    'name' => 'Saurischia',
    'children' => [
      [
        'name' => 'Sauropodomorpha',
        'children' => [
          ['name' => 'Massospondylidae'],
        ],
      ],
    ],
  ],
]
```

Cuando un producto referencia una categoria profunda, el seeder agrega tambien sus ancestros al pivote `category_product`. Eso permite filtrar por nodos amplios sin repetir toda la cadena manualmente en cada producto.

La recomendacion ahora es usar siempre rutas completas, incluso cuando el nombre sea unico, para dejar explicita la rama taxonomica elegida.

Si un mismo nombre aparece en mas de una rama del arbol, debes usar la ruta completa separada por ` > `.

Ejemplo:

```json
{
  "categories": ["Saurischia > Theropoda > Abelisauridae"]
}
```

## Prioridad de carga

`DatabaseSeeder` busca productos en este orden:

1. `database/seeders/data/products.json`
2. `database/seeders/data/products.csv`
3. `products` dentro de `database/seeders/data/catalog.php`

Si existe `products.json` o `products.csv`, ese archivo reemplaza por completo el bloque `products` de `catalog.php`.

## Formato JSON

Puedes usar un array directo de productos o un objeto con clave `products`.

Ejemplo:

```json
[
  {
    "categories": ["Saurischia > Sauropodomorpha > Massospondylidae"],
    "name": "Aardonyx",
    "description": "Descripcion manual completa del producto.",
    "price": 149.9,
    "stock": 12,
    "image": "/images/products/aardonyx.jpg",
    "active": true,
    "height_meters": 2.0,
    "habitat": "terrestre",
    "diet": "herbivoro",
    "era": "jurasico"
  }
]
```

Tambien puedes usar rutas completas cuando haya ambigüedad de nombres:

```json
[
  {
    "categories": ["Saurischia > Theropoda > Abelisauridae"],
    "name": "Majungasaurus"
  }
]
```

## Formato CSV

Usa estas columnas:

`name,description,price,stock,image,active,height_meters,habitat,diet,era,categories`

En `categories`, separa varias categorias con `|`.

Puedes indicar solo las categorias mas profundas. El seeder completa ancestros como `Sauropodomorpha` y `Saurischia` automaticamente.

Si hay nombres repetidos en ramas distintas, usa la ruta completa con ` > ` dentro de la columna `categories`.

Ejemplo:

```csv
name,description,price,stock,image,active,height_meters,habitat,diet,era,categories
Aardonyx,Descripcion manual completa del producto.,149.90,12,/images/products/aardonyx.jpg,true,2.00,terrestre,herbivoro,jurasico,Saurischia > Sauropodomorpha > Massospondylidae
```

Ejemplo con ruta completa:

```csv
name,description,price,stock,image,active,height_meters,habitat,diet,era,categories
Majungasaurus,Abelisaurido del Cretacico.,210.00,5,/images/products/majungasaurus.jpg,true,6.50,terrestre,carnivoro,cretacico,Saurischia > Theropoda > Abelisauridae
```

## Flujo recomendado

1. Mantener las categorias en `database/seeders/data/catalog.php`.
2. Crear `database/seeders/data/products.json` o `database/seeders/data/products.csv` a partir de los ejemplos.
3. Ejecutar `php artisan migrate:fresh --seed` si quieres reconstruir toda la base con el nuevo catalogo.
4. Ejecutar `php artisan db:seed --force` si solo quieres volver a sembrar sobre la base actual.