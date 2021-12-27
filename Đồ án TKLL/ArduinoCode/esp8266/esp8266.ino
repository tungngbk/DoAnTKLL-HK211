#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <OneWire.h> 
#include <DallasTemperature.h>
#include <ESP_Mail_Client.h>
#include <ArduinoJson.h>

#define TdsSensorPin A0  // Analog Pin where Tds Sensor is connected
#define TempSensorPin D7 // Digital Pin where Temp Sensor is connected
#define VREF 3.3

#define SMTP_HOST "smtp.gmail.com"  
#define SMTP_PORT 465 

// SENDER EMAIL
#define SENDER_EMAIL "mokute.4801@gmail.com"
#define SENDER_PASSWORD "Charlesmemory333"
// RECIPIENT EMAIL
#define RECIPIENT_EMAIL "tung.nguyen1303@hcmut.edu.vn"

SMTPSession smtp; //SMTP session used for sending
ESP_Mail_Session session; // Declare the session config data
SMTP_Message message; // Declare the message class
/* Variable declaration for temperature threshold for sending email */
bool trigger_Send = true;
float TDS_Threshold_above = 800;
String textMsg; //--> Variable to hold all data that will be sent to email
ESPTimeHelper ETH; //--> ESPTimeHelper declaration. This is used to get time data from the server.


//My WiFi
char ssid[] = "Thai_viettel";                     
char password[] = "thaianh123";
const char* host     = "tdsk19.000webhostapp.com"; 

//====================GLOBAL VARIABLES FOR JSON DATA PROCESSING======================//
  
String path  = "/test/test.json";  
String line;
bool json_ok =0,en=0;
String tung;
String section="message";

const char* serverName = "http://tdsk19.000webhostapp.com/Project/post-esp-data.php";   // Domain name
String apiKeyValue = "HCMUTK19";   // Compatible with server

String sensor1Name = "DS18B20";             // Temp Sensor
String sensor2Name = "SKU SEN0244";   // Tds Sensor
String sensorLocation = "Home";
 
float averageVoltage = 0,tdsValue = 0,ec = 0;
OneWire oneWire(TempSensorPin); 
DallasTemperature sensors(&oneWire);

int countSample = 30;
int countEmail = 30;

bool ledState = 1;
 
