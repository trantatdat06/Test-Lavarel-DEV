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
        const res = await fetch('../../data/users.json');
        const data = await res.json();
        const temp = JSON.parse(localStorage.getItem('tempUsers')) || [];
        const user = [...data.users, ...temp].find(u => u.email === email && u.password === pass);

        if (user) {
            sessionStorage.setItem('isLoggedIn', 'true');
            sessionStorage.setItem('currentUser', JSON.stringify(user));
            alert(`Chào mừng sếp ${user.name} trở lại!`);
            window.location.href = "../../index.html";
        } else {
            alert("Sai tài khoản hoặc mật khẩu rồi sếp ơi!");
        }
    } catch (err) { alert("Sếp nhớ chạy bằng Live Server nhé!"); }
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