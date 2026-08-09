{{--
    Partial: formulario de producto (compartido entre create y edit)
    Variables esperadas:
      $product    → instancia del modelo (nueva o existente, con old() para errores)
      $categories → Collection de todas las categorías
      $selectedCategoryIds → array de IDs seleccionados (vacío en create)
      $formAction → URL de destino del formulario
      $formMethod → método HTTP ('POST' o 'PATCH')
--}}

@php
    $selectedCategoryIds ??= [];
@endphp

<form method="POST" action="{{ $formAction }}">
    @csrf
    @if ($formMethod === 'PATCH')
        @method('PATCH')
    @endif

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label fw-semibold" for="name">Nombre <span class="text-danger">*</span></label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold"
                   for="price"
            >Precio <span class="text-danger">*</span></label>
            <input class="form-control @error('price') is-invalid @enderror"
                   id="price"
                   type="number"
                   name="price"
                   value="{{ old('price', $product->price ?? '') }}"
                   step="0.01"
                   min="0"
                   required
            >
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Stock --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold"
                   for="stock"
            >Stock <span class="text-danger">*</span></label>
            <input class="form-control @error('stock') is-invalid @enderror"
                   id="stock"
                   type="number"
                   name="stock"
                   value="{{ old('stock', $product->stock ?? 0) }}"
                   min="0"
                   required
            >
            @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Descripción --}}
        <div class="col-12">
            <label class="form-label fw-semibold"
                   for="description"
            >Descripción <span class="text-danger">*</span></label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description"
                      name="description"
                      rows="4"
                      required
            >{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Imagen --}}
        <div class="col-md-8">
            <label class="form-label fw-semibold" for="image" >URL de imagen</label>
            <input class="form-control @error('image') is-invalid @enderror"
                   id="image"
                   type="text"
                   name="image"
                   value="{{ old('image', $product->image ?? '') }}"
                   placeholder="https://..."
            >
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Activo --}}
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input"
                       id="active"
                       type="checkbox"
                       name="active"
                       value="1"
                       {{ old('active', $product->active ?? true) ? 'checked' : '' }}
                >
                <label class="form-check-label fw-semibold"
                       for="active"
                >Visible en el catálogo</label>
            </div>
        </div>

        {{-- Categoría principal (FK legacy) --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="category_id"
            >Categoría principal <span class="text-danger">*</span></label>
            <select class="form-select @error('category_id') is-invalid @enderror"
                    id="category_id"
                    name="category_id"
                    required
            >
                <option value="">-- Seleccionar --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}
                    >{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Hábitat --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="habitat">Hábitat</label>
            <select class="form-select @error('habitat') is-invalid @enderror" id="habitat" name="habitat">
                <option value="">-- Ninguno --</option>
                @foreach (\App\Models\Product::HABITAT_OPTIONS as $key => $label)
                    <option value="{{ $key }}" {{ old('habitat', $product->habitat ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Dieta --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="diet"
            >Dieta</label>
            <select class="form-select @error('diet') is-invalid @enderror"
                    id="diet"
                    name="diet"
            >
                <option value="">-- Ninguno --</option>
                @foreach (\App\Models\Product::DIET_OPTIONS as $key => $label)
                    <option value="{{ $key }}"
                            {{ old('diet', $product->diet ?? '') === $key ? 'selected' : '' }}
                    >{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Era --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="era"
            >Era</label>
            <select class="form-select @error('era') is-invalid @enderror"
                    id="era"
                    name="era"
            >
                <option value="">-- Ninguno --</option>
                @foreach (\App\Models\Product::ERA_OPTIONS as $key => $label)
                    <option value="{{ $key }}"
                            {{ old('era', $product->era ?? '') === $key ? 'selected' : '' }}
                    >{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Altura --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="height_meters"
            >Altura (metros)</label>
            <input class="form-control @error('height_meters') is-invalid @enderror"
                   id="height_meters"
                   type="number"
                   name="height_meters"
                   value="{{ old('height_meters', $product->height_meters ?? '') }}"
                   step="0.01"
                   min="0"
            >
        </div>

        {{-- Categorías many-to-many --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Categorías adicionales (many-to-many)</label>
            <div class="row g-1">
                @foreach ($categories as $cat)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   id="cat_{{ $cat->id }}"
                                   type="checkbox"
                                   name="categories[]"
                                   value="{{ $cat->id }}"
                                   {{ in_array($cat->id, old('categories', $selectedCategoryIds)) ? 'checked' : '' }}
                            >
                            <label class="form-check-label small"
                                   for="cat_{{ $cat->id }}"
                            >{{ $cat->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Botones --}}
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-warning fw-semibold"
                    type="submit"
            >Guardar</button>
            <a class="btn btn-outline-secondary"
               href="{{ route('admin.products') }}"
            >Cancelar</a>
        </div>

    </div>
</form>
