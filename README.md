# Financial Report Sample PDF Generator

A Laravel 12 application that generates financial reports as PDFs using [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf). It reads JSON data from the `public/financial-reports/` directory, renders Blade templates, and outputs downloadable or inline-streamed PDF documents.

## Reports

| Report                                         | Description                                                              | Source           |
| ---------------------------------------------- | ------------------------------------------------------------------------ | --------------- |
| **AMD Q1 Financial Report**                    | Balance-sheet (`amd-q1.json`) | [kaggle.com](https://www.kaggle.com/datasets/wbqrmgmcia7lhhq/sec-financial-statement-data-in-json?resource=download)     |
| **Statement of Receipts & Expenditures (SRE)** | Financial statement (`blgf-sre.json`)    | [blgf.gov.ph](https://blgf.gov.ph) |

## Tech Stack

- **PHP** ^8.2
- **Laravel** ^12.0
- **barryvdh/laravel-dompdf** ^3.1 — HTML-to-PDF rendering
- **Pest** ^3.8 — testing framework

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm

## Installation

```bash
# Clone the repository
git clone <repo-url> && cd dompdf

composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate

```
