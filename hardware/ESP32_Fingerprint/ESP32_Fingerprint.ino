#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Adafruit_Fingerprint.h>
#include <WiFiManager.h>
#include <Preferences.h>
#include "config.h" // Konfigurasi sensitif (Server, API Key, WiFi AP)

// Variables that depend on config.h
String activeServerUrl = ""; // Akan otomatis diisi server mana yang jalan
int lastServerIndex = -1;    // Indeks server terakhir yang sukses
Preferences prefs;


// ==========================================
// KONFIGURASI PIN HARDWARE
// ==========================================
#define RX2_PIN   16
#define TX2_PIN   17
#define LED_MERAH 26
#define LED_HIJAU 25
#define BUZZER    27

// Inisialisasi modul
LiquidCrystal_I2C lcd(0x27, 16, 2);
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

// ==========================================
// VARIABEL STATUS
// ==========================================
unsigned long lastPollingTime = 0;
const unsigned long POLLING_INTERVAL = 3000;

// Mode: 0=standby, 1=verify, 2=enroll, 3=delete
int currentMode = 0;
int previousMode = 0;

// Data event
int currentEventId = 0;

// Data sinkronisasi (enroll / delete)
int currentTargetId = 0; // Menyimpan anggota_id atau fingerprint_id
String syncToken = "";

// Timeout
unsigned long enrollStartTime = 0;
const unsigned long ENROLL_TIMEOUT = 30000;

int pollCount = 0;
bool isSensorFound = false; // Global status sensor


// ==========================================
// FUNGSI BANTU: Parse JSON sederhana
// ==========================================

String getJsonValue(String json, String key) {
  String searchKey = "\"" + key + "\"";
  int keyIndex = json.indexOf(searchKey);
  if (keyIndex == -1) return "";
  
  int colonIndex = json.indexOf(':', keyIndex + searchKey.length());
  if (colonIndex == -1) return "";
  
  int valueStart = colonIndex + 1;
  while (valueStart < json.length() && json.charAt(valueStart) == ' ') valueStart++;
  
  if (json.charAt(valueStart) == '"') {
    int valueEnd = json.indexOf('"', valueStart + 1);
    if (valueEnd == -1) return "";
    return json.substring(valueStart + 1, valueEnd);
  } else {
    int valueEnd = valueStart;
    while (valueEnd < json.length() && json.charAt(valueEnd) != ',' && json.charAt(valueEnd) != '}') {
      valueEnd++;
    }
    String val = json.substring(valueStart, valueEnd);
    val.trim();
    return val;
  }
}


