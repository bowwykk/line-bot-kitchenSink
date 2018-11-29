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

namespace LINE\LINEBot\KitchenSink;

class Setting
{
    public static function getSetting()
    {
        return [
            'settings' => [
                'displayErrorDetails' => true, // set to false in production

                'logger' => [
                    'name' => 'slim-app',
                    // 'path' => __DIR__ . '/../../../logs/app.log',
                    'path' => 'php://stderr'
                ],

                'bot' => [
                    'channelToken' => 'xYWwce1Yov/D+hm3t44Kh2O56s+uoLtWJI6cp9pC/EU1Iw8JwXEoKW0ojBSOh4QRm9tvvd1bWAE8gV4pxQeB234RqRFsQGNBVUXo/OyS7grsUP0nUiNVpVzS+AEFJnE74HvEjG7jek6stse12PAztwdB04t89/1O/w1cDnyilFU=',
                    'channelSecret' => '18c44ec2d7cbaeef30ffe0f331a33f2b',
                ],

                'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
            ],
        ];
    }
}
