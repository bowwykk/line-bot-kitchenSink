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

    private $s3Bucket = "towkay-th";
    private $s3Key = "AKIAIUGX5MJLTSFH43EQ";
    private $s3Secret = "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";
    private $s3Region = "ap-southeast-1";

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
        // Set Amazon S3 Credentials
        $s3 = S3Client::factory(
            array(
                'credentials' => array(
                    'key' => $this->s3Key,
                    'secret' => $this->s3Secret
                ),
                'version' => 'latest',
                'region'  => $this->s3Region
            )
        );

        $contentId = $this->imageMessage->getMessageId();
        $image = $this->bot->getMessageContent($contentId)->getRawBody();
        $this->logger->info("==image content===");
        // $this->logger->info($image);
        $tempFilePath = tempnam($_SERVER['DOCUMENT_ROOT'] . '/static/tmpdir', 'image-');
        $this->logger->info('==tempFilePath==');
        $this->logger->info($tempFilePath);
        unlink($tempFilePath);
        $filePath = $tempFilePath . '.jpg';
        $filename = basename($filePath);

        $fh = fopen($filePath, 'x');
        fwrite($fh, $image);
        fclose($fh);

        $testFile = fopen("testFile.txt", 'w');
        fwrite($testFile, "hello");
        fclose($testFile);

        try {
            $s3->putObject(
                array(
                    'Bucket'=>$this->s3Bucket,
                    'Key' =>  'line/lineImage',
                    'SourceFile' => $tempFilePath,
                    'StorageClass' => 'REDUCED_REDUNDANCY'
                )
            );
            $this->logger->info('==url S#==');
            $this->logger->info($s3['ObjectURL']);
        } catch (S3Exception $e) {
            $this->logger->info($e->getMessage());
        }

        $replyToken = $this->imageMessage->getReplyToken();

        $url = UrlBuilder::buildUrl($this->req, ['static', 'tmpdir', $filename]);
        $this->logger->info('==url==');
        $this->logger->info($url);
        $url = "/Users/bow/Code/line-bot-kitchenSink/public/static/buttons/1040.jpg";
        // NOTE: You should pass the url of small image to `previewImageUrl`.
        // This sample doesn't treat that.
        $this->bot->replyMessage($replyToken, new ImageMessageBuilder($url, $url));
    }
}
