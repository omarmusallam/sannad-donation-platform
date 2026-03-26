# Sannad Donation Platform

Sannad is a Laravel-based donation management platform designed to support fundraising campaigns, donor interactions, payment processing, and transparent donation tracking.

The platform helps organizations manage campaigns, receive donations, verify receipts, process payments, and provide an organized administration experience through a role-based dashboard.

---

## Overview

This project was built as a full-featured donation platform with support for campaign management, donor accounts, payment flows, receipt generation, multilingual content, and administrative reporting.

It is designed to handle real donation workflows, including successful payment processing, webhook-based updates, and donation verification.

---

## Key Features

- Fundraising campaign management
- Donor account system
- Public donation flow
- Stripe payment integration
- Stripe webhook handling
- Receipt generation and verification
- PDF receipt support
- Admin dashboard
- Role and permission management
- Manual and crypto donation review support
- Arabic and English language support
- Social login support
- Transparency and reporting pages

---

## Tech Stack

- **Backend:** PHP, Laravel 10
- **Database:** MySQL
- **Frontend:** Blade, Tailwind CSS, JavaScript
- **Authentication:** Laravel auth + donor auth
- **Payments:** Stripe
- **Authorization:** Spatie Laravel Permission
- **Other Integrations:** Social login, PDF generation, webhook processing

---

## Main Modules

### Public Website
- Browse campaigns
- View donation details
- Submit donations
- Access transparency and informational pages

### Donor Portal
- Donor authentication
- Donation history
- Receipt access
- Account-related actions

### Admin Dashboard
- Manage campaigns
- Review donations
- Track payment statuses
- Handle crypto/manual submissions
- Monitor reports and transparency-related data

### Payment & Receipt System
- Stripe checkout/payment flow
- Webhook-based donation confirmation
- Receipt generation after successful payments
- Public verification for issued receipts

---

## Highlighted Technical Areas

This project demonstrates practical experience in:

- Laravel application architecture
- Payment gateway integration
- Webhook handling
- Multi-role systems
- Receipt and PDF generation
- Business workflow implementation
- Multilingual application development
- Admin dashboard development

---

## Installation

```bash
git clone https://github.com/omarmusallam/sannad-donation-platform.git
cd sannad-donation-platform
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
