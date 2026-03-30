let variants = [];

function renderVariantTable() {
    const tbody = document.getElementById('variantList');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = variants.map((v) => v.isEditing ? `
        <tr class="border-b bg-blue-50" data-id="${v.id}">
            <td class="py-2 px-2"><input type="text" class="edit-size w-full rounded-xl p-1.5 border" value="${v.size}"></td>
            <td class="py-2 px-2"><input type="text" class="edit-color w-full rounded-xl p-1.5 border" value="${v.color}"></td>
            <td class="py-2 px-2"><input type="number" class="edit-qty w-full rounded-xl p-1.5 border" value="${v.qty}"></td>
            <td class="py-2 text-right px-2">
                <button onclick="saveEdit(${v.id})" class="text-green-600 font-bold">Lưu</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500">Xóa</button>
            </td>
        </tr>` : `
        <tr class="border-b hover:bg-gray-50" data-id="${v.id}">
            <td class="py-3">${v.size}</td>
            <td class="py-3">${v.color}</td>
            <td class="py-3">${v.qty}</td>
            <td class="py-3 text-right space-x-3 px-2">
                <button onclick="toggleEdit(${v.id})" class="text-blue-500 font-semibold">Sửa</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500 font-semibold">Xóa</button>
            </td>
        </tr>`).join('');
}

function addVariant() {
    const sizeInput = document.getElementById('size');
    const colorInput = document.getElementById('color');
    const qtyInput = document.getElementById('qty');

    if (!sizeInput || !colorInput || !qtyInput) {
        return;
    }

    const size = sizeInput.value.trim();
    const color = colorInput.value.trim();
    const qty = qtyInput.value;

    if (!size || !color || !qty) {
        alert('Vui lòng nhập đầy đủ thông tin!');
        return;
    }

    variants.push({ id: Date.now(), size, color, qty: Number(qty), isEditing: false });
    renderVariantTable();
    ['size', 'color', 'qty'].forEach((id) => {
        const input = document.getElementById(id);
        if (input) {
            input.value = '';
        }
    });
}

function toggleEdit(id) {
    const variant = variants.find((item) => item.id === id);
    if (variant) {
        variant.isEditing = true;
        renderVariantTable();
    }
}

function saveEdit(id) {
    const variant = variants.find((item) => item.id === id);
    if (!variant) {
        return;
    }

    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) {
        return;
    }

    const sizeValue = row.querySelector('.edit-size');
    const colorValue = row.querySelector('.edit-color');
    const qtyValue = row.querySelector('.edit-qty');

    variant.size = sizeValue ? sizeValue.value : variant.size;
    variant.color = colorValue ? colorValue.value : variant.color;
    variant.qty = qtyValue ? Number(qtyValue.value) : variant.qty;
    variant.isEditing = false;

    renderVariantTable();
}

function removeVariant(id) {
    if (confirm('Xóa biến thể này?')) {
        variants = variants.filter((item) => item.id !== id);
        renderVariantTable();
    }
}

function initProductImagePreview() {
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('main-image-preview');
    const mainImagePreviewWrapper = document.getElementById('main-image-preview-wrapper');
    const galleryInput = document.getElementById('gallery_images');
    const galleryPreview = document.getElementById('gallery-preview');
    const existingGalleryPreview = document.getElementById('gallery-existing-preview');
    const newGalleryPreview = document.getElementById('gallery-new-preview');
    const removedGalleryInputs = document.getElementById('removed-gallery-inputs');

    if (mainImageInput && mainImagePreview) {
        if (mainImagePreviewWrapper && mainImagePreviewWrapper.dataset.hasImage !== '1') {
            mainImagePreviewWrapper.classList.add('hidden');
        }

        mainImageInput.addEventListener('change', function (event) {
            const input = event.target;
            if (!input.files || !input.files[0]) {
                if (mainImagePreviewWrapper) {
                    mainImagePreviewWrapper.classList.add('hidden');
                }
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const result = e.target && e.target.result;
                if (typeof result === 'string') {
                    mainImagePreview.src = result;
                    if (mainImagePreviewWrapper) {
                        mainImagePreviewWrapper.classList.remove('hidden');
                    }
                }
            };
            reader.readAsDataURL(input.files[0]);
        });
    }

    if (galleryInput && galleryPreview) {
        const targetNewPreview = newGalleryPreview || galleryPreview;
        let selectedGalleryFiles = [];
        const removedExistingPaths = [];

        const syncSelectedFilesToInput = function () {
            const dataTransfer = new DataTransfer();
            selectedGalleryFiles.forEach(function (file) {
                dataTransfer.items.add(file);
            });
            galleryInput.files = dataTransfer.files;
        };

        const syncRemovedExistingInputs = function () {
            if (!removedGalleryInputs) {
                return;
            }

            removedGalleryInputs.innerHTML = '';

            removedExistingPaths.forEach(function (path, index) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `removed_gallery_images[${index}]`;
                input.value = path;
                removedGalleryInputs.appendChild(input);
            });
        };

        const renderGalleryPreview = function () {
            targetNewPreview.innerHTML = '';

            selectedGalleryFiles.forEach(function (file, index) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const result = e.target && e.target.result;
                    if (typeof result !== 'string') {
                        return;
                    }

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-100';

                    const image = document.createElement('img');
                    image.src = result;
                    image.alt = 'Gallery image preview';
                    image.className = 'w-full h-full object-cover';

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'absolute top-1 right-1 w-6 h-6 rounded-full bg-black/65 text-white text-[11px] leading-none hover:bg-red-500';
                    removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    removeButton.addEventListener('click', function () {
                        selectedGalleryFiles.splice(index, 1);
                        syncSelectedFilesToInput();
                        renderGalleryPreview();
                    });

                    wrapper.appendChild(image);
                    wrapper.appendChild(removeButton);
                    targetNewPreview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        };

        if (existingGalleryPreview) {
            existingGalleryPreview.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-remove-existing]');
                if (!removeButton) {
                    return;
                }

                const item = removeButton.closest('[data-existing-path]');
                if (!item) {
                    return;
                }

                const existingPath = item.dataset.existingPath;
                if (!existingPath) {
                    return;
                }

                if (!removedExistingPaths.includes(existingPath)) {
                    removedExistingPaths.push(existingPath);
                }

                item.remove();
                syncRemovedExistingInputs();
            });
        }

        galleryInput.addEventListener('change', function (event) {
            const input = event.target;
            if (!input.files || input.files.length === 0) {
                return;
            }

            selectedGalleryFiles = selectedGalleryFiles.concat(Array.from(input.files));
            syncSelectedFilesToInput();
            renderGalleryPreview();
        });

        syncRemovedExistingInputs();
    }
}

