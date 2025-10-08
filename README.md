# ESP32-Mesh_RSSI
This project implements a distributed Wi-Fi signal monitoring system using ESP32 devices in a mesh topology, enabling wireless signal mapping and quality visualization through a database-backed web interface.

# Project Overview
WiFi provides easy access to the internet. However, problems can occur causing slow WiFi connections for the users. These can be due to a variety of factors such as signal interference, access point placement, or physical barriers between the transmitter and the user. The ESP32 mesh provides a way to measure Received Signal Strength Indicator (RSSI) in of multiple access points (AP) in various locations and send the data towards a server to be visualized, even in locations where an ESP32 cannot connect to any AP. As long as one of the ESP32s can connect the server, data from all nodes can be stored and collected.

Below is an illustration of the system:
![ESP32_Mesh_Illustration](https://github.com/Abi-Hurairah/ESP32-Mesh_RSSI/blob/main/images/ESP32_Mesh_Illustration.png)

## Architecture Diagram
The system has the following workflow: The ESP32 will automatically perform an RSSI scan of various Access Points (APs). Once the RSSI scan is complete, the ESP32 will initiate a mesh network to distribute data to other ESP32 devices. After a set period, the device will stop the mesh mode and begin sending data to the server. Then, the ESP32 will restart the cycle.  The server will display data based on the latest data sent from the ESP32. 

Below illustrates the state diagram for the nodes:

![Node State Diagram](https://github.com/Abi-Hurairah/ESP32-Mesh_RSSI/blob/main/images/Node_State_Diagram.png)

## System Components
* ESP32 development board with source code
* Database Server running Linux

## Tested Hardware
The program has been uploaded on 3 ESP32 and 5 ESP32 modules and has ran successfully. Compatibility with an ESP8266 is limited to non-enterprise networks. 
This project was developed and tested on **Ubuntu 22.04 LTS**.  

## Limitations
The ESP32 cannot perform an AP scan while running painlessMesh, as both processes rely on the same Wi-Fi hardware resources.
Workaround: The system alternates between scanning and mesh operation based on a timer (millis() function). Setting the interval too short can interrupt proper operation.

# Setup Instructions.
Steps:
1. Install Arduino IDE (>=2.0) and get the ESP32 board list. Then install the required libraries. 
* AsyncTCP@1.1.4
* PainlessMesh@1.5.6
* ArduinoJSON@7.4.2
* TaskScheduler@4.0.0
* HTTPClient@2.2.0
2. Clone this repository and upload the `src/ESP32/source.ino` code to ESP32 devices. Please change the variables based on your use case as per the comments.
3. Install Apache2, PHP, and MySQL server.
4. Place the PHP files in your web server root directory (e.g., /var/www/html/) and configure database settings in db_conn.php to match your MySQL credentials.

## Current Status
- The ESP32 nodes successfully scan Wi-Fi networks and send the collected data to a MySQL database.
- The backend web dashboard is not active due to missing original files.
- Partially reconstructed the integration logic and verified database connectivity.
- Below is an example of successful data transmission and visualization before the original dashboard was lost. 

![Website](https://github.com/Abi-Hurairah/ESP32-Mesh_RSSI/blob/main/images/Website.png)

### Reproducibility
You can reproduce my part of the system by:
1. Setting up a local MySQL database and PHP server based on `src/HTML`.
2. Editing your database credentials in `src/HTML/db_conn.php`.
3. Uploading the ESP32 firmware from this repository to your ESP32 boards.
A database template and instructions are included to help replicate the data transmission functionality.

Your database should be able to store information in this form:

![Example Result](https://github.com/Abi-Hurairah/ESP32-Mesh_RSSI/blob/main/images/Example_Result.png)

## Note on Collaboration
This repository includes both firmware and server-side components for completeness.
The ESP32 firmware, mesh communication logic, and integration with the database were fully developed by Abi-Hurairah.
The original backend visualization (PHP/HTML and SQL structure) was created by kaonkaon and has since been partially reconstructed for documentation and verification purposes.

# License Information

The `http_parser.c` file includes additional changes licensed under the same terms as NGINX by Joyent, Inc., and other Node contributors. All rights reserved.

### GNU Lesser General Public License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).
