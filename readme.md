# 📊 TalkToData

> An intelligent, self-hosted conversational analytics engine that transforms raw CSV datasets into actionable business insights through natural conversation.

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire)](https://livewire.laravel.com)
[![Python](https://img.shields.io/badge/Python-3.11+-3776AB?style=flat-square&logo=python)](https://python.org)
[![FastAPI](https://img.shields.io/badge/FastAPI-0.100+-009688?style=flat-square&logo=fastapi)](https://fastapi.tiangolo.com)
[![DuckDB](https://img.shields.io/badge/DuckDB-Latest-FFF000?style=flat-square&logo=duckdb)](https://duckdb.org)

---

## 🌟 Overview

**TalkToData** bridges the gap between complex database querying and decision-makers. By combining **Laravel 13** and **Livewire** for a reactive web control plane alongside a **Python (FastAPI + DuckDB)** analytical engine, TalkToData allows non-technical users to profile datasets, execute fast SQL aggregations, and generate charts simply by chatting in plain English.

---

## 🚀 Key Features

* **⚡ Automated Schema Profiling:** Instantly parses uploaded CSVs to detect data types, record counts, and column metadata.
* **💬 Conversational Querying:** Ask questions in plain English—the AI translates intent into optimized SQL queries executed instantly by DuckDB.
* **📈 Dynamic Visualizations:** Automatically formats query outputs into responsive **Chart.js** bar, line, and pie charts.
* **🏎️ High-Performance Analytics:** Powered by DuckDB, allowing sub-second analytical queries on datasets with tens of thousands of rows.
* **🔒 Self-Hosted & Secure:** Complete control over your data pipeline with isolated session execution and custom guardrails.

---

## 🏗️ Architecture Stack

┌───────────────────────────┐         ┌──────────────────────────┐
│      Laravel 13 Web       │ ◄─────► │   Python FastAPI Server  │
│  (Livewire 3 + Gemini AI) │  Guzzle │    (DuckDB + Pandas)     │
└──────────────┬────────────┘         └────────────┬─────────────┘
│                                   │
▼                                   ▼
Web Client UI                       In-Memory Analytics


* **Frontend / App Control:** Laravel 13, Livewire 3, Tailwind CSS, Alpine.js
* **Intelligence / Agent:** Laravel AI SDK + Gemini API Integration
* **Analytics Backend:** Python (FastAPI), DuckDB, Pandas
* **Data Visualization:** Chart.js

---

## 💡 Example Prompts

Here are sample prompts using an inventory dataset (`stock_date`, `material`, `manufacturer`, `free_stock`, `in_transit_stock`, `quality_inspection_stock`):

### 1. Profiling & Summaries
> *"Summarize this dataset for me. What are the key metrics, total row counts, and main numerical fields available?"*
> *"What is the total `free_stock` available across all materials?"*

### 2. Aggregations & SQL Rankings
> *"Which material has the highest `in_transit_stock` value? Show the top 5 in a clean table."*
> *"Calculate the total available inventory by adding `free_stock` and `in_transit_stock` for each material."*

### 3. Dynamic Charting
> *"Generate a bar chart showing the `free_stock` distribution across all materials."*
> *"Create a comparison chart between `free_stock`, `in_transit_stock`, and `quality_inspection_stock` for material `ABNAM0002`."*

---

## 🛠️ Installation & Setup

### Prerequisites
* PHP >= 8.2 & Composer
* Node.js & NPM
* Python >= 3.10
* API Key, I have used Gemini

### 1. Clone & Configure Laravel Backend

```bash
git clone [https://github.com/kemalyen/TalkToData.git](https://github.com/kemalyen/TalkToData.git)
cd TalkToData

# Install PHP dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Add your Gemini API Key in .env
# GEMINI_API_KEY=your_api_key_here

# Run migrations & link storage
php artisan migrate
php artisan storage:link

# Compile assets
npm install && npm run build
```

### 2. Setup Python Intelligence Microservice

```bash
git clone [https://github.com/kemalyen/TalkToData-python-engine.git](https://github.com/kemalyen/TalkToData-python-engine.git) python-backend
 
cd python-backend

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Start FastAPI server
uvicorn main:app --reload --port 8090
```

### 3. Run the Application

```bash
# In the root directory
php artisan serve
```

Open your browser and navigate to http://localhost:8000. Upload a CSV and start querying your data!

## License
Distributed under the MIT License. See LICENSE for more information.
