<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập & Đăng ký - BAV AI Ecosystem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./auth.css">
</head>
<body>
    <a href="../../index.html" class="btn-back" title="Quay lại Trang chủ">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form id="register-form">
                <h1>Tạo tài khoản</h1>
                <span class="subtitle">Sử dụng email @hvnh.edu.vn</span>
                
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" placeholder="Họ và tên" required />
                </div>
                
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" placeholder="Email sinh viên BAV" required />
                </div>
                
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="reg-password" placeholder="Mật khẩu" required />
                </div>
                
                <ul class="password-criteria" id="password-criteria">
                    <li id="rule-length"><i class="fa-solid fa-circle-xmark"></i> Ít nhất 8 ký tự</li>
                    <li id="rule-upper"><i class="fa-solid fa-circle-xmark"></i> Có chữ cái viết hoa</li>
                    <li id="rule-number"><i class="fa-solid fa-circle-xmark"></i> Có chữ số</li>
                    <li id="rule-special"><i class="fa-solid fa-circle-xmark"></i> Ký tự đặc biệt</li>
                </ul>
                <button type="submit" class="btn-submit">Đăng ký ngay</button>
                
                <p class="mobile-switch">Đã có tài khoản? <span id="mobileSignIn">Đăng nhập ngay</span></p>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form id="login-form">
                <h1>Đăng nhập</h1>
                <span class="subtitle">Chào mừng Team Lead Nhóm 6</span>
                
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="login-email" placeholder="Email nhà trường" required />
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="Mật khẩu" required />
                </div>
                
                <button type="submit" class="btn-submit">Đăng nhập</button>

                <p class="mobile-switch">Chưa có tài khoản? <span id="mobileSignUp">Đăng ký ngay</span></p>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Sẵn sàng?</h1>
                    <p>Đã có tài khoản sinh viên BAV, hãy đăng nhập ngay.</p>
                    <button class="ghost" id="signIn">Quay lại Đăng nhập</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Chào sếp Đạt!</h1>
                    <p>Chưa có tài khoản? Hãy đăng ký để tham gia hệ sinh thái.</p>
                    <button class="ghost" id="signUp">Tạo tài khoản mới</button>
                </div>
            </div>
        </div>
    </div>

    <script src="./auth.js"></script>
</body>
</html>