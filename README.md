# Kone CMS 🧩

<img src="/kone-transparent.png" alt="Kone CMS Logo" width="200" />

**Kone** is a custom CMS designed specifically for building and managing **affiliate marketing websites**. Built with Laravel 12, FilamentPHP, Livewire, Tailwind CSS, and FluxUI, it offers an elegant and developer-friendly platform to streamline content management and affiliate link tracking.

---

## 📌 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Environment Setup](#-environment-setup)
- [Running the Application](#-running-the-application)
- [Authentication](#-authentication)
- [Modules Coming Soon](#-modules-coming-soon)
- [License](#-license)

---

## ✨ Features
- Custom admin panel using [FilamentPHP v3](https://filamentphp.com)
- Reactive frontend using [Livewire](https://livewire.laravel.com/)
- Responsive and utility-first design with [Tailwind CSS](https://tailwindcss.com/)
- Beautiful UI components with [FluxUI](https://fluxui.dev/)
- Affiliate link management and cloaking
- SEO-optimized blog and content pages
- Tagging and categorization system
- Multi-role authentication support
- Fast setup with Laravel 12 and Vite

---

## 🛠️ Tech Stack
- [Laravel 12](https://laravel.com/docs/12.x)
- [PHP 8.2+](https://www.php.net/releases/8.2/en.php)
- [MySQL 8+](https://www.mysql.com/)
- [Node.js](https://nodejs.org/en/) & [Vite](https://vitejs.dev/)
- [FilamentPHP](https://filamentphp.com)
- [Livewire](https://livewire.laravel.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [FluxUI](https://fluxui.dev/)

---

## 📦 Installation

### 1. Clone the repository
```bash
git clone https://github.com/your-username/kone-cms.git
cd kone-cms
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Set up environment file
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure your `.env` file
Update the following values in your `.env` file:
```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run migrations
```bash
php artisan migrate
```

### 6. Build frontend assets
```bash
npm run dev
```

---

## 🧪 Environment Setup
Ensure you have the following installed:
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL or PostgreSQL

Set file permissions:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

If you're using Valet, run:
```bash
valet link kone-cms
valet secure kone-cms
```

---

## ▶️ Running the Application
```bash
php artisan serve
```
Visit [http://localhost:8000](http://localhost:8000) to access your app.

Access Filament admin at [http://localhost:8000/admin](http://localhost:8000/admin)

---

## 🔐 Authentication
Kone uses Laravel's built-in authentication. Filament handles the admin panel login. You can customize user roles and permissions in the `UserPolicy` or through gate definitions.

To create an admin user:
```bash
php artisan make:filament-user
```

---

## 🧩 Modules Coming Soon
- Affiliate link cloaking and redirection manager
- Theme marketplace for creators
- Multi-site support
- Advanced analytics dashboard
- REST API for external integrations

---

## 📄 License
Kone CMS is open-source and licensed under the [MIT License](LICENSE).

---

> Built with ❤️ by [Ohene Adjei](https://github.com/oheneadj)

