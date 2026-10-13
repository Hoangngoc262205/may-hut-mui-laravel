<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;

class BestSellingProductsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Categories
        $categories = [];
        foreach ([
            ['name' => 'Máy hút mùi kính cong', 'slug' => 'may-hut-mui-kinh-cong'],
            ['name' => 'Máy hút mùi âm tủ', 'slug' => 'may-hut-mui-am-tu'],
            ['name' => 'Máy hút mùi áp tường', 'slug' => 'may-hut-mui-ap-tuong'],
        ] as $item) {
            $categories[$item['slug']] = Category::firstOrCreate(
                ['slug' => $item['slug']],
                ['name' => $item['name'], 'description' => 'Thiết bị hút mùi cho căn bếp hiện đại.', 'status' => 'active']
            );
        }

        // 2. Ensure Major Brands (Top reputable kitchen appliances brands)
        $brandDefinitions = [
            [
                'slug' => 'bosch',
                'name' => 'Bosch',
                'description' => 'Tập đoàn công nghệ và thiết bị gia dụng số 1 nước Đức, biểu tượng của độ bền và công nghệ tiên tiến toàn cầu.',
            ],
            [
                'slug' => 'hafele',
                'name' => 'Hafele',
                'description' => 'Thương hiệu danh tiếng nước Đức với hơn 100 năm phát triển các giải pháp phụ kiện & thiết bị bếp cao cấp.',
            ],
            [
                'slug' => 'malloca',
                'name' => 'Malloca',
                'description' => 'Thương hiệu thiết bị nhà bếp cao cấp mang phong cách Châu Âu từ Tây Ban Nha, nổi bật với thiết kế sang trọng.',
            ],
            [
                'slug' => 'canzy',
                'name' => 'Canzy',
                'description' => 'Thương hiệu thiết bị nhà bếp công nghệ Ý bán chạy hàng đầu tại Việt Nam ở phân khúc phổ thông và tầm trung.',
            ],
            [
                'slug' => 'sunhouse',
                'name' => 'Sunhouse',
                'description' => 'Tập đoàn gia dụng hàng đầu quốc dân tại Việt Nam, mang đến các sản phẩm chất lượng tốt, giá thành hợp lý.',
            ],
            [
                'slug' => 'electrolux',
                'name' => 'Electrolux',
                'description' => 'Tập đoàn thiết bị gia dụng đa quốc gia hàng đầu Thụy Điển với hơn 100 năm kinh nghiệm trên toàn thế giới.',
            ],
            [
                'slug' => 'kaff',
                'name' => 'Kaff',
                'description' => 'Thương hiệu thiết bị nhà bếp thông minh tiêu chuẩn Đức với động cơ công suất lớn và cảm biến cử chỉ hiện đại.',
            ],
            [
                'slug' => 'teka',
                'name' => 'Teka',
                'description' => 'Tập đoàn đa quốc gia sản xuất thiết bị nhà bếp cao cấp từ Tây Ban Nha và Đức.',
            ],
        ];

        $brands = [];
        foreach ($brandDefinitions as $b) {
            $brands[$b['slug']] = Brand::updateOrCreate(
                ['slug' => $b['slug']],
                ['name' => $b['name'], 'description' => $b['description'], 'status' => 'active']
            );
        }

        // 3. Ensure Sizes & Colors
        $sizes = [];
        foreach (['60 cm', '70 cm', '90 cm'] as $name) {
            $sizes[$name] = Size::firstOrCreate(['name' => $name], ['status' => 'active']);
        }

        $colors = [];
        foreach ([
            ['name' => 'Đen', 'hex_code' => '#111827'],
            ['name' => 'Inox', 'hex_code' => '#b8bec5'],
        ] as $item) {
            $colors[$item['name']] = Color::updateOrCreate(
                ['name' => $item['name']],
                ['hex_code' => $item['hex_code'], 'status' => 'active']
            );
        }

        // 4. 10 Best-Selling Products from Major Brands
        $bestSellingProducts = [
            // 1. Bosch Serie 6 DWB67CM50B
            [
                'slug' => 'bosch-serie-6-dwb67cm50b',
                'name' => 'Máy hút mùi Bosch Serie 6 DWB67CM50B chữ T',
                'brand' => 'bosch',
                'category' => 'may-hut-mui-ap-tuong',
                'description' => 'Dòng máy hút mùi chữ T gắn tường cao cấp chuẩn Đức từ Bosch. Động cơ EcoSilence Drive vận hành êm ái vượt trội với độ ồn chỉ 43dB, hệ thống cảm biến thông minh TouchSelect và lưới lọc mỡ kim loại 3 lớp dễ vệ sinh trong máy rửa bát.',
                'image' => 'images/products/bosch-dwb67cm50b.jpg',
                'secondary_image' => 'images/products/bosch.jpg',
                'hood_type' => 'Áp tường chữ T',
                'suction_capacity' => '710 m³/h',
                'noise_level' => '43 dB (Siêu êm)',
                'speed_levels' => '3 tốc độ + 1 chế độ hút chuyên sâu Booster',
                'voltage' => '220V - 240V / 50Hz',
                'material' => 'Thép không gỉ Inox cao cấp chống bám vân tay',
                'dimensions' => '700 x 500 x 672-1064 mm',
                'warranty' => '36 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'BOS-DWB67-INOX-70', 'price' => 15900000, 'stock' => 15],
                    ['size' => '90 cm', 'color' => 'Inox', 'sku' => 'BOS-DWB67-INOX-90', 'price' => 17500000, 'stock' => 8],
                ],
            ],

            // 2. Bosch DFT63AC50 Serie 2
            [
                'slug' => 'bosch-dft63ac50-serie-2',
                'name' => 'Máy hút mùi âm tủ Bosch DFT63AC50 Serie 2',
                'brand' => 'bosch',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Máy hút mùi âm tủ dạng kéo trượt gọn gàng của Bosch. Lý tưởng cho các căn hộ cao cấp muốn tiết kiệm tối đa diện tích bếp nhưng vẫn đảm bảo khả năng khử mùi dầu mỡ triệt để với độ bền bỉ tiêu chuẩn Đức.',
                'image' => 'images/products/bosch-dw9b7k60.jpg',
                'secondary_image' => 'images/products/bosch.jpg',
                'hood_type' => 'Âm tủ rút (Telescopic)',
                'suction_capacity' => '368 m³/h',
                'noise_level' => '53 dB',
                'speed_levels' => '3 tốc độ quạt',
                'voltage' => '220V - 240V / 50Hz',
                'material' => 'Inox bạc mờ sáng bóng + lưới lọc nhôm nhiều lớp',
                'dimensions' => '600 x 300 x 180 mm',
                'warranty' => '24 tháng chính hãng',
                'variants' => [
                    ['size' => '60 cm', 'color' => 'Inox', 'sku' => 'BOS-DFT63-INOX-60', 'price' => 5890000, 'stock' => 22],
                ],
            ],

            // 3. Hafele HH-TI60D
            [
                'slug' => 'hafele-hh-ti60d-am-tu',
                'name' => 'Máy hút mùi âm tủ Hafele HH-TI60D',
                'brand' => 'hafele',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Model âm tủ bán chạy nhất của thương hiệu Hafele (Đức). Thiết kế dạng kéo trượt thông minh, tự động bật hút khi kéo ra và tắt khi đóng lại. Đèn LED chiếu sáng tiết kiệm điện và lưới lọc nhôm dày dặn ngăn mỡ hiệu quả.',
                'image' => 'images/products/hafele-hh-ti60d.jpg',
                'secondary_image' => null,
                'hood_type' => 'Âm tủ kéo trượt',
                'suction_capacity' => '500 m³/h',
                'noise_level' => '50 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V / 50Hz',
                'material' => 'Thép không gỉ Inox cao cấp',
                'dimensions' => '600 / 700 x 280 x 175 mm',
                'warranty' => '36 tháng chính hãng',
                'variants' => [
                    ['size' => '60 cm', 'color' => 'Inox', 'sku' => 'HAF-TI60D-INOX-60', 'price' => 3890000, 'stock' => 25],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'HAF-TI60D-INOX-70', 'price' => 4190000, 'stock' => 18],
                ],
            ],

            // 4. Hafele HC-BI70B Mặt Kính Đen
            [
                'slug' => 'hafele-hc-bi70b-kinh-den',
                'name' => 'Máy hút mùi âm tủ Hafele HC-BI70B mặt kính đen',
                'brand' => 'hafele',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Máy hút mùi âm tủ cao cấp Hafele sở hữu mặt đáy ốp kính đen bóng nguyên khối sang trọng, bảng điều khiển cảm ứng LED ẩn hiện đại và dải đèn LED ấm dịu mắt. Lực hút mạnh 650 m³/h giúp gian bếp luôn sạch thoáng mùi dầu mỡ.',
                'image' => 'images/products/hafele-hc-bi70b.jpg',
                'secondary_image' => null,
                'hood_type' => 'Âm tủ kính đen cảm ứng',
                'suction_capacity' => '650 m³/h',
                'noise_level' => '52 dB',
                'speed_levels' => '3 tốc độ cảm ứng Touch Control',
                'voltage' => '220V - 240V / 50Hz',
                'material' => 'Kính cường lực đen + Thân thép sơn tĩnh điện đen',
                'dimensions' => '700 x 280 x 275 mm',
                'warranty' => '36 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'HAF-BI70B-BLK-70', 'price' => 6490000, 'stock' => 16],
                ],
            ],

            // 5. Malloca K7199 Kính Cong
            [
                'slug' => 'malloca-k7199-kinh-cong',
                'name' => 'Máy hút mùi kính cong Malloca K7199',
                'brand' => 'malloca',
                'category' => 'may-hut-mui-kinh-cong',
                'description' => 'Dòng kính cong biểu tượng của Malloca phong cách Tây Ban Nha. Vòm kính cong cường lực dày 8mm uốn lượn sắc sảo, công suất hút cực khủng lên tới 1000 m³/h nhanh chóng cuốn sạch mọi khói mùi thức ăn, bảng điều khiển cảm ứng có màn hình LED hiển thị thời gian.',
                'image' => 'images/products/malloca-k7199.jpg',
                'secondary_image' => null,
                'hood_type' => 'Kính cong cao cấp',
                'suction_capacity' => '1000 m³/h (Siêu mạnh)',
                'noise_level' => '56 dB',
                'speed_levels' => '3 tốc độ cảm ứng + Màn hình LED',
                'voltage' => '220V / 50Hz',
                'material' => 'Kính cường lực 8mm + Inox cao cấp',
                'dimensions' => '700 / 900 x 500 x 560-1000 mm',
                'warranty' => '36 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'MAL-K7199-INOX-70', 'price' => 9200000, 'stock' => 14],
                    ['size' => '90 cm', 'color' => 'Inox', 'sku' => 'MAL-K7199-INOX-90', 'price' => 9900000, 'stock' => 10],
                ],
            ],

            // 6. Canzy CZ-70DGB Cảm Ứng Vẫy Tay
            [
                'slug' => 'canzy-cz-70dgb-cam-ung-vay-tay',
                'name' => 'Máy hút mùi kính cong Canzy CZ-70DGB vẫy tay thông minh',
                'brand' => 'canzy',
                'category' => 'may-hut-mui-kinh-cong',
                'description' => 'Mẫu máy hút mùi bán chạy số 1 của thương hiệu Canzy. Tích hợp công nghệ cảm ứng vẫy tay Smart Motion Sensor: chỉ cần gạt tay nhẹ qua trái hoặc phải để bật tắt và tăng giảm tốc độ hút mà không cần chạm tay dính dầu mỡ vào máy. Công suất hút vô địch 1400 m³/h.',
                'image' => 'images/products/canzy-cz70ea.jpg',
                'secondary_image' => 'images/products/canzy.jpg',
                'hood_type' => 'Kính cong cảm ứng vẫy tay',
                'suction_capacity' => '1400 m³/h (Cực đại)',
                'noise_level' => '48 dB',
                'speed_levels' => '3 tốc độ + Cảm biến cử chỉ vẫy tay',
                'voltage' => '220V / 50Hz',
                'material' => 'Kính cường lực đen bóng + Inox gương',
                'dimensions' => '700 x 480 x 500-900 mm',
                'warranty' => '24 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'CZY-70DGB-BLK-70', 'price' => 4590000, 'stock' => 30],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'CZY-70DGB-INOX-70', 'price' => 4790000, 'stock' => 20],
                ],
            ],

            // 7. Canzy CZ-6002G Âm Tủ
            [
                'slug' => 'canzy-cz-6002g-am-tu-rut',
                'name' => 'Máy hút mùi âm tủ rút Canzy CZ-6002G',
                'brand' => 'canzy',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Vua doanh số phân khúc nhà ở chung cư và bếp nhỏ. Giá thành kinh tế, vận hành bền bỉ bằng động cơ tuabin đôi công suất hút 750 m³/h, thiết kế kéo trượt thanh lịch giấu gọn trong hộc tủ bếp trên.',
                'image' => 'images/products/canzy.jpg',
                'secondary_image' => 'images/products/canzy-cz70ea.jpg',
                'hood_type' => 'Âm tủ kéo trượt',
                'suction_capacity' => '750 m³/h',
                'noise_level' => '45 dB',
                'speed_levels' => '2 tốc độ kéo tự động',
                'voltage' => '220V / 50Hz',
                'material' => 'Thép không gỉ Inox sáng bóng + Kính bảo vệ',
                'dimensions' => '600 / 700 x 280 x 175 mm',
                'warranty' => '24 tháng chính hãng',
                'variants' => [
                    ['size' => '60 cm', 'color' => 'Inox', 'sku' => 'CZY-6002G-INOX-60', 'price' => 2190000, 'stock' => 35],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'CZY-6002G-INOX-70', 'price' => 2390000, 'stock' => 28],
                ],
            ],

            // 8. Sunhouse Mama MMB6819
            [
                'slug' => 'sunhouse-mama-mmb6819-kinh-cong',
                'name' => 'Máy hút mùi kính cong Sunhouse Mama MMB6819',
                'brand' => 'sunhouse',
                'category' => 'may-hut-mui-kinh-cong',
                'description' => 'Dòng sản phẩm cao cấp phân khúc gia đình từ Sunhouse - Thương hiệu Quốc gia Việt Nam. Mô-tơ đồng nguyên chất công suất hút 1100 m³/h bền bỉ dài lâu, vòm kính uốn cong tinh tế và hệ thống khử mùi kép (than hoạt tính + ống dẫn thoát khí).',
                'image' => 'images/products/sunhouse-mmb6819.jpg',
                'secondary_image' => null,
                'hood_type' => 'Kính cong cao cấp',
                'suction_capacity' => '1100 m³/h',
                'noise_level' => '57 dB',
                'speed_levels' => '3 tốc độ phím cơ êm',
                'voltage' => '220V / 50Hz',
                'material' => 'Thép không gỉ Inox sáng bóng + Kính cường lực chịu nhiệt',
                'dimensions' => '700 x 480 x 550-950 mm',
                'warranty' => '24 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'SUN-MMB6819-INOX-70', 'price' => 3490000, 'stock' => 24],
                ],
            ],

            // 9. Electrolux EFT7516X
            [
                'slug' => 'electrolux-eft7516x-inox',
                'name' => 'Máy hút mùi cổ điển Electrolux EFT7516X',
                'brand' => 'electrolux',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Máy hút mùi thương hiệu Electrolux (Thụy Điển) đạt tiêu chuẩn chất lượng Châu Âu. Thân máy inox sáng bóng chống trầy xước, lưới lọc nhôm dày dặn có thể tháo rời rửa sạch bằng máy rửa bát, động cơ hút êm ái khử sạch mùi đồ ăn.',
                'image' => 'images/products/electrolux-efc936gar.jpg',
                'secondary_image' => 'images/products/electrolux.jpg',
                'hood_type' => 'Cổ điển / Âm tủ mỏng',
                'suction_capacity' => '550 m³/h',
                'noise_level' => '55 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V - 240V / 50Hz',
                'material' => 'Thép không gỉ Inox cao cấp',
                'dimensions' => '700 x 500 x 140 mm',
                'warranty' => '24 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'ELX-EFT7516-INOX-70', 'price' => 3790000, 'stock' => 20],
                ],
            ],

            // 10. Kaff KF-TL700ST
            [
                'slug' => 'kaff-kf-tl700st-cam-bien-vay-tay',
                'name' => 'Máy hút mùi âm tủ Kaff KF-TL700ST cảm biến vẫy tay',
                'brand' => 'kaff',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Thiết bị hút mùi thông minh công nghệ Đức của Kaff. Nổi bật với mặt kính gương đen sang chảnh, cảm biến chuyển động không chạm thông minh (Hand Wave Sensor), lực hút 750 m³/h vận hành siêu êm thích hợp mọi không gian bếp hiện đại.',
                'image' => 'images/products/kaff-kfgb706.jpg',
                'secondary_image' => 'images/products/kaff.jpg',
                'hood_type' => 'Âm tủ cảm biến vẫy tay',
                'suction_capacity' => '750 m³/h',
                'noise_level' => '48 dB (Êm ái)',
                'speed_levels' => '3 tốc độ + Cảm ứng chạm / Vẫy tay không chạm',
                'voltage' => '220V / 50Hz',
                'material' => 'Mặt kính gương đen bóng + Inox cao cấp',
                'dimensions' => '700 x 280 x 280 mm',
                'warranty' => '36 tháng chính hãng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'KAF-TL700ST-BLK-70', 'price' => 4850000, 'stock' => 22],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'KAF-TL700ST-INOX-70', 'price' => 4950000, 'stock' => 15],
                ],
            ],
        ];

        // 5. Populate products, details, variants, images
        foreach ($bestSellingProducts as $data) {
            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brands[$data['brand']]->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 'active',
                ]
            );

            ProductDetail::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'suction_capacity' => $data['suction_capacity'],
                    'noise_level' => $data['noise_level'],
                    'speed_levels' => $data['speed_levels'],
                    'voltage' => $data['voltage'],
                    'material' => $data['material'],
                    'hood_type' => $data['hood_type'],
                    'dimensions' => $data['dimensions'],
                    'warranty' => $data['warranty'],
                ]
            );

            // Primary Image
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image' => $data['image']],
                ['product_variant_id' => null, 'is_primary' => true, 'sort_order' => 0]
            );

            // Secondary Image if available
            if (!empty($data['secondary_image'])) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'image' => $data['secondary_image']],
                    ['product_variant_id' => null, 'is_primary' => false, 'sort_order' => 1]
                );
            }

            // Variants
            foreach ($data['variants'] as $variantData) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'size_id' => $sizes[$variantData['size']]->id,
                        'color_id' => $colors[$variantData['color']]->id,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'image' => $data['image'],
                        'status' => 'active',
                    ]
                );

                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'image' => $data['image']],
                    ['is_primary' => false, 'sort_order' => 2]
                );
            }
        }

        // 6. Create Demo Customers & Reviews for Top-Selling Products
        $customers = [
            ['name' => 'Nguyễn Tuấn Anh', 'email' => 'tuananh.nguyen@example.com', 'phone' => '0912345678'],
            ['name' => 'Trần Hoàng Yến', 'email' => 'hoangyen.tran@example.com', 'phone' => '0923456789'],
            ['name' => 'Lê Minh Đức', 'email' => 'minhduc.le@example.com', 'phone' => '0934567890'],
            ['name' => 'Đỗ Quốc Huy', 'email' => 'quochuy.do@example.com', 'phone' => '0945678901'],
            ['name' => 'Phạm Thị Mai', 'email' => 'thimai.pham@example.com', 'phone' => '0956789012'],
            ['name' => 'Bùi Ngọc Linh', 'email' => 'ngoclinh.bui@example.com', 'phone' => '0967890123'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = \App\Models\User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'phone' => $c['phone'],
                    'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }

        $reviewSeedData = [
            'bosch-serie-6-dwb67cm50b' => [
                ['customer_idx' => 0, 'rating' => 5, 'comment' => 'Máy chạy cực kỳ êm ái, nấu món nướng hay xào rán chỉ 2 phút là sạch bay khói mùi. Hàng chuẩn thương hiệu Bosch của Đức chất lượng quá đỉnh!'],
                ['customer_idx' => 1, 'rating' => 5, 'comment' => 'Thiết kế chữ T siêu sang, cảm ứng nhạy và lưới lọc inox tháo ra rửa máy rửa bát rất tiện lợi.'],
            ],
            'canzy-cz-70dgb-cam-ung-vay-tay' => [
                ['customer_idx' => 1, 'rating' => 5, 'comment' => 'Chức năng vẫy tay cử chỉ cực kỳ tiện lợi khi đang nấu ăn dính mỡ. Lực hút 1400 m³/h siêu mạnh, xứng đáng là dòng bán chạy nhất!'],
            ],
            'hafele-hh-ti60d-am-tu' => [
                ['customer_idx' => 2, 'rating' => 5, 'comment' => 'Máy âm tủ kéo trượt thông minh, hoàn thiện inox bóng bẩy chuẩn thương hiệu Hafele. Rất êm và tiết kiệm diện tích cho chung cư.'],
            ],
            'malloca-k7199-kinh-cong' => [
                ['customer_idx' => 3, 'rating' => 5, 'comment' => 'Vòm kính cong cường lực dày dặn nhìn rất đẳng cấp phong cách Châu Âu. Khử mùi than hoạt tính hoạt động rất hiệu quả.'],
            ],
            'sunhouse-mama-mmb6819-kinh-cong' => [
                ['customer_idx' => 4, 'rating' => 5, 'comment' => 'Thương hiệu Sunhouse quốc dân giá vừa túi tiền mà lực hút 1100 m³/h rất khỏe. Bảo hành chính hãng 2 năm an tâm sử dụng.'],
            ],
            'hafele-hc-bi70b-kinh-den' => [
                ['customer_idx' => 5, 'rating' => 5, 'comment' => 'Mặt đáy kính đen bóng nguyên khối sang trọng tuyệt đẹp, dải đèn LED ấm dịu mắt và phím cảm ứng ẩn cực kỳ đẳng cấp.'],
            ],
        ];

        foreach ($reviewSeedData as $slug => $reviews) {
            $prod = Product::where('slug', $slug)->first();
            if ($prod) {
                foreach ($reviews as $rev) {
                    $u = $customerModels[$rev['customer_idx']];
                    \App\Models\Review::updateOrCreate(
                        ['user_id' => $u->id, 'product_id' => $prod->id],
                        [
                            'rating' => $rev['rating'],
                            'comment' => $rev['comment'],
                            'status' => 'approved',
                        ]
                    );
                }
            }
        }

        // 7. Seed Simulated Order Items for Top Selling Showcase
        $bestSellingSlugs = [
            'canzy-cz-70dgb-cam-ung-vay-tay' => 38,
            'bosch-serie-6-dwb67cm50b' => 29,
            'hafele-hh-ti60d-am-tu' => 26,
            'canzy-cz-6002g-am-tu-rut' => 24,
            'malloca-k7199-kinh-cong' => 19,
            'sunhouse-mama-mmb6819-kinh-cong' => 17,
            'hafele-hc-bi70b-kinh-den' => 15,
            'electrolux-eft7516x-inox' => 14,
        ];

        $demoOrder = \App\Models\Order::firstOrCreate(
            ['order_code' => 'ORD-BESTSELLER-SEED'],
            [
                'user_id' => $customerModels[0]->id,
                'customer_name' => 'Đơn Mua Thực Tế Showcase',
                'phone' => '0912345678',
                'email' => 'sales@mayhutmui.vn',
                'address' => 'Hà Nội & TP. Hồ Chí Minh',
                'subtotal' => 99999999,
                'shipping_fee' => 0,
                'total' => 99999999,
                'status' => 'completed',
            ]
        );

        foreach ($bestSellingSlugs as $slug => $qty) {
            $p = Product::where('slug', $slug)->first();
            if ($p) {
                $variant = $p->variants()->first();
                if ($variant) {
                    \App\Models\OrderItem::updateOrCreate(
                        [
                            'order_id' => $demoOrder->id,
                            'product_variant_id' => $variant->id,
                        ],
                        [
                            'product_name' => $p->name,
                            'variant_name' => $variant->variant_name,
                            'quantity' => $qty,
                            'price' => $variant->price,
                            'subtotal' => $variant->price * $qty,
                        ]
                    );
                }
            }
        }
    }
}
