---
description: "Use when writing, editing or formatting Blade or HTML files. Defines the indentation and attribute formatting style for all HTML tags."
applyTo: "**/*.blade.php, **/*.html"
---

# Formato de atributos HTML: identation-custom

## Regla principal: un atributo por línea

Cuando un tag HTML tiene **más de un atributo**, cada atributo va en su propia línea y el `>` de cierre del tag va solo en la línea siguiente, alineado con el inicio del tag.

```html
<!-- ✅ Correcto -->
<a class="nav-link"
   href="{{ route('home') }}"
>Inicio</a>

<button class="btn btn-primary"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#loginModal"
>Iniciar sesión</button>

<input class="form-control"
       type="search"
       name="search"
       placeholder="Buscar..."
       autocomplete="off"
>

<!-- ❌ Incorrecto -->
<a class="nav-link" href="{{ route('home') }}">Inicio</a>
<button class="btn btn-primary" type="button" data-bs-toggle="modal">Texto</button>
```

## Regla de indentación de atributos

Los atributos se alinean con el primer carácter después del `<NombreTag ` del tag de apertura.

```html
<form method="GET"
      action="{{ route('products.index') }}"
      class="search-form"
      role="search"
>
```

## Regla: tags con un solo atributo

Tags con **un único atributo** pueden quedar en una sola línea.

```html
<!-- ✅ Correcto — un solo atributo -->
<a class="text-white-50">Política de privacidad</a>
<div class="container-fluid">
```

## Regla: texto y tag de cierre en la misma línea que el `>`

El texto contenido y el tag de cierre (`</...>`) deben ir en la **misma línea** que el `>` de apertura, nunca en la línea siguiente.

```html
<!-- ✅ Correcto -->
<a class="nav-link"
   href="{{ route('home') }}"
>Inicio</a>

<button class="btn btn-warning"
        type="button"
>Ver carrito</button>

<!-- ❌ Incorrecto -->
<a class="nav-link"
   href="{{ route('home') }}"
>Inicio
</a>
```

## Regla: tags con contenido multilinea (SVG, listas, formularios)

Cuando el contenido del tag es multilinea (tiene hijos), el tag de cierre va en su propia línea con la indentación del tag padre, como es habitual en HTML.

```html
<!-- ✅ Correcto — contenido multilinea -->
<button class="btn"
        type="button"
>
    <svg ...>
        <path d="..."/>
    </svg>
    Iniciar sesión
</button>
```

## Regla: tags vacíos (void elements)

Tags que no tienen cierre (`<input>`, `<img>`, `<meta>`, `<link>`, `<br>`, `<hr>`) terminan con `>` solo, sin `/`.

```html
<input class="form-control"
       type="text"
       name="email"
>
<img src="{{ asset('images/logo.jpg') }}"
     alt="Logo"
     width="60"
     height="40"
>
```

## Resumen visual

```
<tagname atributo1="valor"     ← primer atributo en la misma línea que el tag
         atributo2="valor"     ← atributos siguientes alineados
         atributo3             ← atributos booleanos sin valor también cuentan
>texto de contenido</tagname>  ← > + contenido + cierre en una línea
```
