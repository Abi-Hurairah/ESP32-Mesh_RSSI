#include <WiFi.h> //Wifi library
#include "esp_wpa2.h" //wpa2 library for connections to Enterprise networks
#include <HTTPClient.h>
#define EAP_IDENTITY "abihurairah@student.ub.ac.id" //if connecting from another corporation, use identity@organisation.domain in Eduroam 
#define EAP_USERNAME "abihurairah@student.ub.ac.id" //oftentimes just a repeat of the identity
#define EAP_PASSWORD "rytbas-3vysde-Dacpeg" //your Eduroam password
const char* ssid = "WiFi-UB.x"; // Eduroam SSID
const char* host = "10.255.50.6"; //external server domain for HTTP connection after authentification
int counter = 0;

#include <painlessMesh.h>
#ifdef LED_BUILTIN
#define LED LED_BUILTIN
#else
#define LED 2
#endif

#define   BLINK_PERIOD    3000 // milliseconds until cycle repeat
#define   BLINK_DURATION  100  // milliseconds LED is on for

#define   MESH_SSID       "whateverYouLike"
#define   MESH_PASSWORD   "somethingSneaky"
#define   MESH_PORT       5555

// Prototypes
void sendMessage(); 
void receivedCallback(uint32_t from, String & msg);
void newConnectionCallback(uint32_t nodeId);
void changedConnectionCallback(); 
void nodeTimeAdjustedCallback(int32_t offset); 
void delayReceivedCallback(uint32_t from, int32_t delay);

Scheduler     userScheduler; // to control your personal task
painlessMesh  mesh;

bool calc_delay = false;
SimpleList<uint32_t> nodes;

void sendMessage() ; // Prototype
Task taskSendMessage( TASK_SECOND * 1, TASK_FOREVER, &sendMessage ); // start with a one second interval

// Task to blink the number of nodes
Task blinkNoNodes;
bool onFlag = false;

long lastDoTime = 0;

// Keep track of time
long timer1=millis(), timer2;
int biggest_NodeSize=0;

int n;
String RSSI_Network[64];
String Received_RSSI_Network[2048];
String get_sec;
int Received_Amount=0;

bool WiFi_Setup=true;

void doThisAtEvery(int ms){
  if( millis() - lastDoTime >= ms ){
      Serial.println("RESTARTING");
      ESP.restart();
  }
}

void setup() {
  Serial.begin(115200);

  pinMode(LED, OUTPUT);

  // Set WiFi to station mode and disconnect from an AP if it was previously connected.
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);

  Serial.println("Scan start");
 
  // WiFi.scanNetworks will return the number of networks found.
  n = WiFi.scanNetworks();
  Serial.println("Scan done");


  if (n == 0) {
      Serial.println("no networks found");
  } else {
      Serial.print(n);
      Serial.println(" networks found");
      Serial.println("Nr | SSID                             | RSSI | CH | Encryption");
      for (int i = 0; i < n; ++i) {
          // Print SSID and RSSI for each network found
          //RSSI_Network[i]= String(i + 1) + " | " + String(WiFi.SSID(i).c_str()) + " | " + String(WiFi.channel(i)) + " | ";
          /*
          Serial.printf("%2d",i + 1);
          Serial.print(" | ");
          Serial.printf("%-32.32s", WiFi.SSID(i).c_str());
          Serial.print(" | ");
          Serial.printf("%4d", WiFi.RSSI(i));
          Serial.print(" | ");
          Serial.printf("%2d", WiFi.channel(i));
          Serial.print(" | ") ;
          */
          Serial.println(RSSI_Network[i]);
          switch (WiFi.encryptionType(i))
          {
            case WIFI_AUTH_OPEN:
                get_sec = "OPEN";
                break;
            case WIFI_AUTH_WEP:
                get_sec = "WEP";
                break;
            case WIFI_AUTH_WPA_PSK:
                get_sec = "WPA";
                break;
            case WIFI_AUTH_WPA2_PSK:
                get_sec = "WPA2";
                break;
            case WIFI_AUTH_WPA_WPA2_PSK:
                get_sec = "WPA WPA2";
                break;
            case WIFI_AUTH_WPA2_ENTERPRISE:
                get_sec = "WPA-EAP";
                break;
            case WIFI_AUTH_WPA3_PSK:
                get_sec = "WPA3";
                break;
            case WIFI_AUTH_WPA2_WPA3_PSK:
                get_sec = "WPA2 WPA3";
                break;
            case WIFI_AUTH_WAPI_PSK:
                get_sec = "WAPI";
                break;
            default:
                get_sec = "???";
          }

          RSSI_Network[i] = (String)"ssid=" + WiFi.SSID(i) + "&rssi=" + WiFi.RSSI(i) + "&sec=" + get_sec + "&mac=" + WiFi.macAddress();

          Serial.println(RSSI_Network[i]);
          Serial.println();
          delay(10);
      }
  }
  Serial.println("");

  // Delete the scan result to free memory for code below.
  WiFi.scanDelete();

  mesh.setDebugMsgTypes(ERROR | DEBUG);  // set before init() so that you can see error messages

  mesh.init(MESH_SSID, MESH_PASSWORD, &userScheduler, MESH_PORT);
  mesh.onReceive(&receivedCallback);
  mesh.onNewConnection(&newConnectionCallback);
  mesh.onChangedConnections(&changedConnectionCallback);
  mesh.onNodeTimeAdjusted(&nodeTimeAdjustedCallback);
  mesh.onNodeDelayReceived(&delayReceivedCallback);

  userScheduler.addTask( taskSendMessage );
  taskSendMessage.enable();

  blinkNoNodes.set(BLINK_PERIOD, (mesh.getNodeList().size() + 1) * 2, []() {
      // If on, switch off, else switch on
      if (onFlag)
        onFlag = false;
      else
        onFlag = true;
      blinkNoNodes.delay(BLINK_DURATION);

      if (blinkNoNodes.isLastIteration()) {
        // Finished blinking. Reset task for next run 
        // blink number of nodes (including this node) times
        blinkNoNodes.setIterations((mesh.getNodeList().size() + 1) * 2);
        // Calculate delay based on current mesh time and BLINK_PERIOD
        // This results in blinks between nodes being synced
        blinkNoNodes.enableDelayed(BLINK_PERIOD - 
            (mesh.getNodeTime() % (BLINK_PERIOD*1000))/1000);
      }
  });
  userScheduler.addTask(blinkNoNodes);
  blinkNoNodes.enable();

  randomSeed(analogRead(A0));
}

