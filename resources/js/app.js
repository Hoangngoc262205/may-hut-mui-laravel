import './bootstrap';

function formatVnd(value) {
    return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + 'đ';
}

/*
|--------------------------------------------------------------------------
| PRODUCT DETAIL - GALLERY + VARIANTS
|--------------------------------------------------------------------------
*/

function initProductGalleryAndVariants() {
    const variants = Array.isArray(window.productVariants)
        ? window.productVariants
        : [];

    const mainImage = document.getElementById('mainProductImage');
    const priceEl = document.getElementById('variantPrice');
    const skuEl = document.getElementById('variantSku');
    const stockEl = document.getElementById('variantStock');
    const selectedVariant = document.getElementById('selectedVariantId');
    const variantMessage = document.getElementById('variantMessage');
    const addButton = document.getElementById('addToCartButton');
    const buyNowButton = document.getElementById('buyNowButton');
    const quantity = document.getElementById('quantity');

    const sizeButtons = [
        ...document.querySelectorAll('[data-size-id]')
    ];

    const colorButtons = [
        ...document.querySelectorAll('[data-color-id]')
    ];

    const galleryButtons = [
        ...document.querySelectorAll('[data-gallery-image]')
    ];

    /*
    |--------------------------------------------------------------------------
    | Nếu không phải trang product detail
    |--------------------------------------------------------------------------
    */

    if (!selectedVariant) {
        initGalleryOnly(mainImage, galleryButtons);
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT VARIANT
    |--------------------------------------------------------------------------
    */

    const previewVariant =
        variants.find(
            (variant) =>
                String(variant.id) === String(window.defaultVariantId)
        ) ||
        variants.find((variant) => Number(variant.stock || 0) > 0) ||
        variants[0] ||
        null;

    /*
    |--------------------------------------------------------------------------
    | Tự chọn size/color của default variant
    |--------------------------------------------------------------------------
    |
    | Đây là phần quan trọng chị sửa.
    | Trước đây previewVariant chỉ dùng để hiển thị,
    | nhưng selectedSize / selectedColor vẫn null.
    |
    */

    let selectedSize = previewVariant?.size_id
        ? String(previewVariant.size_id)
        : null;

    let selectedColor = previewVariant?.color_id
        ? String(previewVariant.color_id)
        : null;

    /*
    |--------------------------------------------------------------------------
    | Đổi ảnh chính
    |--------------------------------------------------------------------------
    */

    function changeMainImage(src) {
        if (!mainImage || !src) {
            return;
        }

        mainImage.src = src;

        galleryButtons.forEach((button) => {
            button.classList.toggle(
                'active',
                button.dataset.galleryImage === src
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Tìm đúng variant
    |--------------------------------------------------------------------------
    */

    function exactVariant() {
        if (sizeButtons.length && !selectedSize) {
            return null;
        }

        if (colorButtons.length && !selectedColor) {
            return null;
        }

        return (
            variants.find((variant) => {
                const sizeMatched =
                    !sizeButtons.length ||
                    String(variant.size_id) === String(selectedSize);

                const colorMatched =
                    !colorButtons.length ||
                    String(variant.color_id) === String(selectedColor);

                return sizeMatched && colorMatched;
            }) || null
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable nút mua hàng
    |--------------------------------------------------------------------------
    */

    function setButtonsEnabled(enabled) {
        [addButton, buyNowButton].forEach((button) => {
            if (!button) {
                return;
            }

            button.disabled = !enabled;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Render variant
    |--------------------------------------------------------------------------
    */

    function renderVariant() {
        const current = exactVariant();

        const displayVariant = current || previewVariant;

        const hasRequiredOptions =
            (!sizeButtons.length || Boolean(selectedSize)) &&
            (!colorButtons.length || Boolean(selectedColor));

        const inStock =
            current &&
            Number(current.stock || 0) > 0;

        /*
        |--------------------------------------------------------------------------
        | Hidden input gửi product_variant_id
        |--------------------------------------------------------------------------
        */

        selectedVariant.value =
            current && inStock
                ? current.id
                : '';

        /*
        |--------------------------------------------------------------------------
        | Nút thêm giỏ / mua ngay
        |--------------------------------------------------------------------------
        */

        setButtonsEnabled(Boolean(current && inStock));

        /*
        |--------------------------------------------------------------------------
        | Thông tin variant
        |--------------------------------------------------------------------------
        */

        if (displayVariant) {
            if (priceEl) {
                priceEl.textContent =
                    formatVnd(displayVariant.price);
            }

            if (skuEl) {
                skuEl.textContent =
                    displayVariant.sku || 'Đang cập nhật';
            }

            if (stockEl) {
                const stock =
                    Number(displayVariant.stock || 0);

                stockEl.textContent =
                    stock > 0
                        ? `✓ Còn ${stock} sản phẩm`
                        : 'Hết hàng';

                stockEl.classList.toggle(
                    'out',
                    stock <= 0
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Đổi ảnh theo variant
            |--------------------------------------------------------------------------
            */

            if (displayVariant.image_url) {
                changeMainImage(
                    displayVariant.image_url
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Giới hạn quantity theo stock
            |--------------------------------------------------------------------------
            */

            if (quantity) {
                const stock =
                    Number(displayVariant.stock || 0);

                if (stock > 0) {
                    quantity.max = stock;

                    const currentQuantity =
                        parseInt(quantity.value, 10) || 1;

                    quantity.value =
                        Math.min(
                            Math.max(currentQuantity, 1),
                            stock
                        );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Message
        |--------------------------------------------------------------------------
        */

        if (variantMessage) {
            if (!hasRequiredOptions) {
                variantMessage.textContent =
                    'Vui lòng chọn đủ kích thước và màu sắc.';

                variantMessage.classList.remove('ok');
            } else if (!current) {
                variantMessage.textContent =
                    'Phân loại này không tồn tại. Vui lòng chọn lại.';

                variantMessage.classList.remove('ok');
            } else if (!inStock) {
                variantMessage.textContent =
                    'Phân loại này đã hết hàng.';

                variantMessage.classList.remove('ok');
            } else {
                const selectedNames = [
                    current.color_name,
                    current.size_name,
                ].filter(Boolean);

                variantMessage.textContent =
                    selectedNames.length
                        ? 'Đã chọn: ' + selectedNames.join(' - ')
                        : 'Đã chọn phân loại sản phẩm.';

                variantMessage.classList.add('ok');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Active button
        |--------------------------------------------------------------------------
        */

        sizeButtons.forEach((button) => {
            button.classList.toggle(
                'active',
                String(button.dataset.sizeId) ===
                    String(selectedSize)
            );
        });

        colorButtons.forEach((button) => {
            button.classList.toggle(
                'active',
                String(button.dataset.colorId) ===
                    String(selectedColor)
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Kiểm tra option nào còn khả dụng
    |--------------------------------------------------------------------------
    */

    function refreshOptionAvailability() {
        sizeButtons.forEach((button) => {
            const sizeId =
                String(button.dataset.sizeId);

            const exists = variants.some((variant) => {
                const sameSize =
                    String(variant.size_id) === sizeId;

                const sameColor =
                    !selectedColor ||
                    String(variant.color_id) ===
                        String(selectedColor);

                const inStock =
                    Number(variant.stock || 0) > 0;

                return (
                    sameSize &&
                    sameColor &&
                    inStock
                );
            });

            button.disabled = !exists;
        });

        colorButtons.forEach((button) => {
            const colorId =
                String(button.dataset.colorId);

            const exists = variants.some((variant) => {
                const sameColor =
                    String(variant.color_id) === colorId;

                const sameSize =
                    !selectedSize ||
                    String(variant.size_id) ===
                        String(selectedSize);

                const inStock =
                    Number(variant.stock || 0) > 0;

                return (
                    sameColor &&
                    sameSize &&
                    inStock
                );
            });

            button.disabled = !exists;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Chọn SIZE
    |--------------------------------------------------------------------------
    */

    sizeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (button.disabled) {
                return;
            }

            selectedSize =
                String(button.dataset.sizeId);

            /*
            | Nếu màu hiện tại không còn phù hợp với size mới,
            | bỏ màu để người dùng chọn lại.
            */

            const colorStillValid =
                !selectedColor ||
                variants.some((variant) => {
                    return (
                        String(variant.size_id) ===
                            String(selectedSize) &&
                        String(variant.color_id) ===
                            String(selectedColor) &&
                        Number(variant.stock || 0) > 0
                    );
                });

            if (!colorStillValid) {
                selectedColor = null;
            }

            refreshOptionAvailability();
            renderVariant();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Chọn COLOR
    |--------------------------------------------------------------------------
    */

    colorButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (button.disabled) {
                return;
            }

            selectedColor =
                String(button.dataset.colorId);

            /*
            | Nếu size hiện tại không còn phù hợp với màu mới,
            | bỏ size để người dùng chọn lại.
            */

            const sizeStillValid =
                !selectedSize ||
                variants.some((variant) => {
                    return (
                        String(variant.color_id) ===
                            String(selectedColor) &&
                        String(variant.size_id) ===
                            String(selectedSize) &&
                        Number(variant.stock || 0) > 0
                    );
                });

            if (!sizeStillValid) {
                selectedSize = null;
            }

            refreshOptionAvailability();
            renderVariant();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Gallery
    |--------------------------------------------------------------------------
    */

    galleryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            changeMainImage(
                button.dataset.galleryImage
            );
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Chặn submit nếu chưa có variant hợp lệ
    |--------------------------------------------------------------------------
    */

    selectedVariant.form?.addEventListener(
        'submit',
        (event) => {
            if (!selectedVariant.value) {
                event.preventDefault();

                if (variantMessage) {
                    variantMessage.textContent =
                        'Vui lòng chọn phân loại còn hàng trước khi mua.';

                    variantMessage.classList.remove('ok');
                }
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Khởi tạo
    |--------------------------------------------------------------------------
    */

    refreshOptionAvailability();
    renderVariant();
    initQuantity();
}

/*
|--------------------------------------------------------------------------
| GALLERY ONLY
|--------------------------------------------------------------------------
*/

function initGalleryOnly(mainImage, galleryButtons) {
    if (!mainImage) {
        return;
    }

    galleryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const src =
                button.dataset.galleryImage;

            if (!src) {
                return;
            }

            mainImage.src = src;

            galleryButtons.forEach((item) => {
                item.classList.toggle(
                    'active',
                    item === button
                );
            });
        });
    });
}

/*
|--------------------------------------------------------------------------
| QUANTITY
|--------------------------------------------------------------------------
*/

function initQuantity() {
    const quantity =
        document.getElementById('quantity');

    if (!quantity) {
        return;
    }

    const normalize = () => {
        let value =
            parseInt(quantity.value, 10) || 1;

        const max =
            parseInt(quantity.max, 10);

        if (value < 1) {
            value = 1;
        }

        if (
            !Number.isNaN(max) &&
            max > 0 &&
            value > max
        ) {
            value = max;
        }

        quantity.value = value;
    };

    quantity.addEventListener(
        'input',
        normalize
    );

    quantity.addEventListener(
        'blur',
        normalize
    );
}

/*
|--------------------------------------------------------------------------
| NÚT + / -
|--------------------------------------------------------------------------
*/

window.changeQuantity = function (value) {
    const quantity =
        document.getElementById('quantity');

    if (!quantity) {
        return;
    }

    let current =
        parseInt(quantity.value, 10) || 1;

    const max =
        parseInt(quantity.max, 10);

    current += Number(value) || 0;

    current = Math.max(current, 1);

    if (
        !Number.isNaN(max) &&
        max > 0
    ) {
        current = Math.min(current, max);
    }

    quantity.value = current;
};

/*
|--------------------------------------------------------------------------
| CHECKOUT - GHN LOCATION
|--------------------------------------------------------------------------
*/

function initCheckoutLocations() {
    const routes =
        window.checkoutRoutes;

    const form =
        document.querySelector(
            '[data-checkout-form]'
        );

    /*
    |--------------------------------------------------------------------------
    | Không phải trang checkout
    |--------------------------------------------------------------------------
    */

    if (!routes || !form) {
        return;
    }

    const csrf =
        document.querySelector(
            'meta[name="csrf-token"]'
        )?.content || '';

    const subtotal =
        Number(
            document.getElementById(
                'checkoutSubtotal'
            )?.dataset.value || 0
        );

    const totalEl =
        document.getElementById(
            'checkoutTotal'
        );

    const shippingFeeEl =
        document.getElementById(
            'shippingFeeText'
        );

    const shippingMessage =
        document.getElementById(
            'shippingMessage'
        );

    const placeOrderButton =
        document.getElementById(
            'placeOrderButton'
        );

    const province =
        document.getElementById(
            'province_select'
        );

    const district =
        document.getElementById(
            'district_select'
        );

    const ward =
        document.getElementById(
            'ward_select'
        );

    const provinceName =
        document.getElementById(
            'province_name'
        );

    const districtName =
        document.getElementById(
            'district_name'
        );

    const wardName =
        document.getElementById(
            'ward_name'
        );

    /*
    |--------------------------------------------------------------------------
    | Kiểm tra đủ element
    |--------------------------------------------------------------------------
    */

    if (
        !province ||
        !district ||
        !ward ||
        !provinceName ||
        !districtName ||
        !wardName
    ) {
        console.error(
            'Checkout location elements are missing.'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Trạng thái phí ship
    |--------------------------------------------------------------------------
    */

    let shippingFee = null;

    /*
    |--------------------------------------------------------------------------
    | Fetch JSON
    |--------------------------------------------------------------------------
    */

    async function fetchJson(
        url,
        options = {}
    ) {
        try {
            const response =
                await fetch(url, {
                    ...options,

                    headers: {
                        Accept:
                            'application/json',

                        ...(options.method &&
                        options.method !== 'GET'
                            ? {
                                  'X-CSRF-TOKEN':
                                      csrf,
                              }
                            : {}),

                        ...(options.headers || {}),
                    },
                });

            const payload =
                await response
                    .json()
                    .catch(() => ({}));

            if (
                !response.ok ||
                payload.ok === false
            ) {
                throw new Error(
                    payload.message ||
                        `Không thể tải dữ liệu. Mã lỗi HTTP: ${response.status}`
                );
            }

            return payload.data ?? [];
        } catch (error) {
            console.error(
                'Checkout API error:',
                error
            );

            throw error;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    function setMessage(
        message,
        isError = false
    ) {
        if (!shippingMessage) {
            return;
        }

        shippingMessage.textContent =
            message || '';

        shippingMessage.classList.toggle(
            'error',
            Boolean(isError)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset select
    |--------------------------------------------------------------------------
    */

    function resetSelect(
        select,
        placeholder,
        disabled = true
    ) {
        if (!select) {
            return;
        }

        select.innerHTML =
            `<option value="">${placeholder}</option>`;

        select.disabled = disabled;
    }

    /*
    |--------------------------------------------------------------------------
    | Đồng bộ tên vào hidden input
    |--------------------------------------------------------------------------
    */

    function syncSelectedName(
        select,
        hidden
    ) {
        if (!select || !hidden) {
            return;
        }

        hidden.value =
            select.selectedOptions[0]
                ?.dataset.name || '';
    }

    /*
    |--------------------------------------------------------------------------
    | Cập nhật tổng tiền
    |--------------------------------------------------------------------------
    */

    function updateTotal() {
        const total =
            subtotal +
            Number(shippingFee || 0);

        if (shippingFeeEl) {
            shippingFeeEl.textContent =
                shippingFee === null
                    ? 'Chọn địa chỉ'
                    : formatVnd(shippingFee);
        }

        if (totalEl) {
            totalEl.textContent =
                formatVnd(total);
        }

        /*
        | Không cho đặt hàng nếu chưa tính được phí.
        */

        if (placeOrderButton) {
            const locationReady =
                Boolean(
                    province.value &&
                    district.value &&
                    ward.value
                );

            placeOrderButton.disabled =
                !locationReady ||
                shippingFee === null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fill option
    |--------------------------------------------------------------------------
    */

    function fillOptions(
        select,
        items,
        valueKey,
        labelKey,
        placeholder
    ) {
        if (!select) {
            return;
        }

        select.innerHTML =
            `<option value="">${placeholder}</option>`;

        if (!Array.isArray(items)) {
            select.disabled = true;
            return;
        }

        items.forEach((item) => {
            const option =
                document.createElement('option');

            option.value =
                item[valueKey] ?? '';

            option.textContent =
                item[labelKey] ?? '';

            option.dataset.name =
                item[labelKey] ?? '';

            select.appendChild(option);
        });

        select.disabled =
            items.length === 0;

        /*
        |--------------------------------------------------------------------------
        | Khôi phục old value nếu có
        |--------------------------------------------------------------------------
        */

        const oldValue =
            select.dataset.old;

        if (
            oldValue &&
            [...select.options].some(
                (option) =>
                    String(option.value) ===
                    String(oldValue)
            )
        ) {
            select.value = oldValue;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tính phí vận chuyển
    |--------------------------------------------------------------------------
    */

    async function calculateShippingFee() {
        syncSelectedName(
            ward,
            wardName
        );

        shippingFee = null;

        updateTotal();

        if (
            !district.value ||
            !ward.value
        ) {
            return;
        }

        setMessage(
            'Đang tính phí vận chuyển...'
        );

        try {
            const data =
                await fetchJson(
                    routes.fee,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',
                        },

                        body: JSON.stringify({
                            district_id:
                                Number(
                                    district.value
                                ),

                            ward_code:
                                ward.value,

                            insurance_value:
                                Math.min(
                                    subtotal,
                                    5000000
                                ),
                        }),
                    }
                );

            shippingFee =
                Number(
                    data.shipping_fee ?? 0
                );

            setMessage(
                'Đã tính phí vận chuyển GHN.'
            );
        } catch (error) {
            shippingFee = null;

            setMessage(
                error.message ||
                    'Không thể tính phí vận chuyển.',
                true
            );
        }

        updateTotal();
    }

    /*
    |--------------------------------------------------------------------------
    | CHỌN TỈNH / THÀNH
    |--------------------------------------------------------------------------
    */

    province.addEventListener(
        'change',
        async () => {
            syncSelectedName(
                province,
                provinceName
            );

            /*
            | Reset huyện + xã
            */

            resetSelect(
                district,
                'Đang tải quận/huyện...'
            );

            resetSelect(
                ward,
                'Chọn huyện trước'
            );

            districtName.value = '';
            wardName.value = '';

            shippingFee = null;

            updateTotal();

            if (!province.value) {
                resetSelect(
                    district,
                    'Chọn tỉnh trước'
                );

                resetSelect(
                    ward,
                    'Chọn huyện trước'
                );

                return;
            }

            try {
                setMessage(
                    'Đang tải quận/huyện...'
                );

                const items =
                    await fetchJson(
                        `${routes.districts}/${encodeURIComponent(
                            province.value
                        )}`
                    );

                fillOptions(
                    district,
                    items,
                    'DistrictID',
                    'DistrictName',
                    'Chọn quận/huyện'
                );

                syncSelectedName(
                    district,
                    districtName
                );

                /*
                | Nếu có old district,
                | tự động load xã.
                */

                if (district.value) {
                    district.dispatchEvent(
                        new Event('change')
                    );
                } else {
                    setMessage('');
                }
            } catch (error) {
                resetSelect(
                    district,
                    'Không tải được quận/huyện'
                );

                resetSelect(
                    ward,
                    'Chọn huyện trước'
                );

                setMessage(
                    error.message ||
                        'Không tải được quận/huyện.',
                    true
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | CHỌN QUẬN / HUYỆN
    |--------------------------------------------------------------------------
    */

    district.addEventListener(
        'change',
        async () => {
            syncSelectedName(
                district,
                districtName
            );

            resetSelect(
                ward,
                'Đang tải phường/xã...'
            );

            wardName.value = '';

            shippingFee = null;

            updateTotal();

            if (!district.value) {
                resetSelect(
                    ward,
                    'Chọn huyện trước'
                );

                return;
            }

            try {
                setMessage(
                    'Đang tải phường/xã...'
                );

                const items =
                    await fetchJson(
                        `${routes.wards}/${encodeURIComponent(
                            district.value
                        )}`
                    );

                fillOptions(
                    ward,
                    items,
                    'WardCode',
                    'WardName',
                    'Chọn phường/xã'
                );

                syncSelectedName(
                    ward,
                    wardName
                );

                /*
                | Nếu có old ward thì tự động tính phí.
                */

                if (ward.value) {
                    await calculateShippingFee();
                } else {
                    setMessage('');
                }
            } catch (error) {
                resetSelect(
                    ward,
                    'Không tải được phường/xã'
                );

                setMessage(
                    error.message ||
                        'Không tải được phường/xã.',
                    true
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | CHỌN PHƯỜNG / XÃ
    |--------------------------------------------------------------------------
    */

    ward.addEventListener(
        'change',
        async () => {
            syncSelectedName(
                ward,
                wardName
            );

            await calculateShippingFee();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | SUBMIT CHECKOUT
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        (event) => {
            /*
            | Đồng bộ tên trước khi gửi.
            */

            syncSelectedName(
                province,
                provinceName
            );

            syncSelectedName(
                district,
                districtName
            );

            syncSelectedName(
                ward,
                wardName
            );

            /*
            | Kiểm tra địa chỉ.
            */

            if (
                !province.value ||
                !district.value ||
                !ward.value
            ) {
                event.preventDefault();

                setMessage(
                    'Vui lòng chọn đầy đủ tỉnh/thành phố, quận/huyện và phường/xã.',
                    true
                );

                return;
            }

            /*
            | Kiểm tra phí ship.
            */

            if (shippingFee === null) {
                event.preventDefault();

                setMessage(
                    'Vui lòng chờ hệ thống tính phí vận chuyển trước khi đặt hàng.',
                    true
                );

                return;
            }

            /*
            | Cho submit bình thường.
            */
        }
    );

    /*
    |--------------------------------------------------------------------------
    | LOAD PROVINCES
    |--------------------------------------------------------------------------
    */

    async function loadProvinces() {
        try {
            setMessage(
                'Đang tải danh sách tỉnh/thành phố...'
            );

            const items =
                await fetchJson(
                    routes.provinces
                );

            fillOptions(
                province,
                items,
                'ProvinceID',
                'ProvinceName',
                'Chọn tỉnh/thành phố'
            );

            syncSelectedName(
                province,
                provinceName
            );

            /*
            | Nếu có old province thì tự động load district.
            */

            if (province.value) {
                await new Promise(
                    (resolve) => {
                        const handler =
                            () => {
                                province.removeEventListener(
                                    'change',
                                    handler
                                );

                                resolve();
                            };

                        /*
                        | Không cần dùng handler thực tế
                        | vì change async đã tự xử lý.
                        */

                        province.dispatchEvent(
                            new Event('change')
                        );

                        resolve();
                    }
                );
            } else {
                setMessage('');
            }
        } catch (error) {
            resetSelect(
                province,
                'Không tải được tỉnh/thành'
            );

            resetSelect(
                district,
                'Chọn tỉnh trước'
            );

            resetSelect(
                ward,
                'Chọn huyện trước'
            );

            setMessage(
                error.message ||
                    'Không tải được danh sách tỉnh/thành phố.',
                true
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    resetSelect(
        district,
        'Chọn tỉnh trước'
    );

    resetSelect(
        ward,
        'Chọn huyện trước'
    );

    updateTotal();

    loadProvinces();
}

/*
|--------------------------------------------------------------------------
| DOM READY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    () => {
        initProductGalleryAndVariants();
        initCheckoutLocations();
    }
);