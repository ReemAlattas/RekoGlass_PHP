<?php
/*
* Copyright (C) 2013 Orbeus Inc.
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

require_once $GLOBALS['REKOGNITION_ROOT'].'Rekognition_API.php';
require_once $GLOBALS['REKOGNITION_ROOT'].'Rekognition_GUI.php';

/**
* API wrapper for Google Glass sdk.
* Typical usage is:
*  <code>
*   $image_parser = new Rekognition_Google_Glass($rekognition_api);
*   $image_parser->Reset($userToken);
*   $image_parser->SendFaceNumTimelineItem($itemId);
*  </code>
*/
   
class Rekognition_Google_Glass {
  private $rekognition_api_;
  private $mirror_services_;
  private $content_type_;
  private $userToken_;
  
  /**
   * @param Rekognition_API $rekognition_api
   */
   
  public function __construct($rekognition_api) {
    $this->rekognition_api_ = $rekognition_api;
  }
  
  /**
   * Get r/w permission of the Google Glass timeline
   * @param string $userToken: Google Glass user Id
   */
   
  public function Reset($userToken) {
    $this->userToken_ = $userToken;
    $access_token = get_credentials($userToken);
    $client = get_google_api_client();
    $client->setAccessToken($access_token);
    $this->mirror_services_ = new Google_MirrorService($client);
  }
  
  /**
   * Get image raw data by item id
   * @param string $item_id: Google Glass item Id
   * @param string $img_raw: Image raw data of callback
   * @return true if successfully get the image, otherwise false
   */
   
  public function GetRawImage($item_id, &$img_raw) {
    $image_item = $this->mirror_services_->timeline->get($item_id);
    $attachments = $image_item->attachments;
    if(count($attachments) <= 0){
      return false;
    }
    $this->content_type_ = $attachments[0]->contentType;
    $image = $this->mirror_services_->timeline_attachments->get($item_id, $attachments[0]->id);
    $img_raw = download_attachment($item_id, $image);
    return true;
  }
  
  /**
   * Post a card to timeline, which labels detected faces in the queried image
   * @param string $item_id: Google Glass item Id
   * @return true if successfully get the image, otherwise false
   */

  public function SendFaceNumTimelineItem($item_id) {
    global $orbgui;
    $timeline_item = new Google_TimelineItem();

    if($this->GetRawImage($item_id, $img_raw)) {
      $parsed_obj = $this->rekognition_api_->GetMetadata($img_raw, Rekognition_API::REQUEST_RAW, Rekognition_API::RETURN_PARSED);
      $orbgui->SetImage($img_raw, Rekognition_GUI::MODE_RAW);
      $orbgui->DrawObjects($parsed_obj->GetFaces());
      $img_raw = $orbgui->GetImage();

      $timeline_item->setText($parsed_obj->GetFacesNum().' faces detected');
      $notification = new Google_NotificationConfig();
      $notification->setLevel("DEFAULT");
      $timeline_item->setNotification($notification);
      insert_timeline_item($this->mirror_services_, $timeline_item,
        $this->content_type_, $img_raw);
      return true;
    }
    else {
      $timeline_item->setText("Cannot find attachment.." . $request);
      insert_timeline_item($this->mirror_services_, $timeline_item,
        null, null);
      return false;
    }
  }
}

$image_parser = new Rekognition_Google_Glass($rekognition);