void setup() 
{
  pinMode(D4, OUTPUT); 
  digitalWrite(D4, LOW);
  Serial.begin(115200); // Debugging on hardware Serial 0
  pinMode(TdsSensorPin,INPUT);
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  
  ETH.TZ = 7; // Get time data from server --> GMT+7 
  ETH.DST_MN = 0;  // Daylight Saving Time
  ETH.setClock(ETH.TZ, ETH.DST_MN);

  // Setup for sending Email
  /* Enable the debug via Serial port
   * none debug or 0
   * basic debug or 1 */
  smtp.debug(1);
  smtp.callback(smtpCallback);  // Callback function to get sending results
  /* Set the session config */
  session.server.host_name = SMTP_HOST;
  session.server.port = SMTP_PORT;
  session.login.email = SENDER_EMAIL;
  session.login.password = SENDER_PASSWORD;
  /* Set the message headers */
  message.sender.name = "Water Quality Monitoring System";
  message.sender.email = SENDER_EMAIL;
  message.subject = "TDS sensor data report";
  message.addRecipient("Tung Nguyen", RECIPIENT_EMAIL);
  /* The Plain text message character set */
  message.text.charSet = "us-ascii";
  /* The content transfer encoding */
  message.text.transfer_encoding = Content_Transfer_Encoding::enc_7bit;
  /* The message priority */
  message.priority = esp_mail_smtp_priority::esp_mail_smtp_priority_high;
  /* The Delivery Status Notifications */
  message.response.notify = esp_mail_smtp_notify_success | esp_mail_smtp_notify_failure | esp_mail_smtp_notify_delay;
}
 
 
void loop() 
{
    //Check WiFi connection status
    if(WiFi.status()== WL_CONNECTED){
        countSample--;
        
      WiFiClient client;
      HTTPClient http;
    
      // Domain name with URL path or IP address with path
      http.begin(client, serverName);
    
      // Specify content-type header
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      if(countSample <= 0){
    countSample = 30;
    // ===== Calculate TDS, EC, Temperature =====
    sensors.requestTemperatures();
    float temperature = sensors.getTempCByIndex(0); 
  
    averageVoltage = analogRead(TdsSensorPin)*VREF/1024.0; // read the analog value more stable by the median filtering algorithm, and convert to voltage value
    float compensationCoefficient =1.0+0.02*(temperature-25.0); // temperature compensation formula: fFinalResult(25^C) = fFinalResult(current)/(1.0+0.02*(fTP-25.0));
  
    float compensationVolatge=averageVoltage/compensationCoefficient;  //temperature compensation
    tdsValue=(133.42*compensationVolatge*compensationVolatge*compensationVolatge - 255.86*compensationVolatge*compensationVolatge + 857.39*compensationVolatge)*0.5; //convert voltage value to tds value
    ec = compensationVolatge;
    Serial.printf("Date/Time: %02d/%02d/%d %02d:%02d:%02d\n", ETH.getDay(), ETH.getMonth(), ETH.getYear(), ETH.getHour(), ETH.getMin(), ETH.getSec());
    Serial.print(F("TDS:")); Serial.println(tdsValue,0);
    Serial.print(F("EC:")); Serial.println(ec,2);
    Serial.print(F("Temperature:")); Serial.println(temperature,2);  
    Serial.print(F(""));
    // ==========================================
    // ===== Insert Data into Database using HTTP POST Request =====
      // Prepare HTTP POST request data
      String httpRequestData = "api_key=" + apiKeyValue + "&sensor1=" + sensor1Name + "&sensor2=" + sensor2Name
                          + "&location=" + sensorLocation + "&TDS=" + String((int)tdsValue)
                          + "&EC=" + String(ec,2) + "&TEMP=" + String(temperature,2) + "";
    
      // Send HTTP POST request
      int httpResponseCode = http.POST(httpRequestData);
     
      // If you need an HTTP request with a content type: text/plain
      //http.addHeader("Content-Type", "text/plain");
      //int httpResponseCode = http.POST("Hello, World!");
    
      // If you need an HTTP request with a content type: application/json, use the following:
      //http.addHeader("Content-Type", "application/json");
      //int httpResponseCode = http.POST("{\"value1\":\"19\",\"value2\":\"67\",\"value3\":\"78\"}");
        
      if (httpResponseCode>0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
      }
      else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      }
      // Free resources
      http.end();
      //=============== JSON REQUEST DATA ==============
      const int httpPort = 80;
      if (!client.connect(host, httpPort)) {
        Serial.println("Connect to server fail!");  
        return;
      }
       client.print(String("GET ") + path + " HTTP/1.1\r\n" +
      "Host: " + host + "\r\n" + 
      "Connection: keep-alive\r\n\r\n");
       delay(1000);
           while(client.available()){

 //=====================================   
    if(json_ok==false)
    {
      line = client.readStringUntil('\r');
     //Serial.println(line); 
      en=false;
    }
    else
    {  
      line = client.readStringUntil('}'); 
      int find_json = line.indexOf('{');
      
      if(find_json<0) Serial.println("NOT JSON==>SKIP DATA"); 
      else {tung=line+"}"; en=true;}
      json_ok=false;  
    }
//=======================================    
        if (line=="\n") 
        { 
          section="json";
          json_ok=true;
        }
        else 
        {
          section="header";
        }
 
//=========================================        
 
      if(en==true) // If response is json
      {     
         String result = tung.substring(1);
         line="";
              
// ===============Parse JSON===============
      int size = result.length()+1;
      char json[size];
      result.toCharArray(json, size);
      DynamicJsonDocument doc(size);
      DeserializationError error = deserializeJson(doc,json);
      if (error)
      {
        Serial.print("deserializeJson() failed with code ");
        Serial.println(error.c_str());
        break;
      }
      else   
//===========================================================  
      
                if (strcmp(doc["led"], "on") == 0) 
                    {
                  digitalWrite(D4,LOW); 
                  ledState = 1;
                
                    }
                else 
                    {
                  digitalWrite(D4,HIGH);
                  ledState = 0;
                
                    }
                  
  }//end if(en==true)
 
} // end while client_available 



// ====== Condition of sending email messages when the temperature value is above the threshold value ======

  if(ledState == 1 && tdsValue > TDS_Threshold_above){
    if(countEmail<=0){
      countEmail = 30;  
      textMsg = textMsg + "TDS above threshold value: " + String(TDS_Threshold_above) + " ppm" + "\n";
      Serial.print(textMsg);
      Serial.println("Send TDS sensor data via email");
      setTextMsg();
      sendTextMsg();
      textMsg = "";
      }
    countEmail--;
  }
  else {
    countEmail = 0;  
  }
  // =================  
    }
    else {
      Serial.println("WiFi Disconnected");
    }
    // ====================================================
    
  

  
  
//===============
  
}
// ============= void setTextMsg() =========
void setTextMsg() {
  //  Email message to be sent
  textMsg = textMsg + "TDS : " + String(tdsValue) + " ppm"  + "\n";
  message.text.content = textMsg.c_str();
}
// =========================================

// ============= void sendTextMsg() =========
void sendTextMsg() {
  // Set the custom message header
  message.addHeader("Message-ID: <tds.send@gmail.com>");
  
  // Connect to server with the session config
  if (!smtp.connect(&session)) return;

  // Start sending Email and close the session
  if (!MailClient.sendMail(&smtp, &message)) Serial.println("Error sending Email, " + smtp.errorReason());
}
// ========================================

// ============= void smtpCallback(SMTP_Status status) ===========
/* Callback function to get the Email sending status */
void smtpCallback(SMTP_Status status) {
  // Print the current status
  Serial.println(status.info());
  // Print the sending result
  if (status.success()) {
    Serial.println("----------------");
    Serial.printf("Message sent success: %d\n", status.completedCount());
    Serial.printf("Message sent failled: %d\n", status.failedCount());
    Serial.println("----------------\n");
    struct tm dt;

    for (size_t i = 0; i < smtp.sendingResult.size(); i++) {
      /* Get the result item */
      SMTP_Result result = smtp.sendingResult.getItem(i);
      time_t ts = (time_t)result.timestamp;
      localtime_r(&ts, &dt);

      Serial.printf("Message No: %d\n", i + 1);
      Serial.printf("Status: %s\n", result.completed ? "success" : "failed");
      Serial.printf("Date/Time: %02d/%02d/%d %02d:%02d:%02d\n", dt.tm_mday, dt.tm_mon + 1, dt.tm_year + 1900, dt.tm_hour, dt.tm_min, dt.tm_sec);
      Serial.printf("Recipient: %s\n", result.recipients);
      Serial.printf("Subject: %s\n", result.subject);
    }
    Serial.println("----------------\n");
  }
}


 