void setup() {
  Serial.begin(9600);
  delay(1000);
  
  Serial.println();
  Serial.println("====================================");
  Serial.println("  SISTEM ABSENSI IoT - GLOBAL DEV");
  Serial.println("====================================");
  
  pinMode(LED_MERAH, OUTPUT);
  pinMode(LED_HIJAU, OUTPUT);
  pinMode(BUZZER, OUTPUT);
  
  tone(BUZZER, 1500, 500);
  delay(600);
  
  Wire.begin();
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Sistem Absensi");
  lcd.setCursor(0, 1);
  lcd.print("Global Booting..");
  
  // ==========================================
  // WIFIMANAGER: Setup WiFi tanpa hardcode
  // ==========================================
  WiFiManager wm;
  
  // Set timeout portal (detik). Setelah 180 detik tanpa konfigurasi, restart
  wm.setConfigPortalTimeout(180);
  
  // Set timeout koneksi ke WiFi lama (detik). 
  // Jika lewat 20 detik gagal, langsung buka Portal Config.
  wm.setConnectTimeout(20); 
  
  // Tampilkan pesan di LCD bahwa portal akan dimulai jika perlu
  lcd.setCursor(0, 1);
  lcd.print("Cari WiFi...    ");
  
  // autoConnect("NAMA_AP", "PASSWORD_AP")
  // Jika gagal konek, WiFiManager akan otomatis membuka Portal Config selama configPortalTimeout
  if (!wm.autoConnect(WM_AP_NAME, WM_AP_PASS)) {
    Serial.println("Gagal connect & Portal Timeout, restart...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi Gagal!");
    lcd.setCursor(0, 1);
    lcd.print("Restart...");
    delay(3000);
    ESP.restart();
  }
  
  // ==========================================
  // DEBUG: Info jaringan lengkap
  // ==========================================
  Serial.println("\n[OK] WiFi connected!");
  Serial.print("[DEBUG] SSID: "); Serial.println(WiFi.SSID());
  Serial.print("[DEBUG] IP: "); Serial.println(WiFi.localIP());
  Serial.print("[DEBUG] Gateway: "); Serial.println(WiFi.gatewayIP());
  Serial.print("[DEBUG] Subnet: "); Serial.println(WiFi.subnetMask());
  Serial.print("[DEBUG] DNS: "); Serial.println(WiFi.dnsIP());
  Serial.print("[DEBUG] RSSI: "); Serial.print(WiFi.RSSI()); Serial.println(" dBm");
  
  lcd.setCursor(0, 1);
  lcd.print("WiFi OK!        ");
  
  // ==========================================
  // Tunggu stack TCP/IP & ARP cache siap
  // ==========================================
  delay(5000);
  
  // Pastikan IP valid (bukan 0.0.0.0)
  int ipRetry = 0;
  while (WiFi.localIP() == IPAddress(0, 0, 0, 0) && ipRetry < 10) {
    delay(500);
    ipRetry++;
    Serial.println("[WAIT] Menunggu IP address... (" + String(ipRetry) + "/10)");
    lcd.setCursor(0, 1);
    lcd.print("Tunggu IP...    ");
  }
  
  if (WiFi.localIP() == IPAddress(0, 0, 0, 0)) {
    Serial.println("[ERROR] Gagal mendapat IP! Restart...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("IP Gagal!");
    lcd.setCursor(0, 1);
    lcd.print("Restart...");
    delay(3000);
    ESP.restart();
  }
  
  Serial.println("[OK] IP Final: " + WiFi.localIP().toString());
  
  Serial.println("[INIT] Mencari sensor AS608...");
  lcd.setCursor(0, 1);
  lcd.print("Cari sensor...  ");
  
  long baudRates[] = {57600, 9600, 115200, 38400, 19200};
  int numBauds = 5;
  
  for (int i = 0; i < numBauds; i++) {
    mySerial.begin(baudRates[i], SERIAL_8N1, RX2_PIN, TX2_PIN);
    finger.begin(baudRates[i]);
    delay(300);
    
    if (finger.verifyPassword()) {
      Serial.println("TERDETEKSI!");
      isSensorFound = true;
      
      // OPTIMASI: Set Security Level ke 2 (lebih lunak/cepat mendeteksi)
      // Level 1-5 (1 paling longgar, 5 paling ketat)
      finger.setSecurityLevel(2); 
      Serial.println("[OK] Security Level diatur ke: 2");

      lcd.setCursor(0, 1);
      lcd.print("Sensor OK!      ");
      delay(500);
      break;
    }
  }
  
  if (!isSensorFound) {
    Serial.println("[WARN] Sensor tidak terdeteksi!");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("SENSOR ERROR!");
    lcd.setCursor(0, 1);
    lcd.print("Cek Kabel / PIN");
    delay(3000);
  }
  
  // ==========================================
  // PENCARIAN SERVER DENGAN MEMORI (PREFERENCES)
  // ==========================================
  Serial.println("[SERVER] Mencari server yang aktif...");
  lcd.clear();
  lcd.print("Mencari Server..");
  
  prefs.begin("iot-absensi", false);
  lastServerIndex = prefs.getInt("srv_idx", -1);
  
  bool serverFound = false;

  // 1. Coba server terakhir yang sukses dulu (FAST BOOT)
  if (lastServerIndex >= 0 && lastServerIndex < NUM_SERVERS) {
    Serial.print("[SPEED] Mencoba server terakhir: ");
    Serial.println(SERVER_URLS[lastServerIndex]);
    
    WiFiClient client;
    HTTPClient http;
    http.begin(client, String(SERVER_URLS[lastServerIndex]) + "?action=mode");
    http.addHeader("X-API-KEY", api_key);
    http.setTimeout(2500); // Timeout lebih pendek untuk fast-check
    int httpCode = http.GET();
    
    if (httpCode == 200) {
      activeServerUrl = String(SERVER_URLS[lastServerIndex]);
      serverFound = true;
      Serial.println(">>> FAST BOOT: Server OK! <<<");
    }
    http.end();
  }

  // 2. Jika gagal atau belum ada memori, cari semua
  if (!serverFound) {
    int maxRetries = 2; // Cukup 2 putaran agar tidak terlalu lama
    for (int retry = 0; retry < maxRetries && !serverFound; retry++) {
      if (retry > 0) {
        lcd.setCursor(0, 1);
        lcd.print("Retry "); lcd.print(retry + 1); lcd.print("...   ");
        delay(1000);
      }
      
      for (int i = 0; i < NUM_SERVERS; i++) {
        // Lewati yang sudah dicoba di tahap fast boot jika gagal
        if (retry == 0 && i == lastServerIndex) continue;

        WiFiClient client;
        HTTPClient http;
        String testUrl = String(SERVER_URLS[i]) + "?action=mode";
        Serial.print("Mencoba: "); Serial.println(testUrl);
        
        http.begin(client, testUrl);
        http.addHeader("X-API-KEY", api_key);
        http.setTimeout(2000); // 2 Detik saja untuk pencarian awal
        int httpCode = http.GET();
        
        if (httpCode == 200) {
          activeServerUrl = String(SERVER_URLS[i]);
          serverFound = true;
          lastServerIndex = i;
          prefs.putInt("srv_idx", i); // Simpan ke memori permanen
          Serial.println(">>> SERVER BARU DITEMUKAN: " + activeServerUrl + " <<<");
          http.end();
          break;
        }
        http.end();
      }
    }
  }

  if (!serverFound) {
    Serial.println("[ERROR] Tidak ada server merespon!");
    lcd.clear();
    lcd.print("Server Tdk Ada!");
    lcd.setCursor(0, 1);
    lcd.print("Cek WiFi/Server");
    delay(3000);
  }
  prefs.end();

  lcd.clear();
  showStandbyLCD();
  
  Serial.println("====================================");
  Serial.println("  SETUP SELESAI - MULAI POLLING");
  Serial.println("====================================");
  
  checkServerMode();
  lastPollingTime = millis();
}


void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[WARN] WiFi terputus!");
    lcd.clear();
    lcd.print("WiFi Terputus!");
    delay(3000);
    return;
  }

  if (millis() - lastPollingTime > POLLING_INTERVAL) {
    checkServerMode();
    lastPollingTime = millis();
  }

  // === LOGIKA MODE (Hanya jalan jika Sensor OK) ===
  if (!isSensorFound && currentMode != 0) {
    // Jika sensor tidak ada tapi server minta mode tertentu
    lcd.setCursor(0, 0);
    lcd.print("ERROR: NO SENSOR");
    lcd.setCursor(0, 1);
    lcd.print("Check Wiring!!  ");
    delay(1000);
    return;
  }

  if (currentMode == 3 && currentTargetId > 0) {
    // Mode DELETE
    executeDeletion();
    currentMode = previousMode;
    currentTargetId = 0;
    syncToken = "";
    updateLCD();
  }
  else if (currentMode == 2 && currentTargetId > 0) {
    // Mode ENROLL
    enrollStartTime = millis();
    executeEnrollment();
    currentMode = previousMode;
    currentTargetId = 0;
    syncToken = "";
    updateLCD();
  }
  else if (currentMode == 1) {
    // Mode VERIFY (Standard Attendance) 
    executeVerification();
  }
  
  delay(100);
}


