<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Cây thư mục
Social_Network-main/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   ├── robots.txt
│   └── views/
│       ├── page\auth/
│       │   ├── auth.css
│       │   └── auth.js
│       ├── src/
│       │   ├── components/
│       │   │   ├── feed-filters/    <-- (Thư mục rỗng)
│       │   │   ├── footer/          <-- (Thư mục rỗng)
│       │   │   ├── header/
│       │   │   │   └── header.css
│       │   │   ├── post-composer/   <-- (Thư mục rỗng)
│       │   │   ├── post-item/       <-- (Thư mục rỗng)
│       │   │   ├── sidebar-left/
│       │   │   │   └── sidebar-left.css
│       │   │   ├── sidebar-right/
│       │   │   │   └── sidebar-right.css
│       │   │   └── widgets/
│       │   │       └── right-widget.css
│       │   ├── css/
│       │   │   ├── layout.css
│       │   │   └── modules-shared.css
│       │   ├── js/                  <-- (Thư mục rỗng)
│       │   └── modules/             
│       │       ├── feed/
│       │       │   ├── feed-controller/
│       │       │   │   └── feed-controller.css
│       │       │   ├── page/      
│       │       │   │   ├── tabs/    <-- (Thư mục rỗng)
│       │       │   │   └── page.css
│       │       │   ├── profile/    
│       │       │   │   ├── tabs/    <-- (Thư mục rỗng)
│       │       │   │   └── profile.css
│       │       │   └── search/      <-- (Thư mục rỗng)
│       │       └── widgets/
│       │           ├── calendar/    <-- (Thư mục rỗng)
│       │           ├── events/      <-- (Thư mục rỗng)
│       │           ├── notifications/ <-- (Thư mục rỗng)
│       │           ├── settings/    <-- (Thư mục rỗng)
│       │           └── todo/        <-- (Thư mục rỗng)
│       └── utils/
│           └── loadComponent.js
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── index.blade.php
│       ├── page\auth/
│       │   └── auth.blade.php
│       ├── src/
│       │   ├── components/
│       │   │   ├── feed-filters/
│       │   │   │   └── feed-filters.blade.php
│       │   │   ├── footer/          <-- (Thư mục rỗng)
│       │   │   ├── header/
│       │   │   │   └── header.blade.php
│       │   │   ├── post-composer/
│       │   │   │   └── post-composer.blade.php
│       │   │   ├── post-item/
│       │   │   │   └── post-item.blade.php
│       │   │   ├── sidebar-left/
│       │   │   │   └── sidebar-left.blade.php
│       │   │   ├── sidebar-right/
│       │   │   │   └── sidebar-right.blade.php
│       │   │   └── widgets/
│       │   │       └── right-widget.blade.php
│       │   ├── css/                 <-- (Thư mục rỗng)
│       │   ├── js/                  <-- (Thư mục rỗng)
│       │   └── modules/          
│       │       ├── feed/
│       │       │   ├── feed-controller/
│       │       │   │   └── feed-controller.blade.php
│       │       │   ├── page/        
│       │       │   │   ├── tabs/
│       │       │   │   │   ├── page-all.blade.php
│       │       │   │   │   ├── page-data.blade.php
│       │       │   │   │   ├── page-posts.blade.php
│       │       │   │   │   ├── page-roles.blade.php
│       │       │   │   │   └── page-subpages.blade.php
│       │       │   │   └── page.blade.php
│       │       │   ├── profile/     
│       │       │   │   ├── tabs/
│       │       │   │   │   ├── profile-all.blade.php
│       │       │   │   │   ├── profile-events.blade.php
│       │       │   │   │   ├── profile-roles.blade.php
│       │       │   │   │   ├── profile-saved.blade.php
│       │       │   │   │   └── profile-schedule.blade.php
│       │       │   │   └── profile.blade.php
│       │       │   └── search/      <-- (Thư mục rỗng)
│       │       └── widgets/
│       │           ├── calendar/
│       │           │   └── calendar.blade.php
│       │           ├── events/
│       │           │   └── events.blade.php
│       │           ├── notifications/
│       │           │   └── notifications.blade.php
│       │           ├── settings/
│       │           │   └── settings.blade.php
│       │           └── todo/
│       │               └── todo.blade.php
│       └── utils/                   <-- (Thư mục rỗng)
├── routes/
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
├── vendor/
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── [README.md](http://readme.md/)
└── vite.config.js