function syncVariantHiddenInputs() {
    const variantHiddenInputs = document.getElementById('variant-hidden-inputs');
    if (!variantHiddenInputs) {
        return;
    }

    variantHiddenInputs.innerHTML = '';

    variants.forEach((variant, index) => {
        const fields = {
            size: variant.size,
            color: variant.color,
            stock: variant.qty,
        };

        Object.entries(fields).forEach(([key, value]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `variants[${index}][${key}]`;
            input.value = value ?? '';
            variantHiddenInputs.appendChild(input);
        });
    });
}

function hydrateOldVariants(form) {
    if (!form) {
        return;
    }

    const rawOldVariants = form.dataset.oldVariants || '[]';
    let oldVariants = [];

    try {
        oldVariants = JSON.parse(rawOldVariants);
    } catch (error) {
        oldVariants = [];
    }

    if (!Array.isArray(oldVariants) || oldVariants.length === 0) {
        return;
    }

    variants = oldVariants.map((item, index) => ({
        id: Date.now() + index,
        size: item.size || '',
        color: item.color || '',
        qty: Number(item.stock || 0),
        isEditing: false,
    }));

    renderVariantTable();
}

function initAddProductForm() {
    const form = document.getElementById('add-product-form');
    if (!form) {
        return;
    }

    hydrateOldVariants(form);

    form.addEventListener('submit', function (event) {
        syncVariantHiddenInputs();

        if (variants.length === 0) {
            event.preventDefault();
            alert('Vui lòng thêm ít nhất 1 biến thể sản phẩm.');
        }
    });
}

function openCategorySetupModal() {
    const modal = document.getElementById('categorySetupModal');
    if (!modal) {
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeCategorySetupModal() {
    const modal = document.getElementById('categorySetupModal');
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function initCategorySetupModal() {
    const modal = document.getElementById('categorySetupModal');
    if (!modal) {
        return;
    }

    if (modal.dataset.autoOpen === '1' || modal.dataset.keepOpen === '1') {
        openCategorySetupModal();
    }

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeCategorySetupModal();
        }
    });

    window.openCategorySetupModal = openCategorySetupModal;
    window.closeCategorySetupModal = closeCategorySetupModal;
}

function initCategorySelectShortcut() {
    const categorySelect = document.getElementById('category_id');
    if (!categorySelect) {
        return;
    }

    let previousValue = categorySelect.value;

    categorySelect.addEventListener('focus', function () {
        previousValue = categorySelect.value;
    });

    categorySelect.addEventListener('change', function () {
        if (categorySelect.value === '__add_category__') {
            categorySelect.value = previousValue || '';
            openCategorySetupModal();
            return;
        }

        previousValue = categorySelect.value;
    });
}

function setFieldError(form, fieldName, message) {
    const field = form.querySelector(`[name="${fieldName}"]`);
    const error = form.querySelector(`[data-error-for="${fieldName}"]`);

    if (field) {
        field.classList.add('border-red-300');
        field.classList.remove('border-gray-100');
    }

    if (error) {
        error.textContent = message;
        error.classList.remove('hidden');
    }
}

function clearFieldError(form, fieldName) {
    const field = form.querySelector(`[name="${fieldName}"]`);
    const error = form.querySelector(`[data-error-for="${fieldName}"]`);

    if (field) {
        field.classList.remove('border-red-300');
        field.classList.add('border-gray-100');
    }

    if (error) {
        error.textContent = '';
        error.classList.add('hidden');
    }
}

function initCategoryFormValidation() {
    const forms = document.querySelectorAll('#categorySetupModal form[data-category-form]');
    if (!forms.length) {
        return;
    }

    forms.forEach(function (form) {
        const type = form.dataset.categoryForm;

        form.addEventListener('submit', function (event) {
            let hasError = false;

            clearFieldError(form, 'name');
            clearFieldError(form, 'parent_id');

            const nameInput = form.querySelector('[name="name"]');
            const nameValue = nameInput ? nameInput.value.trim() : '';

            if (!nameValue) {
                setFieldError(form, 'name', 'Vui lòng nhập tên danh mục.');
                hasError = true;
            }

            if (type === 'child') {
                const parentInput = form.querySelector('[name="parent_id"]');
                const parentValue = parentInput ? parentInput.value : '';

                if (!parentValue) {
                    setFieldError(form, 'parent_id', 'Vui lòng chọn parent_cate.');
                    hasError = true;
                }
            }

            if (hasError) {
                event.preventDefault();
            }
        });

        ['name', 'parent_id'].forEach(function (fieldName) {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field) {
                return;
            }

            const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventName, function () {
                clearFieldError(form, fieldName);
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initProductImagePreview();
    initAddProductForm();
    initCategorySetupModal();
    initCategorySelectShortcut();
    initCategoryFormValidation();
});
