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
                    'channelToken' => 'wWF7g49KKUSjhn7HG7WO3ewuxFqZtoh2QVdQbbMgJXg4oEQlmFwf8xkvcGXnPqRRqSwqKgoJ++UBHTl0UMl9IS6h8ASCko+pzqVw5V2fTOBvwdJVPN9Dw0Jpy5zGu8VBJosx/7iXLKmRrtoT9ot1BwdB04t89/1O/w1cDnyilFU=',
                    'channelSecret' => '3bdb36e86b3a0592332c6e4063902b6e',
                ],

                'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
            ],
        ];
    }
}
