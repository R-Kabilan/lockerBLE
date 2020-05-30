#include <BLEDevice.h>
#include <BLEUtils.h>
#include <BLEServer.h> //Library to use BLE as server
#include <BLE2902.h>


BLECharacteristic *pCharacteristic;
BLEDescriptor WriteDescriptor(BLEUUID((uint16_t)0x2901));
bool _BLEClientConnected = false;
bool _MsgRecieved;
int tvalue = 0;
String pass = "1234";
bool lock = false;

#define SERVICE_UUID "6E400001-B5A3-F393-E0A9-E50E24DCCA9E"         //6e400001-b5a3-f3 93-e0a9-e50e24dcca9e
#define CHARACTERISTIC_UUID_T "6E400003-B5A3-F393-E0A9-E50E24DCCA9E"//6e400003-b5a3-f393-e0a9-e50e24dcca9e
#define CHARACTERISTIC_UUID_R "6E400002-B5A3-F393-E0A9-E50E24DCCA9E"//6e400002-b5a3-f393-e0a9-e50e24dcca9e

class MyServerCallbacks : public BLEServerCallbacks {
    void onConnect(BLEServer* pServer) {
      _BLEClientConnected = true;
    };
    void onDisconnect(BLEServer* pServer) {
      _BLEClientConnected = false;
    }
};

class MyCallbacks: public BLECharacteristicCallbacks {
    void onWrite(BLECharacteristic *pCharacteristic) {
      std::string rValue = pCharacteristic->getValue();
      int len = rValue.length();
      if (len > 0) {
        _MsgRecieved = true;
        for (int i = 0; i < len; i++) {
          if (len == pass.length()) {
            if (rValue[i] == pass[i]) {
              lock = true;
            }
            else {
              lock = false;
              break;
            }
          }
          else {
            break;
          }
        }
        Serial.println("msg recieved");
        //use a for loop if you have to iterate through the rvalue
      }
    }
};

void Bleinit() {
  BLEDevice::init("locker");

  BLEServer *pServer = BLEDevice::createServer();
  pServer->setCallbacks(new MyServerCallbacks());

  BLEService *pService = pServer->createService(SERVICE_UUID);
  pCharacteristic = pService->createCharacteristic(CHARACTERISTIC_UUID_T, BLECharacteristic::PROPERTY_NOTIFY);
  pCharacteristic->addDescriptor(new BLE2902());

  BLECharacteristic *pCharacteristic = pService->createCharacteristic(CHARACTERISTIC_UUID_R, BLECharacteristic::PROPERTY_WRITE|BLECharacteristic::PROPERTY_NOTIFY);
//  WriteDescriptor.setValue("asd");
  pCharacteristic->addDescriptor(&WriteDescriptor);
  
  pCharacteristic->setCallbacks(new MyCallbacks());
  pService->start();
  pServer->getAdvertising()->start();

}

void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  pinMode(2, OUTPUT);
  digitalWrite(2, LOW);
  Bleinit();

}

void loop() {
  if (_BLEClientConnected) {
    char tstring[8];
    pCharacteristic->setValue("locker empty");
    pCharacteristic->notify();
    delay(500);
  }
  if (lock) {
    Serial.println("open");
    digitalWrite(2, HIGH);
    delay(15000);
    digitalWrite(2, LOW);
    lock = false;
  }
}