// ==========================================
// HTTP: Check Server Mode
// ==========================================
void checkServerMode() {
  pollCount++;
  
  // Jika server belum ditemukan saat setup, coba cari lagi
  if (activeServerUrl.length() == 0) {
    Serial.println("[WARN] Server URL kosong, mencoba cari ulang...");
    for (int i = 0; i < NUM_SERVERS; i++) {
      WiFiClient client;
      HTTPClient http;
      String testUrl = String(SERVER_URLS[i]) + "?action=mode";
      http.begin(client, testUrl);
      http.addHeader("X-API-KEY", api_key);
      http.setTimeout(5000);
      int httpCode = http.GET();
      if (httpCode == 200) {
        activeServerUrl = String(SERVER_URLS[i]);
        Serial.println(">>> SERVER DITEMUKAN (RECOVERY): " + activeServerUrl + " <<<");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Server OK!");
        delay(1000);
        // Refresh LCD ke tampilan mode yang benar
        updateLCD();
        http.end();
        break;
      }
      http.end();
    }
    if (activeServerUrl.length() == 0) return;
  }
  
  WiFiClient client;
  HTTPClient http;
  // Menghilangkan &ukm_id=1
  String url = activeServerUrl + "?action=mode";
  
  Serial.println("--- POLL #" + String(pollCount) + " ---");
  http.begin(client, url);
  http.addHeader("X-API-KEY", api_key);
  http.setTimeout(5000);
  
  int httpCode = http.GET();
  
  if (httpCode < 0) {
    Serial.println("[ERROR] " + http.errorToString(httpCode));
    http.end();
    return;
  }
  if (httpCode != 200) {
    http.end();
    return;
  }
  
  String payload = http.getString();
  http.end();
  
  String newMode = getJsonValue(payload, "mode");
  
  // === TRANSISI MODE ===
  if (newMode == "delete" && currentMode != 3) {
    previousMode = currentMode;
    currentMode = 3;
    
    // Gunakan fingerprint_id dari server untuk hapus slot
    String slotStr = getJsonValue(payload, "fingerprint_id");
    currentTargetId = slotStr.toInt();
    syncToken = getJsonValue(payload, "token");
    
    Serial.println(">>> MODE: DELETE (Slot: " + String(currentTargetId) + ") <<<");
    
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("MENGHAPUS JARI");
    lcd.setCursor(0, 1);
    lcd.print("Slot: "); lcd.print(currentTargetId);
  }
  else if (newMode == "enroll" && currentMode != 2 && currentMode != 3) {
    previousMode = currentMode;
    currentMode = 2;
    
    String targetStr = getJsonValue(payload, "target_id");
    String userType = getJsonValue(payload, "user_type");
    currentTargetId = targetStr.toInt();
    
    syncToken = getJsonValue(payload, "token");
    
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("DAFTAR ANGGOTA");
    lcd.setCursor(0, 1);
    lcd.print("ID: "); lcd.print(currentTargetId);
    beep(1);
  }
  else if (newMode == "verify" && currentMode != 1) {
    currentMode = 1;
    Serial.println(">>> MODE: VERIFY (ABSENSI) <<<");
    showVerifyLCD();
  }
  // Mode verify_backup dihapus untuk penyederhanaan
  else if (newMode == "standby" && currentMode != 0) {
    currentMode = 0;
    Serial.println(">>> MODE: STANDBY <<<");
    showStandbyLCD();
  }
}


