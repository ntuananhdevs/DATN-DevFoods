# API Order Endpoint Documentation

## Tạo Đơn Hàng Qua API

Endpoint này cho phép các hệ thống bên ngoài hoặc admin tạo đơn hàng mới một cách nhanh chóng và đơn giản.

### Thông Tin Endpoint

- **URL**: `/api/orders`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Authentication**: Không yêu cầu (có thể thêm sau nếu cần)

### Request Body

```json
{
  "user_id": 1,
  "address_id": 5,
  "payment_method": "cod",
  "note": "Giao trong giờ hành chính",
  "items": [
    { "product_id": 12, "quantity": 2 },
    { "product_id": 8, "quantity": 1 }
  ]
}
```

### Mô Tả Các Trường

| Trường | Kiểu | Bắt buộc | Mô tả |
|--------|------|----------|-------|
| `user_id` | integer | ✅ | ID của người dùng trong hệ thống |
| `address_id` | integer | ✅ | ID của địa chỉ giao hàng (phải thuộc về user_id) |
| `payment_method` | string | ✅ | Phương thức thanh toán: `cod`, `vnpay`, `balance` |
| `note` | string | ❌ | Ghi chú đơn hàng (tối đa 1000 ký tự) |
| `items` | array | ✅ | Danh sách sản phẩm trong đơn hàng (tối thiểu 1 sản phẩm) |
| `items[].product_id` | integer | ✅ | ID của sản phẩm |
| `items[].quantity` | integer | ✅ | Số lượng sản phẩm (1-100) |

### Response Thành Công (201)

```json
{
  "success": true,
  "message": "Tạo đơn hàng thành công",
  "order_id": 1005,
  "order_code": "API-A1B2C3D4",
  "total_amount": 125000
}
```

### Response Lỗi Validation (422)

```json
{
  "success": false,
  "message": "Dữ liệu đầu vào không hợp lệ",
  "errors": {
    "user_id": ["Trường user_id là bắt buộc."],
    "items.0.quantity": ["Số lượng phải lớn hơn 0."]
  }
}
```

### Response Lỗi Không Tìm Thấy Địa Chỉ (404)

```json
{
  "success": false,
  "message": "Địa chỉ không tồn tại hoặc không thuộc về người dùng này"
}
```

### Response Lỗi Hệ Thống (500)

```json
{
  "success": false,
  "message": "Đã có lỗi xảy ra: Không tìm thấy biến thể có sẵn cho sản phẩm: Bánh mì"
}
```

## Ví Dụ Sử Dụng

### cURL

```bash
curl -X POST http://your-domain.com/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "address_id": 5,
    "payment_method": "cod",
    "note": "Giao trong giờ hành chính",
    "items": [
      { "product_id": 12, "quantity": 2 },
      { "product_id": 8, "quantity": 1 }
    ]
  }'
```

### PHP (sử dụng Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->post('http://your-domain.com/api/orders', [
    'json' => [
        'user_id' => 1,
        'address_id' => 5,
        'payment_method' => 'cod',
        'note' => 'Giao trong giờ hành chính',
        'items' => [
            ['product_id' => 12, 'quantity' => 2],
            ['product_id' => 8, 'quantity' => 1]
        ]
    ]
]);

$data = json_decode($response->getBody(), true);
echo "Order ID: " . $data['order_id'];
```

### JavaScript (Fetch API)

```javascript
fetch('http://your-domain.com/api/orders', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    user_id: 1,
    address_id: 5,
    payment_method: 'cod',
    note: 'Giao trong giờ hành chính',
    items: [
      { product_id: 12, quantity: 2 },
      { product_id: 8, quantity: 1 }
    ]
  })
})
.then(response => response.json())
.then(data => {
  console.log('Order created:', data.order_id);
})
.catch(error => {
  console.error('Error:', error);
});
```

## Logic Xử Lý

### 1. Validation
- Kiểm tra tất cả các trường bắt buộc
- Xác minh user_id và address_id tồn tại trong database
- Đảm bảo address_id thuộc về user_id
- Kiểm tra product_id có tồn tại không

### 2. Tính Toán Giá
- Tự động lấy variant đầu tiên có sẵn của mỗi sản phẩm
- Tính subtotal dựa trên giá variant và số lượng
- Áp dụng phí giao hàng: 25,000đ (miễn phí nếu đơn hàng > 200,000đ)
- Không áp dụng discount (có thể mở rộng sau)

### 3. Tạo Đơn Hàng
- Tạo order với status = 'awaiting_confirmation'
- Tạo order_items tương ứng
- Gán branch_id = 1 (mặc định)
- Dispatch event NewOrderReceived để thông báo cho branch

## Lưu Ý Quan Trọng

1. **Variant Selection**: API tự động chọn variant đầu tiên có sẵn (active = true) của mỗi sản phẩm
2. **Branch Assignment**: Hiện tại gán cứng branch_id = 1, có thể cần cấu hình linh hoạt hơn
3. **No Authentication**: Endpoint hiện tại không yêu cầu xác thực, cần cân nhắc bảo mật
4. **Error Handling**: Sử dụng database transaction để đảm bảo tính nhất quán
5. **Event Dispatch**: Tự động thông báo cho branch khi có đơn hàng mới

## Mở Rộng Trong Tương Lai

- [ ] Thêm authentication/authorization
- [ ] Cho phép chọn branch_id
- [ ] Hỗ trợ chọn variant cụ thể
- [ ] Áp dụng discount code
- [ ] Thêm topping cho sản phẩm
- [ ] Rate limiting
- [ ] Logging chi tiết 