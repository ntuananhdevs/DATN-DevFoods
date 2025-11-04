# 🔍 Debug Healthcheck Failed

## Nguyên Nhân Có Thể

### 1. App Chưa Start Được

**Kiểm tra:**
- Vào Railway → Service → **Logs**
- Xem có lỗi gì khi start

**Nguyên nhân thường gặp:**
- ❌ `APP_KEY` missing → Laravel không boot được
- ❌ Database connection failed → App crash khi boot
- ❌ Service providers fail → App crash
- ❌ Missing dependencies → Composer packages chưa install

### 2. Port Không Đúng

**Kiểm tra:**
- Railway tự động set `PORT` env variable
- App phải listen trên `0.0.0.0:$PORT` (không phải `127.0.0.1`)

### 3. Laravel Boot Process Fail

Khi Laravel boot, nó sẽ:
1. Load service providers
2. Boot database connection
3. Load routes
4. Check config

Nếu bất kỳ bước nào fail, app sẽ crash.

## 🚀 Giải Pháp

### Solution 1: Dùng Endpoint Đơn Giản (Đã Áp Dụng)

Đã tạo `/health.php` - không cần Laravel, chỉ cần PHP:
- Không cần database
- Không cần config
- Chỉ trả về OK

### Solution 2: Kiểm Tra Logs

1. Vào Railway → Service → **Logs**
2. Xem lỗi cụ thể:
   ```
   SQLSTATE[HY000] [2002] Connection refused
   → Database connection failed
   
   No application encryption key has been specified
   → APP_KEY missing
   
   Class XYZ not found
   → Missing dependency
   ```

### Solution 3: Set Environment Variables

Đảm bảo có trong Railway Variables:
- ✅ `APP_KEY` (bắt buộc!)
- ✅ `APP_ENV=production`
- ✅ `APP_DEBUG=false`
- ✅ `DATABASE_URL` hoặc `DB_*` variables
- ✅ `APP_URL` (domain Railway)

### Solution 4: Test Thủ Công

```bash
# Test endpoint đơn giản
railway run curl http://localhost:$PORT/health.php

# Test Laravel endpoint
railway run curl http://localhost:$PORT/up

# Check logs
railway logs
```

### Solution 5: Tạm Thời Tắt Healthcheck

Nếu vẫn không được, tạm thời tắt:
- Xóa `healthcheckPath` trong `railway.json`
- App vẫn chạy nhưng Railway không check

## 📝 Checklist Debug

- [ ] Đã kiểm tra Logs để xem lỗi cụ thể
- [ ] Đã set `APP_KEY` trong Variables
- [ ] Đã set `DATABASE_URL` hoặc `DB_*` variables
- [ ] Đã test endpoint `/health.php` thủ công
- [ ] Đã test endpoint `/up` thủ công
- [ ] Đã xóa cache nếu cần
- [ ] Đã rebuild service

## 💡 Tips

1. **Logs là quan trọng nhất** - Xem logs để biết lỗi cụ thể
2. **APP_KEY là bắt buộc** - Laravel không boot được nếu không có
3. **Database không bắt buộc cho healthcheck** - Nhưng app có thể crash nếu config sai
4. **Endpoint đơn giản** - `/health.php` không cần Laravel, chỉ cần PHP

## 🔄 Next Steps

1. Commit và push changes
2. Kiểm tra Logs trong Railway
3. Test endpoint `/health.php` thủ công
4. Nếu vẫn fail, xem lỗi cụ thể trong Logs

---

**Lưu ý:** Sau khi sửa, commit và push để Railway redeploy!

