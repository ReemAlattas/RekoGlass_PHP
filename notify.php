<?php
/*
 * Copyright (C) 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
//  Author: Tianqiang Liu - tqliu@orbe.us

require_once 'config.php';
require_once 'mirror-client.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_MirrorService.php';
require_once 'util.php';

//Add rekognition API headers
require_once 'rekognition/Rekognition_Google_Glass.php';

if($_SERVER['REQUEST_METHOD'] != "POST") {
 // http_send_status(400);
//  exit();
}

  
// Parse the request body
$request = json_decode(file_get_contents("php://input"), true);
$image_parser->Reset($request['userToken']);
$image_parser->SendFaceNumTimelineItem($request['itemId']);



