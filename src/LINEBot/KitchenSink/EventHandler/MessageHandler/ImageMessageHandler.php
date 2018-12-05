<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Util\UrlBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class ImageMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var ImageMessage $imageMessage */
    private $imageMessage;

    /**
     * ImageMessageHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param \Slim\Http\Request $req
     * @param ImageMessage $imageMessage
     */
    public function __construct($bot, $logger, \Slim\Http\Request $req, ImageMessage $imageMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->req = $req;
        $this->imageMessage = $imageMessage;
    }

    public function handle()
    {
        // $contentId = $this->imageMessage->getMessageId();
        // $image = $this->bot->getMessageContent($contentId)->getRawBody();
        // $this->logger->info("==image content===");
        // // $this->logger->info($image);
        // $tempFilePath = tempnam($_SERVER['DOCUMENT_ROOT'] . '/static/tmpdir', 'image-');
        // $this->logger->info('==tempFilePath==');
        // $this->logger->info($tempFilePath);
        // unlink($tempFilePath);
        // $filePath = $tempFilePath . '.jpg';
        // $filename = basename($filePath);

        // $fh = fopen($filePath, 'x');
        // fwrite($fh, $image);
        // fclose($fh);

        // $testFile = fopen("testFile.txt", 'w');
        // fwrite($testFile, "hello");
        // fclose($testFile);

        // $replyToken = $this->imageMessage->getReplyToken();

        // $url = UrlBuilder::buildUrl($this->req, ['static', 'tmpdir', $filename]);
        // $this->logger->info('==url==');
        // $this->logger->info($url);
        // $url = "/Users/bow/Code/line-bot-kitchenSink/public/static/buttons/1040.jpg";
        // // NOTE: You should pass the url of small image to `previewImageUrl`.
        // // This sample doesn't treat that.
        // $this->bot->replyMessage($replyToken, new ImageMessageBuilder($url, $url));

        $userId = $this->imageMessage->getUserId();
        $contentId = $this->imageMessage->getMessageId();
        $this->logger->info("==userId==". $userId);
        $response = $this->bot->getMessageContent($contentId);
        if ($response->isSucceeded()) {
            $this->logger->info("==isSucceeded==");
            // คำสั่ง getRawBody() ในกรณีนี้ จะได้ข้อมูลส่งกลับมาเป็น binary 
            // เราสามารถเอาข้อมูลไปบันทึกเป็นไฟล์ได้
            $dataBinary = $response->getRawBody(); // return binary
            // ดึงข้อมูลประเภทของไฟล์ จาก header
            $fileType = $response->getHeader('Content-Type');    
            switch ($fileType){
                case (preg_match('/^image/',$fileType) ? true : false):
                    list($typeFile,$ext) = explode("/",$fileType);
                    $ext = ($ext=='jpeg' || $ext=='jpg')?"jpg":$ext;
                    $fileNameSave = time().".".$ext;
                    break;
                case (preg_match('/^audio/',$fileType) ? true : false):
                    list($typeFile,$ext) = explode("/",$fileType);
                    $fileNameSave = time().".".$ext;                        
                    break;
                case (preg_match('/^video/',$fileType) ? true : false):
                    list($typeFile,$ext) = explode("/",$fileType);
                    $fileNameSave = time().".".$ext;                                
                    break;                                                      
            }
            $this->logger->info("==fileNameSave== ". $fileNameSave);
            $botDataFolder = 'botdata/'; // โฟลเดอร์หลักที่จะบันทึกไฟล์
            $botDataUserFolder = $botDataFolder.$userId; // มีโฟลเดอร์ด้านในเป็น userId อีกขั้น
            if(!file_exists($botDataUserFolder)) { // ตรวจสอบถ้ายังไม่มีให้สร้างโฟลเดอร์ userId
                mkdir($botDataUserFolder, 0777, true);
            }   
            // กำหนด path ของไฟล์ที่จะบันทึก
            $fileFullSavePath = $botDataUserFolder.'/'.$fileNameSave;
            $this->logger->info("==fileFullSavePath== ". $fileFullSavePath);
            file_put_contents($fileFullSavePath,$dataBinary); // ทำการบันทึกไฟล์
            $textReplyMessage = "save success $fileNameSave";
            $this->logger->info("==textReplyMessage== ". $textReplyMessage);
            $this->bot->replyMessage(
                $this->imageMessage->getReplyToken(), $textReplyMessage
            );
        }
    }
}