void loop() {
  mesh.update();
  digitalWrite(LED, !onFlag);

  if (nodes.size()>biggest_NodeSize){
    biggest_NodeSize = nodes.size();
    timer1 = millis();
  }

  if(millis() - timer1 >= 60000 ){
      Serial.println("RESTARTING");
      Serial.println(millis() - timer1);
      if(millis() - timer1 >= 80000 ){
        ESP.restart();
      }
      if (WiFi_Setup==true){
        WiFi_Setup=false;
        mesh.stop();
        Serial.println();
        Serial.print("Connecting to network: ");
        Serial.println(ssid);
        WiFi.disconnect(true);  //disconnect form wifi to set new wifi connection
        WiFi.mode(WIFI_STA); //init wifi mode
        
        // Example1 (most common): a cert-file-free eduroam with PEAP (or TTLS)
        WiFi.begin(ssid, WPA2_AUTH_PEAP, EAP_IDENTITY, EAP_USERNAME, EAP_PASSWORD);

        while (WiFi.status() != WL_CONNECTED) {
          delay(500);
          Serial.print(".");
          counter++;
          if(counter>=60){ //after 30 seconds timeout - reset board
            ESP.restart();
          }
        }
        Serial.println("");
        Serial.println("WiFi connected");
        Serial.println("IP address set: "); 
        Serial.println(WiFi.localIP()); //print LAN IP
      }
      
      int arraySize = sizeof(Received_RSSI_Network) / sizeof(Received_RSSI_Network[0]);
      removeDuplicates(Received_RSSI_Network, arraySize);

      // Print array after removing duplicates
      Serial.println("Array after removing duplicates:");
      for (int i = 0; i < arraySize; i++) {
        if (Received_RSSI_Network[i].length() > 0) {
          Serial.println(Received_RSSI_Network[i]);
        }
      }

      arraySize = sizeof(RSSI_Network) / sizeof(RSSI_Network[0]);
      Serial.println("ESP32 Local Sensor Readings:");
      for (int i = 0; i < arraySize; i++){
        if (RSSI_Network[i].length() > 0){
          Serial.println(RSSI_Network[i]);
        }
      }

      if (WiFi.status() == WL_CONNECTED) { //if we are connected to Eduroam network
        counter = 0; //reset counter
        Serial.println("Wifi is still connected with IP: "); 
        Serial.println(WiFi.localIP());   //inform user about his IP address
      }else if (WiFi.status() != WL_CONNECTED) { //if we lost connection, retry
        WiFi.begin(ssid);      
      }
      while (WiFi.status() != WL_CONNECTED) { //during lost connection, print dots
        delay(500);
        Serial.print(".");
        counter++;
        if(counter>=60){ //30 seconds timeout - reset board
          ESP.restart();
        }
      }
      Serial.print("Connecting to website: ");
      Serial.println(host);
      WiFiClient client;
      if (client.connect(host, 80)) {
        
        /*
        arraySize = sizeof(RSSI_Network) / sizeof(RSSI_Network[0]);
        Serial.println("ESP32 Local Sensor Readings:");
        for (int i = 0; i < arraySize; i++){
          if (RSSI_Network[i].length() > 0){
            Serial.println(RSSI_Network[i]);
          }
        }

        RSSI_Network[i]= WiFi.macAddress() + " | " + String(i + 1) + " | " + WiFi.RSSI(i) + " | " + String(WiFi.SSID(i).c_str()) + " | " + String(WiFi.channel(i)) + " | " + String("open");

        */

        for (int i = 0; i < arraySize; i++){
          if (RSSI_Network[i].length() > 0) {
            Serial.println(RSSI_Network[i]);
            HTTPClient http;
            http.begin("http://10.255.50.6/send_data.php");
            http.addHeader("Content-Type", "application/x-www-form-urlencoded");

            auto httpCode = http.POST(RSSI_Network[i]);
            String payload = http.getString();

            Serial.print(">> "); Serial.println(payload);

            delay(10);
          }


        }
        
        arraySize = sizeof(Received_RSSI_Network) / sizeof(Received_RSSI_Network[0]);
        for (int i = 0; i < arraySize; i++){
          if (Received_RSSI_Network[i].length() > 0) {
            Serial.println(Received_RSSI_Network[i]);
            HTTPClient http;
            http.begin("http://10.255.50.6/send_data.php");
            http.addHeader("Content-Type", "application/x-www-form-urlencoded");

            auto httpCode = http.POST(Received_RSSI_Network[i]);
            String payload = http.getString();

            Serial.print(">> "); Serial.println(payload);

            delay(10);
            }
          }

      } else {
          Serial.println("Connection unsucessful");
      }  

      //ESP.restart();
  }
  //doThisAtEvery(12000);

}
void sendMessage() {
  String msg = "Hello from node ";
  msg += mesh.getNodeId();
  msg += " myFreeMemory: " + String(ESP.getFreeHeap());
  for (int i = 0; i < n; ++i)
  {
    mesh.sendBroadcast(RSSI_Network[i]);
  }

  if (calc_delay) {
    SimpleList<uint32_t>::iterator node = nodes.begin();
    while (node != nodes.end()) {
      mesh.startDelayMeas(*node);
      node++;
    }
    calc_delay = false;
  }

  Serial.printf("Sending message: %s\n", msg.c_str());
  
  taskSendMessage.setInterval( random(TASK_SECOND * 1, TASK_SECOND * 5));  // between 1 and 5 seconds
}


