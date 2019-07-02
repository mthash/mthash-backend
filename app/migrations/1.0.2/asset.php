<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class AssetMigration_102
 */
class AssetMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('asset', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'cmc_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'logo_url',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'cmc_id'
                        ]
                    ),
                    new Column(
                        'symbol',
                        [
                            'type' => Column::TYPE_CHAR,
                            'size' => 8,
                            'after' => 'logo_url'
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 32,
                            'after' => 'symbol'
                        ]
                    ),
                    new Column(
                        'mineable',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'size' => 1,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'can_mine',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'size' => 1,
                            'after' => 'mineable'
                        ]
                    ),
                    new Column(
                        'total_hashrate',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'can_mine'
                        ]
                    ),
                    new Column(
                        'hashrate_speed',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'total_hashrate'
                        ]
                    ),
                    new Column(
                        'price_usd',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'default' => "0.00000000",
                            'size' => 8,
                            'scale' => 8,
                            'after' => 'hashrate_speed'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'price_usd'
                        ]
                    ),
                    new Column(
                        'updated_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'created_at'
                        ]
                    ),
                    new Column(
                        'deleted_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'updated_at'
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'size' => 2,
                            'after' => 'deleted_at'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY')
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8mb4_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $assets     =
            [
                'ETH'           => 'Ethereum',
                'BTC'           => 'Bitcoin',
                'LTC'           => 'Litecoin',
                'BCH'           => 'Bitcoin Cash',
                'ADA'           => 'Cardano',
                'TRX'           => 'TRON',
                'XMR'           => 'Monero',
                'DASH'          => 'Dash',
                'ETC'           => 'Ethereum Classic',
            ];

        foreach ($assets as $symbol => $name)
        {
            self::$connection->insert (
                'asset',
                [
                    $name, $symbol, time()
                ],
                [
                    'name', 'symbol', 'created_at'
                ]
            );
        }
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