// ==========================================
// HTTP: Post Verification Result
// ==========================================
void postVerificationResult(int fingerprintId) {
  WiFiClient client;
  HTTPClient http;
  String url = activeServerUrl + "?action=verify";
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", api_key);
  http.setTimeout(5000);

  // Payload simpel hanya butuh sidik jari, server yang melacak UKM/Event-nya
  String body = "{\"fingerprint_id\":" + String(fingerprintId) + "}";
  
  Serial.println("[HTTP] VERIFY: " + body);

  int httpCode = http.POST(body);
  
  if (httpCode == 200) {
    String payload = http.getString();
    Serial.println("[HTTP] Res: " + payload);
    
    String status = getJsonValue(payload, "status");
    String nama = getJsonValue(payload, "nama");
    String msg = getJsonValue(payload, "message");
    
    lcd.clear();
    if (status == "matched") {
      lcd.setCursor(0, 0);
      lcd.print(msg != "" ? msg.substring(0, 16) : "Terverifikasi");
      lcd.setCursor(0, 1);
      lcd.print(nama.substring(0, 16));
      triggerSuccess();
    } else if (status == "already") {
      lcd.setCursor(0, 0);
      lcd.print("Sudah Absen!");
      lcd.setCursor(0, 1);
      lcd.print(nama.substring(0, 16));
      triggerError();
    } else if (status == "error" || status == "not_found") {
      lcd.setCursor(0, 0);
      lcd.print(msg != "" ? msg.substring(0, 16) : "Ditolak!");
      triggerError();
    } else if (status == "no_event") {
      lcd.setCursor(0, 0);
      lcd.print("Tidak ada jadwal");
      lcd.setCursor(0, 1);
      lcd.print("di UKM Anda!");
      triggerError();
    } else {
      lcd.setCursor(0, 0);
      lcd.print("Bermasalah");
      triggerError();
    }
  } else {
    lcd.clear();
    lcd.print("Server Error");
    triggerError();
  }
  http.end();
  delay(1000); 
  updateLCD();
}


