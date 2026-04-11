(function () {
    const form = document.querySelector('[data-voucher-form]');
    if (!form) {
        return;
    }

    const categorySelect = document.getElementById('voucher-category');
    const idGroups = form.querySelectorAll('[data-id-group]');

    const categorySearchInput = document.getElementById('category-search-input');
    const categoryIdHidden = document.getElementById('category-id-hidden');
    const collectionSearchInput = document.getElementById('collection-search-input');
    const collectionIdHidden = document.getElementById('collection-id-hidden');
    const productSearchInput = document.getElementById('product-search-input');
    const productIdHidden = document.getElementById('product-id-hidden');

    const codeInput = form.querySelector('input[name="code"]');
    const discountTypeInput = form.querySelector('select[name="discount_type"]');
    const discountValueInput = form.querySelector('input[name="discount_value"]');
    const minOrderInput = form.querySelector('input[name="min_order_value"]');
    const endDateInput = form.querySelector('input[name="end_date"]');

    const previewCode = document.getElementById('preview-code');
    const previewValue = document.getElementById('preview-value');
    const previewMinOrder = document.getElementById('preview-min-order');
    const previewEndDate = document.getElementById('preview-end-date');

    const numberVn = (value) => {
        const num = Number(value || 0);
        return Number.isNaN(num) ? '0' : num.toLocaleString('vi-VN');
    };

    const trimDecimal = (value) => {
        const num = Number(value || 0);
        if (Number.isNaN(num)) {
            return '0';
        }

        return `${num}`;
    };

    const dateVn = (value) => {
        if (!value) {
            return '--/--/----';
        }

        const dateOnlyMatch = String(value).match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dateOnlyMatch) {
            return `${dateOnlyMatch[3]}/${dateOnlyMatch[2]}/${dateOnlyMatch[1]}`;
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '--/--/----';
        }

        const day = `${date.getDate()}`.padStart(2, '0');
        const month = `${date.getMonth() + 1}`.padStart(2, '0');
        const year = date.getFullYear();

        return `${day}/${month}/${year}`;
    };

    const mapFromDatalist = (listId) => {
        const list = document.getElementById(listId);
        if (!list) {
            return {};
        }

        const mapping = {};
        list.querySelectorAll('option').forEach((option) => {
            const key = (option.value || '').trim();
            const value = (option.dataset.id || '').trim();
            if (key !== '') {
                mapping[key] = value;
            }
        });

        return mapping;
    };

    const categoryMap = mapFromDatalist('category-options-list');
    const collectionMap = mapFromDatalist('collection-options-list');
    const productMap = mapFromDatalist('product-options-list');

    const bindLookupField = (inputElement, hiddenElement, dataMap, listId) => {
        if (!inputElement || !hiddenElement) {
            return;
        }

        const listElement = document.getElementById(listId);
        const sourceOptionValues = listElement
            ? Array.from(listElement.querySelectorAll('option[data-id]')).map((option) => (option.value || '').trim())
            : [];

        const optionList = document.createElement('div');
        optionList.className =
            'hidden absolute z-30 mt-1 w-full max-h-44 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg';

        const fieldWrapper = inputElement.parentElement;
        if (fieldWrapper) {
            fieldWrapper.classList.add('relative');
            fieldWrapper.appendChild(optionList);
        }

        const renderOptionList = (keyword) => {
            if (!optionList) {
                return;
            }

            const keywordLower = (keyword || '').toLowerCase();
            const filteredValues = sourceOptionValues.filter((value) => value.toLowerCase().includes(keywordLower));

            if (filteredValues.length === 0 && keyword !== '') {
                optionList.innerHTML =
                    '<div class="px-3 py-2 text-xs font-medium text-gray-400">Không tìm thấy</div>';
                optionList.classList.remove('hidden');
                return;
            }

            if (filteredValues.length === 0) {
                optionList.classList.add('hidden');
                optionList.innerHTML = '';
                return;
            }

            optionList.innerHTML = filteredValues
                .slice(0, 30)
                .map(
                    (value) =>
                        `<button type="button" class="block w-full px-3 py-2 text-left text-xs text-gray-700 hover:bg-gray-50" data-option-value="${value.replace(/"/g, '&quot;')}">${value}</button>`
                )
                .join('');
            optionList.classList.remove('hidden');
        };

        const syncHiddenValue = () => {
            const keyword = (inputElement.value || '').trim();
            const hasExactMatch = Object.prototype.hasOwnProperty.call(dataMap, keyword);
            hiddenElement.value = hasExactMatch ? dataMap[keyword] : '';
            renderOptionList(keyword);
        };

        optionList.addEventListener('mousedown', (event) => {
            const target = event.target.closest('[data-option-value]');
            if (!target) {
                return;
            }

            event.preventDefault();
            const selectedValue = target.getAttribute('data-option-value') || '';
            inputElement.value = selectedValue;
            hiddenElement.value = dataMap[selectedValue] || '';
            optionList.classList.add('hidden');
        });

        inputElement.addEventListener('focus', () => {
            renderOptionList((inputElement.value || '').trim());
        });

        inputElement.addEventListener('blur', () => {
            setTimeout(() => optionList.classList.add('hidden'), 120);
        });

        inputElement.addEventListener('input', syncHiddenValue);
        inputElement.addEventListener('change', syncHiddenValue);
        syncHiddenValue();
    };

    const updateIdVisibility = () => {
        const currentCategory = categorySelect ? categorySelect.value : 'all';
        idGroups.forEach((group) => {
            const type = group.getAttribute('data-id-group');
            group.style.display = currentCategory === type ? '' : 'none';
        });
    };

    const updatePreview = () => {
        const code = (codeInput && codeInput.value ? codeInput.value : '').trim();
        const discountType = discountTypeInput && discountTypeInput.value ? discountTypeInput.value : 'percent';
        const discountValue = discountValueInput && discountValueInput.value ? discountValueInput.value : '0';
        const minOrder = minOrderInput && minOrderInput.value ? minOrderInput.value : '0';
        const endDate = endDateInput && endDateInput.value ? endDateInput.value : '';

        if (previewCode) {
            previewCode.textContent = code !== '' ? code.toUpperCase() : 'CHƯA NHẬP';
        }

        if (previewValue) {
            if (discountType === 'percent') {
                previewValue.textContent = `Giảm ${trimDecimal(discountValue)}%`;
            } else {
                previewValue.textContent = `Giảm ${numberVn(discountValue)}đ`;
            }
        }

        if (previewMinOrder) {
            previewMinOrder.textContent = `Cho đơn hàng từ ${numberVn(minOrder)}đ`;
        }

        if (previewEndDate) {
            previewEndDate.textContent = dateVn(endDate);
        }
    };

    if (categorySelect) {
        categorySelect.addEventListener('change', updateIdVisibility);
    }

    bindLookupField(categorySearchInput, categoryIdHidden, categoryMap, 'category-options-list');
    bindLookupField(collectionSearchInput, collectionIdHidden, collectionMap, 'collection-options-list');
    bindLookupField(productSearchInput, productIdHidden, productMap, 'product-options-list');

    [codeInput, discountTypeInput, discountValueInput, minOrderInput, endDateInput]
        .filter(Boolean)
        .forEach((element) => {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        });

    updateIdVisibility();
    updatePreview();
})();
