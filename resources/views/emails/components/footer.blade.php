        </div> <!-- End content div -->
        <div class="footer" style="border-top: 1px solid #eee; padding: 15px 0; text-align: center; font-size: 14px; color: #666;">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Bảo lưu mọi quyền.</p>
            <p>
                Nếu bạn có câu hỏi, vui lòng liên hệ với chúng tôi qua email 
                <a href="mailto:{{ config('mail.from.address') }}" style="color: #007bff; text-decoration: none;">
                    {{ config('mail.from.address') }}
                </a>
            </p>
            <div class="social-links" style="margin-top: 15px;">
                <a href="#" style="margin: 0 5px; color: #007bff; text-decoration: none;">Facebook</a>
                <a href="#" style="margin: 0 5px; color: #007bff; text-decoration: none;">Instagram</a>
                <a href="#" style="margin: 0 5px; color: #007bff; text-decoration: none;">Twitter</a>
            </div>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                Bạn nhận được email này vì bạn đã đăng ký tại {{ config('app.name') }}.
                <br>
            </p>
        </div>
    </div> <!-- End email-container div -->
</body>
</html> 