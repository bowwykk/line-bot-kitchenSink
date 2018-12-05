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

namespace LINE\LINEBot\KitchenSink\EventHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\KitchenSink\EventHandler;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;

class PostbackEventHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var PostbackEvent $postbackEvent */
    private $postbackEvent;

    /**
     * PostbackEventHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param PostbackEvent $postbackEvent
     */
    public function __construct($bot, $logger, PostbackEvent $postbackEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->postbackEvent = $postbackEvent;
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $this->logger->info('Got postback ' . $this->postbackEvent->getPostbackData());
        $data = $this->postbackEvent->getPostbackData();
        switch ($data) {
            case strpos($data, 'model'):
                $userId = $this->textMessage->getUserId();
                $response = $this->bot->getProfile($userId);

                $profile = $response->getJSONDecodedBody();
                $this->bot->replyText(
                    $this->postbackEvent->getReplyToken(),
                    "Your name: " . $profile['displayName'],
                    "Got postback: " . $this->postbackEvent->getPostbackData(),
                    "Please send car's image"
                );
                break;
            case strpos($data, 'make'):
                $this->logger->info("==make==");
                $buttonTemplateBuilder = new ButtonTemplateBuilder(
                    'Model',
                    'Choose your model..',
                    null,
                    [
                        new PostbackTemplateActionBuilder('one', $data . '&model=1', '1'),
                        new PostbackTemplateActionBuilder('two', $data . '&model=2', '2'),
                        new PostbackTemplateActionBuilder('three', $data . '&model=3', '3'),
                        new PostbackTemplateActionBuilder('four', $data . '&model=4', '4'),
                    ]
                );
                $templateMessage = new TemplateMessageBuilder('Button alt text', $buttonTemplateBuilder);
                $this->bot->replyMessage(
                    $this->postbackEvent->getReplyToken(), $templateMessage
                );
                break;
            default:
                $this->bot->replyText(
                    $this->postbackEvent->getReplyToken(),
                    'Got postback ' . $this->postbackEvent->getPostbackData()
                );
                break;
        }
        
    }
}