// ==========================================
// HTTP: Post Enrollment/Deletion Result
// ==========================================
void postEnrollmentResult(String token, int fingerprintId) {
  WiFiClient client;
  HTTPClient http;
  String url = activeServerUrl + "?action=register";
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", api_key);
  http.setTimeout(5000);

  String body = "{\"token\":\"" + token + "\",\"fingerprint_id\":" + String(fingerprintId) + "}";
  int httpCode = http.POST(body);
  
  if (httpCode == 200) {
    Serial.println("[OK] Reg server OK");
  }
  http.end();
}

void postDeletionResult(String token, int slot) {
  WiFiClient client;
  HTTPClient http;
  String url = activeServerUrl + "?action=delete";
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", api_key);
  http.setTimeout(5000);

  String body = "{\"token\":\"" + token + "\",\"fingerprint_id\":" + String(slot) + "}";
  int httpCode = http.POST(body);
  
  if (httpCode == 200) {
    Serial.println("[OK] Del server OK");
  }
  http.end();
}


// ==========================================
// FINGERPRINT: Logic
// ==========================================
void executeVerification() {
  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK) return;

  Serial.println("[FINGER] Jari terdeteksi!");

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Gagal membaca");
    triggerError();
    delay(1000);
    showVerifyLCD();
    return;
  }

  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Memverifikasi...");
    Serial.println("[FINGER] Match Found ID: " + String(finger.fingerID) + " (Confidence: " + String(finger.confidence) + ")");
    postVerificationResult(finger.fingerID);
  } else {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Ditolak! (Fail)");
    lcd.setCursor(0, 1);
    lcd.print("Brsihkan Jari/OK"); // Advice
    Serial.println("[FINGER] Tidak ada kecocokan / Kualitas jelek");
    triggerError();
    delay(1500);
    showVerifyLCD();
  }
}

