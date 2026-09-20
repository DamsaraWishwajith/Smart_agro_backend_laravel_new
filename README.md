# SmartAgro: Intelligent IoT Greenhouse & Farm Automation System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Flutter](https://img.shields.io/badge/Flutter-3.x-02569B?style=for-the-badge&logo=flutter&logoColor=white)](https://flutter.dev)
[![ESP32](https://img.shields.io/badge/ESP32-Microcontroller-000000?style=for-the-badge&logo=espressif&logoColor=white)](https://espressif.com)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

**SmartAgro** is an end-to-end precision agriculture and greenhouse automation ecosystem. It combines an **ESP32 microcontroller subsystem** for sensor polling and relay control, a **Laravel 11 REST API & Filament v3 Admin Panel** for business logic, data persistence, and administrative auditing, and a cross-platform **Flutter Mobile Application** for farmers.

> 📄 **Full Technical Reference:** For complete database migration details, 11-table schema specifications, complete API route directory, hardware circuit pinouts, and system workflows, see [PROJECT_DOCUMENTATION.md](file:///e:/Flutter%20my/Flutter/sm_agro_final_project/sm_agro_laravel/PROJECT_DOCUMENTATION.md).

---

## 🌟 Key Features & Functional Modules

### 📱 1. Mobile Application (Flutter)
* **Real-time Telemetry Dashboard:** Live monitoring of ambient temperature (°C), relative humidity (%), and soil moisture (%) with dynamic gauge cards.
* **Actuator Remote Override:** Direct manual toggling of 4 hardware actuators (Drip Irrigation Pump, High-Pressure Mist Spray Pump, Exhaust Fan, Grow Lights).
* **Dual Operational Modes:** Instant switching between **Automated Threshold Mode** and **Manual Control Mode**.
* **AI Agronomist Chatbot:** Integrated AI assistant powered by Google Gemini for diagnosing crop diseases, fertilizer recommendations, and agronomic support.
* **Irrigation Timer Routine Scheduler:** Custom creation of time-of-day drip watering routines with weekly day-of-week recurrence.
* **QR Code Hardware Pairing:** Fast mobile camera QR scanning to link physical ESP32 enclosures to farmer accounts.
* **Subscription Billing & Slip Upload:** Monthly payment auditing pipeline allowing farmers to capture and submit bank slips for admin verification.
* **PDF Telemetry Reports:** Exportable PDF reports summarizing crop microclimate trends and irrigation history.

### ⚙️ 2. Cloud Backend & Admin Portal (Laravel 11 & Filament v3)
* **Sanctum Token Authentication:** Secure API token authentication for mobile users and ESP32 hardware units.
* **Filament v3 Audit Panel:** Web management portal for superusers to verify pending user accounts, audit uploaded payment receipts, and monitor system analytics.
* **Real-time FCM Notifications:** Firebase Cloud Messaging integration for pushing critical microclimate alerts (e.g. overheating, severe drought) to mobile devices.
* **Live Admin Chat Support:** Real-time bi-directional direct messaging between farmers and administrators with document/image attachment support.

### 🔌 3. IoT Embedded Subsystem (ESP32 Firmware)
* **Sensor Suite:** DHT22 (AM2302) digital temperature & humidity sensor and Analog Soil Moisture probe.
* **4-Channel Relay Actuation:** Optocoupled relays driving high-voltage hardware (Drip Pump, Mist Spray Pump, Exhaust Fan, LED Grow Lights).
* **Asynchronous 3-Second Heartbeat:** Non-blocking HTTP POST sync with `/api/esp32/sync` transmitting telemetry and fetching dynamic backend overrides.
* **Local Safety Fallback:** Automatic switch to local threshold control if network connectivity is interrupted.

---

## 🗄️ Database Architecture (MySQL - `sm_agro`)

The database architecture consists of **11 tables**:
1. `users` - Farmer profiles, hardware assignments, approval states (`pending`, `approved`, `denied`), FCM tokens.
2. `admins` - Superuser credentials for Filament admin portal access.
3. `plants` - Selected crop metadata, cultivation age, daily water volume targets (mL), ideal microclimate thresholds.
4. `farm_conditions` - Time-series environmental sensor telemetry log records.
5. `motors` - Real-time state of hardware relays (`drip_pump`, `mist_pump`, `fan`, `light`).
6. `modes` - System control state (`Auto` vs `Manual`) and mist scheduling parameters.
7. `irrigation_schedules` - Custom recurring drip irrigation watering timers.
8. `payments` - Subscription payment audit log with uploaded bank slip paths and status (`pending`, `approved`, `rejected`).
9. `messages` - Support chat message histories and attachments exchanged between farmers and admins.
10. `esp_notifications` - Historical critical alert log notifications.
11. `device_events` - Hardware system status change logs and diagnostic events.

---

## 🚀 Quick Setup & Installation Guide

### Backend Setup (Laravel)
```bash
cd sm_agro_laravel
composer install
cp .env.example .env
# Configure DB_DATABASE=sm_agro in .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000
```
* Access REST API at `http://localhost:8000/api`
* Access Admin Portal at `http://localhost:8000/admin`

### Mobile App Setup (Flutter)
```bash
cd smart_agro
flutter pub get
# Update base URL in lib/core/ services to server IP address
flutter run
```

### Firmware Setup (ESP32)
1. Open `esp32_smart_agro/esp32_smart_agro.ino` in Arduino IDE.
2. Install `ArduinoJson`, `Adafruit DHT`, and `LiquidCrystal_I2C` libraries.
3. Update Wi-Fi SSID, Password, and API Server IP.
4. Flash firmware to **ESP32 Dev Module**.

---

## 📑 Detailed Documentation

For full architectural diagrams, complete field-by-field database tables, REST API endpoint request/response payloads, and sequence diagrams, refer to [PROJECT_DOCUMENTATION.md](file:///e:/Flutter%20my/Flutter/sm_agro_final_project/sm_agro_laravel/PROJECT_DOCUMENTATION.md).
