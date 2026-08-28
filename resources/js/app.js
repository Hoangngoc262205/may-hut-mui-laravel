import './bootstrap';

function formatVnd(value) {
    return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + 'đ';
}

function initProductGalleryAndVariants() {
    const variants = Array.isArray(window.productVariants)
        ? window.productVariants
        : [];

    const mainImage = document.getElementById('mainProductImage');
    const priceEl = document.getElementById('variantPrice');
    const skuEl = document.getElementById('variantSku');
    const stockEl = document.getElementById('variantStock');
    const selectedVariant = document.getElementById('selectedVariantId');

    const sizeButtons = [
        ...document.querySelectorAll('[data-size-id]')
    ];

    const colorButtons = [
        ...document.querySelectorAll('[data-color-id]')
    ];

    const galleryButtons = [
        ...document.querySelectorAll('[data-gallery-image]')
    ];

    const quantity = document.getElementById('quantity');

    /*
    |--------------------------------------------------------------------------
    | Nếu không phải trang chi tiết sản phẩm
    |--------------------------------------------------------------------------
    */

    if (!selectedVariant) {
        initGalleryOnly(mainImage, galleryButtons);
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Không có variant
    |--------------------------------------------------------------------------
    */

    if (!variants.length) {
        initGalleryOnly(mainImage, galleryButtons);
        initQuantity();
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Variant mặc định
    |--------------------------------------------------------------------------
    */

    let current =
        variants.find(
            (variant) =>
                String(variant.id) === String(window.defaultVariantId)
        ) || variants[0];

    let selectedSize = current?.size_id ?? null;
    let selectedColor = current?.color_id ?? null;


    /*
    |--------------------------------------------------------------------------
    | Tìm variant theo size + color
    |--------------------------------------------------------------------------
    */

    function findVariant() {
        return variants.find((variant) => {

            const sizeMatched =
                selectedSize === null ||
                selectedSize === undefined ||
                String(variant.size_id) === String(selectedSize);

            const colorMatched =
                selectedColor === null ||
                selectedColor === undefined ||
                String(variant.color_id) === String(selectedColor);

            return sizeMatched && colorMatched;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Đổi ảnh
    |--------------------------------------------------------------------------
    */

    function changeMainImage(src) {

        if (!mainImage || !src) {
            return;
        }

        mainImage.src = src;

        galleryButtons.forEach((button) => {

            const buttonImage = button.dataset.galleryImage;

            button.classList.toggle(
                'active',
                buttonImage === src
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Render variant
    |--------------------------------------------------------------------------
    */

    function render() {

        const foundVariant = findVariant();

        if (foundVariant) {
            current = foundVariant;
        }

        if (!current) {
            return;
        }


        /*
        | ID variant
        */

        selectedVariant.value = current.id;


        /*
        | Giá
        */

        if (priceEl) {
            priceEl.textContent = formatVnd(current.price);
        }


        /*
        | SKU
        */

        if (skuEl) {
            skuEl.textContent =
                current.sku || 'Đang cập nhật';
        }


        /*
        | Tồn kho
        */

        if (stockEl) {

            const stock = Number(current.stock) || 0;

            const inStock = stock > 0;

            stockEl.textContent = inStock
                ? `✓ Còn ${stock} sản phẩm`
                : 'Hết hàng';

            stockEl.classList.toggle(
                'out',
                !inStock
            );
        }


        /*
        | Ảnh variant
        */

        const variantImage =
            current.image_url ||
            current.image ||
            null;

        if (variantImage) {
            changeMainImage(variantImage);
        }


        /*
        | Giới hạn số lượng theo tồn kho
        */

        if (quantity) {

            const stock =
                Math.max(Number(current.stock) || 0, 1);

            quantity.max = stock;

            let value =
                parseInt(quantity.value, 10) || 1;

            if (value > stock) {
                value = stock;
            }

            if (value < 1) {
                value = 1;
            }

            quantity.value = value;
        }


        /*
        | Active size
        */

        sizeButtons.forEach((button) => {

            const active =
                String(button.dataset.sizeId) ===
                String(selectedSize);

            button.classList.toggle(
                'active',
                active
            );
        });


        /*
        | Active color
        */

        colorButtons.forEach((button) => {

            const active =
                String(button.dataset.colorId) ===
                String(selectedColor);

            button.classList.toggle(
                'active',
                active
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Kiểm tra option còn hàng
    |--------------------------------------------------------------------------
    */

    function refreshOptionAvailability() {

        /*
        | Size
        */

        sizeButtons.forEach((button) => {

            const sizeId =
                button.dataset.sizeId;

            const available =
                variants.some((variant) => {

                    const sameSize =
                        String(variant.size_id) ===
                        String(sizeId);

                    const sameColor =
                        selectedColor === null ||
                        selectedColor === undefined ||
                        String(variant.color_id) ===
                        String(selectedColor);

                    const inStock =
                        Number(variant.stock) > 0;

                    return (
                        sameSize &&
                        sameColor &&
                        inStock
                    );
                });

            button.disabled = !available;
        });


        /*
        | Color
        */

        colorButtons.forEach((button) => {

            const colorId =
                button.dataset.colorId;

            const available =
                variants.some((variant) => {

                    const sameColor =
                        String(variant.color_id) ===
                        String(colorId);

                    const sameSize =
                        selectedSize === null ||
                        selectedSize === undefined ||
                        String(variant.size_id) ===
                        String(selectedSize);

                    const inStock =
                        Number(variant.stock) > 0;

                    return (
                        sameColor &&
                        sameSize &&
                        inStock
                    );
                });

            button.disabled = !available;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Chọn kích thước
    |--------------------------------------------------------------------------
    */

    sizeButtons.forEach((button) => {

        button.addEventListener('click', () => {

            if (button.disabled) {
                return;
            }

            selectedSize =
                button.dataset.sizeId;


            /*
            | Kiểm tra màu hiện tại có tồn tại
            | với size vừa chọn không
            */

            const currentCombinationExists =
                variants.some((variant) => {

                    return (
                        String(variant.size_id) ===
                        String(selectedSize) &&

                        String(variant.color_id) ===
                        String(selectedColor)
                    );
                });


            /*
            | Nếu không tồn tại thì tự chọn
            | màu đầu tiên còn hàng
            */

            if (!currentCombinationExists) {

                const matchingVariant =
                    variants.find((variant) => {

                        return (
                            String(variant.size_id) ===
                            String(selectedSize) &&

                            Number(variant.stock) > 0
                        );
                    });

                if (matchingVariant) {

                    selectedColor =
                        matchingVariant.color_id;
                }
            }


            render();

            refreshOptionAvailability();
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Chọn màu sắc
    |--------------------------------------------------------------------------
    */

    colorButtons.forEach((button) => {

        button.addEventListener('click', () => {

            if (button.disabled) {
                return;
            }

            selectedColor =
                button.dataset.colorId;


            /*
            | Kiểm tra size hiện tại có tồn tại
            | với màu vừa chọn không
            */

            const currentCombinationExists =
                variants.some((variant) => {

                    return (
                        String(variant.color_id) ===
                        String(selectedColor) &&

                        String(variant.size_id) ===
                        String(selectedSize)
                    );
                });


            /*
            | Nếu không tồn tại thì tự chọn
            | size đầu tiên còn hàng
            */

            if (!currentCombinationExists) {

                const matchingVariant =
                    variants.find((variant) => {

                        return (
                            String(variant.color_id) ===
                            String(selectedColor) &&

                            Number(variant.stock) > 0
                        );
                    });

                if (matchingVariant) {

                    selectedSize =
                        matchingVariant.size_id;
                }
            }


            render();

            refreshOptionAvailability();
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Gallery thumbnail
    |--------------------------------------------------------------------------
    */

    galleryButtons.forEach((button) => {

        button.addEventListener('click', () => {

            const src =
                button.dataset.galleryImage;

            if (!src) {
                return;
            }

            changeMainImage(src);
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Khởi tạo
    |--------------------------------------------------------------------------
    */

    render();

    refreshOptionAvailability();

    initQuantity();
}


/*
|--------------------------------------------------------------------------
| Gallery khi không có variant
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
| Tăng / giảm số lượng
|--------------------------------------------------------------------------
*/

function initQuantity() {

    const quantity =
        document.getElementById('quantity');

    if (!quantity) {
        return;
    }


    /*
    | Không cho nhập số nhỏ hơn 1
    */

    quantity.addEventListener('input', () => {

        let value =
            parseInt(quantity.value, 10) || 1;

        const max =
            parseInt(quantity.max, 10);

        if (value < 1) {
            value = 1;
        }

        if (!Number.isNaN(max) && value > max) {
            value = max;
        }

        quantity.value = value;
    });


    /*
    | Khi blur khỏi input
    */

    quantity.addEventListener('blur', () => {

        let value =
            parseInt(quantity.value, 10) || 1;

        const max =
            parseInt(quantity.max, 10);

        if (value < 1) {
            value = 1;
        }

        if (!Number.isNaN(max) && value > max) {
            value = max;
        }

        quantity.value = value;
    });
}


/*
|--------------------------------------------------------------------------
| Hàm tăng giảm số lượng
|--------------------------------------------------------------------------
| Dùng trực tiếp từ Blade:
| onclick="changeQuantity(-1)"
| onclick="changeQuantity(1)"
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

    if (current < 1) {
        current = 1;
    }

    if (!Number.isNaN(max) && current > max) {
        current = max;
    }

    quantity.value = current;
};


/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    initProductGalleryAndVariants
);