void receivedCallback(uint32_t from, String & msg) {
  Serial.printf("startHere: Received from %u msg=%s\n", from, msg.c_str());
  Received_RSSI_Network[Received_Amount] =  msg.c_str();
  Received_Amount++;
}

void newConnectionCallback(uint32_t nodeId) {
  // Reset blink task
  onFlag = false;
  blinkNoNodes.setIterations((mesh.getNodeList().size() + 1) * 2);
  blinkNoNodes.enableDelayed(BLINK_PERIOD - (mesh.getNodeTime() % (BLINK_PERIOD*1000))/1000);
 
  Serial.printf("--> startHere: New Connection, nodeId = %u\n", nodeId);
  Serial.printf("--> startHere: New Connection, %s\n", mesh.subConnectionJson(true).c_str());
}

void changedConnectionCallback() {
  Serial.printf("Changed connections\n");
  // Reset blink task
  onFlag = false;
  blinkNoNodes.setIterations((mesh.getNodeList().size() + 1) * 2);
  blinkNoNodes.enableDelayed(BLINK_PERIOD - (mesh.getNodeTime() % (BLINK_PERIOD*1000))/1000);
 
  nodes = mesh.getNodeList();

  Serial.printf("Num nodes: %d\n", nodes.size());
  Serial.printf("Connection list:");

  SimpleList<uint32_t>::iterator node = nodes.begin();
  while (node != nodes.end()) {
    Serial.printf(" %u", *node);
    node++;
  }
  Serial.println();
  calc_delay = true;
}

void nodeTimeAdjustedCallback(int32_t offset) {
  Serial.printf("Adjusted time %u. Offset = %d\n", mesh.getNodeTime(), offset);
}

void delayReceivedCallback(uint32_t from, int32_t delay) {
  Serial.printf("Delay to node %u is %d us\n", from, delay);
}

void removeDuplicates(String arr[], int &size) {
  for (int i = 0; i < size; i++) {
    if (arr[i].length() == 0) continue; // Skip already removed elements
    for (int j = i + 1; j < size; j++) {
      if (arr[i] == arr[j]) {
        // Shift elements to the left
        for (int k = j; k < size - 1; k++) {
          arr[k] = arr[k + 1];
        }
        arr[size - 1] = ""; // Clear the last element
        size--; // Reduce the array size
        j--; // Adjust the index to check the new element at position j
      }
    }
  }
}
