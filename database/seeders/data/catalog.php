<?php

return [
    'categories' => [
        // Define la taxonomia con `children` para persistir la jerarquia real.
        ['name' => 'Saurischia',
            'children' => [
                ['name' => 'Herrerasauridae'],
                ['name' => 'Theropoda',
                    'children' => [
                        ['name' => 'Coelophysoidea'],
                        ['name' => 'Ceratosauria',
                            'children' => [
                                ['name' => 'Ceratosauridae'],
                                ['name' => 'Abelisauroidea',
                                    'children' => [
                                        ['name' => 'Noasauridae',
                                            'children' => [
                                                ['name' => 'Elaphrosaurinae'],
                                                ['name' => 'Noasaurinae'],
                                            ],
                                        ],
                                        ['name' => 'Abelisauridae',
                                            'children' => [
                                                ['name' => 'Majungasaurinae'],
                                                ['name' => 'Brachyrostra',
                                                    'children' => [
                                                        ['name' => 'Furileusauria',
                                                            'children' => [
                                                                ['name' => 'Carnotaurini'],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        ['name' => 'Tetanurae',
                            'children' => [
                                ['name' => 'Megalosauroidea'],
                                ['name' => 'Allosauroidea',
                                    'children' => [
                                        ['name' => 'Allosauridae'],
                                        ['name' => 'Carcharodontosauridae'],
                                    ]
                                ],
                                ['name' => 'Coelurosauria',
                                    'children' => [
                                        ['name' => 'Tyrannosauroidea'],
                                        ['name' => 'Ornithomimosauria'],
                                        ['name' => 'Compsognathidae'],
                                        ['name' => 'Maniraptora',
                                            'children' => [
                                                ['name' => 'Therizinosauridae'],
                                                ['name' => 'Oviraptorosauria'],
                                                ['name' => 'Alvarezsauridae'],
                                                ['name' => 'Pavares',
                                                    'children' => [
                                                        ['name' => 'Dromaeosauridae'],
                                                        ['name' => 'Troodontidae'],
                                                        ['name' => 'Avialae'],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                ['name' => 'Sauropodomorpha',
                    'children' => [
                        ['name' => 'Plateosauridae'],
                        ['name' => 'Massospondylidae'],
                        ['name' => 'Sauropoda',
                            'children' => [
                                ['name' => 'Diplodocoidea'],
                                ['name' => 'Macronaria',
                                    'children' => [
                                        ['name' => 'Brachiosauridae'],
                                        ['name' => 'Titanosauria'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        ['name' => 'Ornithischia',
            'children' => [
                ['name' => 'Heterodontosauridae'],
                ['name' => 'Genasauria',
                    'children' => [
                        ['name' => 'Thyreophora',
                            'children' => [
                                ['name' => 'Stegosauria'],
                                ['name' => 'Ankylosauria',
                                    'children' => [
                                        ['name' => 'Nodosauridae'],
                                        ['name' => 'Ankylosauridae'],
                                    ],
                                ],
                            ],
                        ],
                        ['name' => 'Neornithischia',
                            'children' => [
                                ['name' => 'Ornithopoda',
                                    'children' => [
                                        ['name' => 'Iguanodontia'],
                                        ['name' => 'Hadrosauridae'],
                                    ],
                                ],
                                ['name' => 'Marginocephalia',
                                    'children' => [
                                        ['name' => 'Pachycephalosauria'],
                                        ['name' => 'Ceratopsia'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'products' => [
        // Si existe database/seeders/data/products.json o products.csv,
        // ese archivo externo reemplaza por completo este bloque.
        // Usa este array solo si quieres seguir cargando productos desde PHP.
        //
        // Cuando quieras cargar productos reales, agrega entradas como esta.
        // Cada producto puede pertenecer a varias categorias.
        // Usa rutas completas para dejar explicita la rama del arbol; el seeder agregara ancestros automaticamente.
        // Cada referencia de `categories` debe coincidir exactamente con una ruta real de las categorias.
        // Ejemplo:
        // [
        //     'categories' => ['Saurischia > Sauropodomorpha > Massospondylidae'],
        //     'name' => 'Aardonyx',
        //     'description' => 'Descripcion completa del producto escrita manualmente.',
        //     'price' => 129.90,
        //     'stock' => 15,
        //     'image' => '/images/products/aardonyx.jpg',
        //     'active' => true,
        //     'height_meters' => 2.00,
        //     'habitat' => 'terrestre',
        //     'diet' => 'herbivoro',
        //     'era' => 'jurasico',
        // ],
    ],
];