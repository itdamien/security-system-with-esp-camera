#include<Arduino.h>
#include "esp_camera.h"
#include<WiFi.h>
#include<HTTPClient.h>
#include<HardwareSerial.h>

HardwareSerial gsm(1);

const char* ssid="";
const char* password="";

#define trig_pin 12
#define echo_pin 13
#define led_pin 14
#define green_pin 15
#define buzzer 2

#define GSM_RX 3
#define GSM_TX 1

bool AlertSent = false;

#define PWDN_GPIO_NUM 32
#define RESET_GPIO_NUM -1
#define XCLK_GPIO_NUM 0
#define SIOD_GPIO_NUM 26
#define SIOC_GPIO_NUM 27
#define Y9_GPIO_NUM 35
#define Y8_GPIO_NUM 34
#define Y7_GPIO_NUM 39
#define Y6_GPIO_NUM 36
#define Y5_GPIO_NUM 21
#define Y4_GPIO_NUM 19
#define Y3_GPIO_NUM 18
#define Y2_GPIO_NUM 5
#define VSYNC_GPIO_NUM 25
#define HREF_GPIO_NUM 23
#define PCLK_GPIO_NUM 22

void setupCamera(){
    camera_config_t config;

    config.ledc_channel = LEDC_CHANNEL_0;
    config.ledc_timer = LEDC_TIMER_0;

    config.pin_d0 = Y2_GPIO_NUM;
    config.pin_d1 = Y3_GPIO_NUM;
    config.pin_d2 = Y4_GPIO_NUM;
    config.pin_d3 = Y5_GPIO_NUM;
    config.pin_d4 = Y6_GPIO_NUM;
    config.pin_d5 = Y7_GPIO_NUM;
    config.pin_d6 = Y8_GPIO_NUM;
    config.pin_d7 = Y9_GPIO_NUM;
    config.pin_xclk = XCLK_GPIO_NUM;
    config.pin_pclk = PCLK_GPIO_NUM;
    config.pin_vsync = VSYNC_GPIO_NUM;
    config.pin_href = HREF_GPIO_NUM;

    config.pin_sscb_sda = SIOD_GPIO_NUM;
    config.pin_sscb_scl = SIOC_GPIO_NUM;
    config.pin_pwdn = PWDN_GPIO_NUM;
    config.pin_reset = RESET_GPIO_NUM;
    config.xclk_freq_hz = 20000000;
    config.pixel_format = PIXFORMAT_JPEG;

    config.frame_size = FRAMESIZE_SVGA;
    config.jpeg_quality = 10;
    config.fb_count = 2;

    esp_err_t err = esp_camera_init(&config);

    if(err != ESP_OK){
    Serial.println("Camera init failed");
    return;
    }

}

void setup(){
    Serial.begin(115200);
    gsm.begin(9600, SERIAL_8N1, GSM_RX, GSM_TX);

    pinMode(trig_pin, OUTPUT);
    pinMode(echo_pin, INPUT);

    pinMode(led_pin, OUTPUT);
    pinMode(green_pin, OUTPUT);
    pinMode(buzzer, OUTPUT);

    WiFi.begin(ssid,password);

    while(WiFi.status() != WL_CONNECTED){
        Serial.println("Connecting to WiFi...");
        delay(500);
    }
    setupCamera();
}

void loop(){
    int distance = getDistance();

    Serial.print("Distance:  ");
    Serial.println(distance);

    if( distance <= 10){
        digitalWrite(led_pin, HIGH);
        digitalWrite(green_pin, LOW);
        digitalWrite(buzzer, HIGH);

        sendData(distance, "ON");

        if(!AlertSent){
            AlertSent = true;
            sendSMS();
            makeCall();
            captureAndUploadPhoto();
            sendEmail();

        }

    }
    else{
        digitalWrite(led_pin, LOW);
        digitalWrite(green_pin, HIGH);
        digitalWrite(buzzer, LOW);

        sendData(distance, "OFF");
        AlertSent = false;
    
    }
    delay(1000);

}

int getDistance(){
    digitalWrite(trig_pin, LOW);
    delayMicroseconds(2);

    digitalWrite(trig_pin, HIGH);
    delayMicroseconds(10);

    digitalWrite(trig_pin, LOW);

    long duration = pulseIn(echo_pin,HIGH);
    int distance = duration * 0.0343 / 2;
    return distance;
}

void sendData(int distance, String status){

    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;
        String url = "http://192.168.1.1/high_security/insert.php?distance=" + String(distance) + "&led_status=" + status;
        http.begin(url);
        http.GET();
        http.end();

    }
}

void captureAndUploadPhoto(){
    camera_fb_t *fb = esp_camera_fb_get();

    if(!fb){
        Serial.println("Camera capture failed");
        return;
    }

    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;
        
        String url = "http://192.168.1.1/high_security/upload.php";

        http.begin();

        http.addHeader("Content-Type", "image/jpeg");

        http.POST(fb->buf, fb->len);
        http.end();

        
    }
    esp_camera_fb_return(fb);
}

void sendSMS(){
    gsm.println("AT+CMGF=1");
    delay(1000);

    gsm.println("AT+CMGS=\"+250795884333\"");
    delay(1000);

    gsm.println("ALERT! Intruder Detected.");
    delay(1000);

    gsm.write(26);

    delay(2000);

}

void makeCall(){
    gsm.println("ATD+250795884333;");
    delay(1500);

    gsm.println("ATH");
}

void sendEmail(){
    if(WiFi.status() == WL_CONNECTED){
        HTTPClient http;
        http.begin("http://192.168.1.1/high_security/email.php");
        http.GET();
        http.end();

    }
}