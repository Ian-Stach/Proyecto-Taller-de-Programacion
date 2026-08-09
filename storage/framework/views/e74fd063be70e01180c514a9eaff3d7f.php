

<?php
    $selectedCategoryIds ??= [];
?>

<form method="POST" action="<?php echo e($formAction); ?>">
    <?php echo csrf_field(); ?>
    <?php if($formMethod === 'PATCH'): ?>
        <?php echo method_field('PATCH'); ?>
    <?php endif; ?>

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label fw-semibold" for="name">Nombre <span class="text-danger">*</span></label>
            <input class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" type="text" name="name" value="<?php echo e(old('name', $product->name ?? '')); ?>" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold"
                   for="price"
            >Precio <span class="text-danger">*</span></label>
            <input class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   id="price"
                   type="number"
                   name="price"
                   value="<?php echo e(old('price', $product->price ?? '')); ?>"
                   step="0.01"
                   min="0"
                   required
            >
            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="col-md-2">
            <label class="form-label fw-semibold"
                   for="stock"
            >Stock <span class="text-danger">*</span></label>
            <input class="form-control <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   id="stock"
                   type="number"
                   name="stock"
                   value="<?php echo e(old('stock', $product->stock ?? 0)); ?>"
                   min="0"
                   required
            >
            <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="col-12">
            <label class="form-label fw-semibold"
                   for="description"
            >Descripción <span class="text-danger">*</span></label>
            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      id="description"
                      name="description"
                      rows="4"
                      required
            ><?php echo e(old('description', $product->description ?? '')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="col-md-8">
            <label class="form-label fw-semibold" for="image" >URL de imagen</label>
            <input class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   id="image"
                   type="text"
                   name="image"
                   value="<?php echo e(old('image', $product->image ?? '')); ?>"
                   placeholder="https://..."
            >
            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input"
                       id="active"
                       type="checkbox"
                       name="active"
                       value="1"
                       <?php echo e(old('active', $product->active ?? true) ? 'checked' : ''); ?>

                >
                <label class="form-check-label fw-semibold"
                       for="active"
                >Visible en el catálogo</label>
            </div>
        </div>

        
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="category_id"
            >Categoría principal <span class="text-danger">*</span></label>
            <select class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    id="category_id"
                    name="category_id"
                    required
            >
                <option value="">-- Seleccionar --</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"
                            <?php echo e(old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : ''); ?>

                    ><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="habitat">Hábitat</label>
            <select class="form-select <?php $__errorArgs = ['habitat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="habitat" name="habitat">
                <option value="">-- Ninguno --</option>
                <?php $__currentLoopData = \App\Models\Product::HABITAT_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(old('habitat', $product->habitat ?? '') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="diet"
            >Dieta</label>
            <select class="form-select <?php $__errorArgs = ['diet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    id="diet"
                    name="diet"
            >
                <option value="">-- Ninguno --</option>
                <?php $__currentLoopData = \App\Models\Product::DIET_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"
                            <?php echo e(old('diet', $product->diet ?? '') === $key ? 'selected' : ''); ?>

                    ><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="era"
            >Era</label>
            <select class="form-select <?php $__errorArgs = ['era'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    id="era"
                    name="era"
            >
                <option value="">-- Ninguno --</option>
                <?php $__currentLoopData = \App\Models\Product::ERA_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"
                            <?php echo e(old('era', $product->era ?? '') === $key ? 'selected' : ''); ?>

                    ><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-md-4">
            <label class="form-label fw-semibold"
                   for="height_meters"
            >Altura (metros)</label>
            <input class="form-control <?php $__errorArgs = ['height_meters'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   id="height_meters"
                   type="number"
                   name="height_meters"
                   value="<?php echo e(old('height_meters', $product->height_meters ?? '')); ?>"
                   step="0.01"
                   min="0"
            >
        </div>

        
        <div class="col-12">
            <label class="form-label fw-semibold">Categorías adicionales (many-to-many)</label>
            <div class="row g-1">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   id="cat_<?php echo e($cat->id); ?>"
                                   type="checkbox"
                                   name="categories[]"
                                   value="<?php echo e($cat->id); ?>"
                                   <?php echo e(in_array($cat->id, old('categories', $selectedCategoryIds)) ? 'checked' : ''); ?>

                            >
                            <label class="form-check-label small"
                                   for="cat_<?php echo e($cat->id); ?>"
                            ><?php echo e($cat->name); ?></label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-warning fw-semibold"
                    type="submit"
            >Guardar</button>
            <a class="btn btn-outline-secondary"
               href="<?php echo e(route('admin.products')); ?>"
            >Cancelar</a>
        </div>

    </div>
</form>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\products\partials\form.blade.php ENDPATH**/ ?>