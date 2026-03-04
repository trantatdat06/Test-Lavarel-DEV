<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập & Đăng ký - BAV AI Ecosystem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('views/page/auth/auth.css') }}">
</head>
<body>
    <a href="{{ url('/') }}" class="btn-back" title="Quay lại Trang chủ">
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

    <script>
    // Các nút chuyển đổi
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const mobileSignUp = document.getElementById('mobileSignUp');
    const mobileSignIn = document.getElementById('mobileSignIn');
    const container = document.getElementById('container');

    // Hàm chuyển đổi chung
    const toggleForm = (isSignUp) => {
        if (isSignUp) {
            container.classList.add("right-panel-active");
        } else {
            container.classList.remove("right-panel-active");
        }
    };

    signUpButton?.addEventListener('click', () => toggleForm(true));
    signInButton?.addEventListener('click', () => toggleForm(false));
    mobileSignUp?.addEventListener('click', () => toggleForm(true));
    mobileSignIn?.addEventListener('click', () => toggleForm(false));

    // Logic kiểm tra mật khẩu
    const regPassword = document.getElementById('reg-password');
    const criteria = {
        length: { id: 'rule-length', regex: /.{8,}/ },
        upper: { id: 'rule-upper', regex: /[A-Z]/ },
        number: { id: 'rule-number', regex: /[0-9]/ },
        special: { id: 'rule-special', regex: /[!@#$%^&*(),.?":{}|<>]/ }
    };

    regPassword?.addEventListener('input', function() {
        for (const key in criteria) {
            const item = criteria[key];
            const el = document.getElementById(item.id);
            const icon = el.querySelector('i');
            if (item.regex.test(this.value)) {
                el.classList.add('valid');
                icon.className = "fa-solid fa-circle-check";
            } else {
                el.classList.remove('valid');
                icon.className = "fa-solid fa-circle-xmark";
            }
        }
    });

    // Xử lý đăng nhập
    document.getElementById('login-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const pass = this.querySelector('input[type="password"]').value;

        try {
            const res = await fetch('/data/users.json');
            const data = await res.json();
            const temp = JSON.parse(localStorage.getItem('tempUsers')) || [];
            const user = [...data.users, ...temp].find(u => u.email === email && u.password === pass);

            if (user) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('currentUser', JSON.stringify(user));
                alert(`Chào mừng sếp ${user.name} trở lại!`);
                window.location.href = "/";
            } else {
                alert("Sai tài khoản hoặc mật khẩu rồi sếp ơi!");
            }
        } catch (err) { 
            alert("Lỗi tải data! Sếp kiểm tra xem đã vứt thư mục 'data' (chứa users.json) vào trong thư mục 'public' chưa nhé.");
            console.error(err);
        }
    });

    // Xử lý đăng ký
    document.getElementById('register-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const validCount = document.querySelectorAll('.password-criteria li.valid').length;
        if (validCount < 4) { 
            alert("Mật khẩu chưa đủ an toàn sếp nhé!"); 
            return; 
        }
        
        const newUser = {
            name: this.querySelector('input[type="text"]').value,
            email: this.querySelector('input[type="email"]').value,
            password: regPassword.value,
            role: "Student"
        };
        
        let temp = JSON.parse(localStorage.getItem('tempUsers')) || [];
        temp.push(newUser);
        localStorage.setItem('tempUsers', JSON.stringify(temp));
        alert("Đăng ký thành công! Mời sếp đăng nhập.");
        toggleForm(false);
    });
    </script>
</body>
</html>