# ESP32-Mesh_RSSI
Scan RSSI of Wi-Fi Networks with ESP32 in Mesh Topology

# How it Works
The system has the following workflow: first, the device is powered on using a power source. Then, the device will automatically perform an RSSI scan of various Access Points (APs). Once the RSSI scan is complete, the ESP32 will initiate a mesh network to distribute data to other ESP32 devices. After a while, the device will stop the mesh mode and begin sending data to the server. The server will display data based on the latest data sent from the ESP32.

# System Components
* ESP32 with source code
* Database Server

# Tested Hardware
The program has been uploaded on ESP32 module and has ran successfully. Compatibility with an ESP8266 has not been verified.

## Dependencies

This project uses the `http_parser.c` file, which is based on `src/http/ngx_http_parse.c` from NGINX, originally authored by Igor Sysoev.

### License Information

The `http_parser.c` file includes additional changes licensed under the same terms as NGINX by Joyent, Inc., and other Node contributors. All rights reserved.

### GNU Lesser General Public License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).