void executeDeletion() {
  int slot = currentTargetId;
  Serial.println("[DELETE] Menghapus slot: " + String(slot));
  
  uint8_t p = finger.deleteModel(slot);
  
  if (p == FINGERPRINT_OK) {
    Serial.println("[DELETE] Sukses sensor!");
    triggerSuccess();
  } else {
    Serial.println("[DELETE] Tdk ada di sensor");
    triggerError();
  }
  // Paksa server setel status kelar apa pun hasilnya dari sensor
  postDeletionResult(syncToken, slot);
  
  // Kembalikan ke standby agar poll berikutnya bisa ganti mode
  currentMode = 0;
  currentTargetId = 0;
  delay(1000);
}

void executeEnrollment() {
  int slot = currentTargetId;
  if (slot > 127) slot = slot % 127 + 1;
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("DAFTAR SIDIK");
  lcd.setCursor(0, 1);
  lcd.print("Tempel jari 1x");

  int p = -1;
  while (p != FINGERPRINT_OK) {
    if (millis() - enrollStartTime > ENROLL_TIMEOUT) {
      lcd.clear();
      lcd.print("Timeout!");
      triggerError();
      delay(2000);
      return;
    }
    p = finger.getImage();
    delay(100);
  }
  
  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) {
    triggerError();
    return;
  }

  lcd.clear();
  lcd.print("OK! Angkat jari");
  delay(2000);
  while (finger.getImage() != FINGERPRINT_NOFINGER) { delay(100); }

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Tempel lagi");
  lcd.setCursor(0, 1);
  lcd.print("untuk konfirm");

  p = -1;
  while (p != FINGERPRINT_OK) {
    if (millis() - enrollStartTime > ENROLL_TIMEOUT) return;
    p = finger.getImage();
    delay(100);
  }

  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) { triggerError(); return; }

  p = finger.createModel();
  if (p != FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Jari tdk cocok");
    triggerError();
    delay(2000);
    return;
  }

  p = finger.storeModel(slot);
  if (p == FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Tersimpan!");
    triggerSuccess();
    postEnrollmentResult(syncToken, slot);
    delay(2000);
  } else {
    lcd.clear();
    lcd.print("Gagal simpan!");
    triggerError();
    delay(2000);
  }

  // Final reset agar loop berikutnya bisa ganti mode dari polling server
  currentMode = 0;
  currentTargetId = 0;
  enrollStartTime = 0;
}


// ==========================================
// LCD DISPLAY
// ==========================================
void updateLCD() {
  if (currentMode == 1) showVerifyLCD();
  else if (currentMode == 4) showBackupAuthLCD();
  else showStandbyLCD();
}

void showStandbyLCD() {
  // Hanya tulis jika konten beneran beda (minimalisir clear)
  lcd.setCursor(0, 0);
  lcd.print("Sistem Absensi  ");
  lcd.setCursor(0, 1);
  lcd.print("Siap Digunakan  ");
}

void showVerifyLCD() {
  lcd.setCursor(0, 0);
  lcd.print("Mode: Absensi   "); // More specific
  lcd.setCursor(0, 1);
  lcd.print("Tempel Jari Anda");
}

void showBackupAuthLCD() {
  lcd.setCursor(0, 0);
  lcd.print("OTORISASI BACKUP");
  lcd.setCursor(0, 1);
  lcd.print("Konfirmasi Admin");
}

// ==========================================
// FEEDBACK: LED & BUZZER
// ==========================================
void triggerSuccess() {
  digitalWrite(LED_HIJAU, HIGH);
  tone(BUZZER, 2000, 150);
  delay(150);
  noTone(BUZZER);
  delay(350);
  digitalWrite(LED_HIJAU, LOW);
}

void triggerError() {
  digitalWrite(LED_MERAH, HIGH);
  tone(BUZZER, 2000, 150);
  delay(150);
  noTone(BUZZER);
  delay(100);
  tone(BUZZER, 2000, 150);
  delay(150);
  noTone(BUZZER);
  delay(100);
  digitalWrite(LED_MERAH, LOW);
}

void beep(int times) {
  for (int i = 0; i < times; i++) {
    tone(BUZZER, 2000, 150);
    delay(200);
  }
}
