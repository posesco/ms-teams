<?php

require __DIR__ . '/vendor/autoload.php';
require 'elastic.php';
require 'teams.php';

$index      = 'kibana-alert-history-';
$index_file = 'filebeat-*';

for ($i = 1; $i <= 9; $i++) {

    $hits = iterator($index, $client);
    foreach ($hits as $hit) {

        if ($hit['_source']['app']) {

            $response = error_search($index_file, $hit['_source']['app'], $client);
            if ($hit['_source']['groups']) {
                $groups = array_map('trim', explode(',', $hit['_source']['groups']));

                foreach ($groups as $group) {
                    $title  = $hit['_source']['title'];
                    $reason = $hit['_source']['reason'];
                    $url    = $hit['_source']['url'];
                    $payload = $http->post($base_uri . $conectors[$group] . $end_uri, [
                        'json' => [
                            "@type"      => "MessageCard",
                            "@context"   => "http://schema.org/extensions",
                            "themeColor" => "f48e00",
                            "summary"    => $title,
                            "title"      => $title,
                            "sections"   => [
                                [
                                    "facts" => [
                                        [
                                            "name"  => "Reason:",
                                            "value" => $reason
                                        ],
                                        [
                                            "name"  => "Last error:",
                                            "value" => $response
                                        ]
                                    ]
                                ]
                            ],
                            "potentialAction"   => [
                                [
                                    "@type" => "OpenUri",
                                    "name"  => "View Report",
                                    "targets" => [
                                        [
                                            "os"  => "default",
                                            "uri" => $url
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                }
            } 
        } elseif ($hit['_source']['php']) {
            $value    = $hit['_source']['value'];
            $response = slow_search($index_file, $value, $client);
            $count    = count($response['groups']);
            for ($i = 0; $i < $count; $i++) {

                if ($response['groups'][$i]['count'] > 0) {
                    $title = $hit['_source']['title'] . $response['groups'][$i]['app'];
                    $reason = $response['groups'][$i]['count'] . $hit['_source']['reason'];
                    $last_error = $response['groups'][$i]['msg'];
                    $url = $response['groups'][$i]['url'];
                    $playload = $http->post($base_uri . $conectors[$response['groups'][$i]['group']] . $end_uri, [
                        'json' => [
                            "@type"      => "MessageCard",
                            "@context"   => "http://schema.org/extensions",
                            "themeColor" => "f48e00",
                            "summary"    => $title,
                            "title"      => $title,
                            "sections"   => [
                                [
                                    "facts" => [
                                        [
                                            "name"  => "Reason:",
                                            "value" => $reason
                                        ],
                                        [
                                            "name"  => "Last error:",
                                            "value" => $last_error
                                        ]
                                    ]
                                ]
                            ],
                            "potentialAction"   => [
                                [
                                    "@type" => "OpenUri",
                                    "name"  => "View Report",
                                    "targets" => [
                                        [
                                            "os"  => "default",
                                            "uri" => $url
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                }
            }
        } else {
            $response = '';
            if ($hit['_source']['groups']) {
                $groups = array_map('trim', explode(',', $hit['_source']['groups']));
    
                foreach ($groups as $group) {
                    $title   = $hit['_source']['title'];
                    $reason  = $hit['_source']['reason'];
                    $payload = $http->post($base_uri . $conectors[$group] . $end_uri, [
                        'json' => [
                            "@type"      => "MessageCard",
                            "@context"   => "http://schema.org/extensions",
                            "themeColor" => "f40000",
                            "summary"    => $title,
                            "title"      => $title,
                            "sections"   => [
                                [
                                    "facts" => [
                                        [
                                            "name"  => "Reason:",
                                            "value" => $reason
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                }
            }
        }

        $client->update([
            'index' => $index,
            'id'    => $hit['_id'],
            'body'  => [
                'doc' => [
                    'alert' => 'yes'
                ]
            ]
        ]);
    }
    echo "$i \n";
    sleep(5);
}
