<?php

use Elasticsearch\ClientBuilder;
use Elasticsearch\Helper\Iterators\SearchHitIterator;
use Elasticsearch\Helper\Iterators\SearchResponseIterator;

$client = ClientBuilder::create()
    ->setHosts(['https://127.0.0.1:9200'])
    ->setBasicAuthentication('elastic', 'securepassword')
    ->build();

function iterator($index, $client)
{
    $pages = new SearchResponseIterator($client, [
        'scroll'      => '5m',
        'index'       => $index,
        'size'        => 100,
        'body'        => [
            'query' => [
                'match' => [
                    'alert' => 'no'
                ]
            ]
        ]
    ]);
    return new SearchHitIterator($pages);
}

function error_search($index, $app, $client)
{
    $search = [
        'index'       => $index,
        'size'        => 500,
        'body'        => [
            'sort'        => [
                "@timestamp" => [
                    "order"         => "desc",
                    "unmapped_type" => "boolean"
                ]
            ],
            'query' => [
                'bool' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                'match' => [
                                    'event.module' => 'nginx'
                                ]
                            ],
                            'minimum_should_match' => 1
                        ],
                        'bool' => [
                            'should' => [
                                'match' => [
                                    'message' => $app
                                ]
                            ],
                            'minimum_should_match' => 1
                        ]
                    ]
                ]
            ]
        ]
    ];

    $results = $client->search($search);
    return $results['hits']['hits'][0]['_source']['message'];
}

function slow_search($index, $value, $client)
{
    $search = [
        'index'       => $index,
        'size'        => $value,
        'body'        => [
            'sort'        => [
                "@timestamp" => [
                    "order"         => "desc",
                    "unmapped_type" => "boolean"
                ]
            ],
            'query' => [
                'bool' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                'match' => [
                                    'php' => 'true'
                                ]
                            ],
                            'minimum_should_match' => 1
                        ],
                        'bool' => [
                            'should' => [
                                'match' => [
                                    'message' => 'slow'
                                ]
                            ],
                            'minimum_should_match' => 1
                        ]
                    ]
                ]
            ]
        ]
    ];

    $results = $client->search($search);
    return filter_app($results['hits']['hits']);
}

function filter_app($response)
{
    $count         = count($response);
    $count_app1   = 0;
    $count_app2 = 0;
    $count_app3    = 0;
    $msg_app1     = '';
    $msg_app2   = '';
    $msg_app3      = '';
    for ($i = 0; $i <= $count; $i++) {
        $app1 = strpos($response[$i]['_source']['message'], 'app1');
        $app2 = strpos($response[$i]['_source']['message'], 'app2');
        if ($app1) {
            $count_app1++;
            $msg_app1 = $response[$i]['_source']['message'];
        } elseif ($app2) {
            $count_app2++;
            $msg_app2 = $response[$i]['_source']['message'];
        }
    }
    if ($count != ($count_app1+$count_app2) ) {
        $count_app3 = $count_app1+$count_app2-$count;
        $msg_app3   = 'Slow php requests were detected that do not correspond to App1 or App2 applications.';
    }

    return array(

        'groups' => array(
            0 => array(
                'group' => 'channel_1',
                'app'   => 'App1',
                'count' => $count_app1,
                'msg'   => $msg_app1,
                'url'   => 'https://127.0.0.1:5601/goto/fa349300-8830-11ec-99aa-a1af5924c431'
            ),
            1 => array(
                'group' => 'channel_2',
                'app'   => 'App2',
                'count' => $count_app2,
                'msg'   => $msg_app2,
                'url'   => 'https://127.0.0.1:5601/goto/3263d060-8831-11ec-99aa-a1af5924c431'
            ),
            2 => array(
                'group' => 'channel_3',
                'app'   => 'Other APP',
                'count' => $count_app3,
                'msg'   => $msg_app3,
                'url'   => 'https://127.0.0.1:5601/goto/b9843220-82db-11ec-9844-df8c3c5ca50c'
            )
        )
    );
}
