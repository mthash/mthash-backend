<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class WalletMigration_102
 */
class WalletMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('wallet', [
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
                        'user_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'address',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'public_key',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'address'
                        ]
                    ),
                    new Column(
                        'private_key',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'public_key'
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 64,
                            'after' => 'private_key'
                        ]
                    ),
                    new Column(
                        'currency',
                        [
                            'type' => Column::TYPE_CHAR,
                            'size' => 5,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'balance',
                        [
                            'type' => Column::TYPE_BIGINTEGER,
                            'default' => "0",
                            'size' => 18,
                            'after' => 'currency'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'balance'
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
                    'AUTO_INCREMENT' => '3',
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
        foreach (\MtHash\Model\Asset\Asset::find() as $asset)
        {
            $address    = \MtHash\Model\Asset\Eth\Address::generate();
            self::$connection->insert (
                'wallet',
                [
                    $asset->id, -1, $address['address'], $address['public_key'], $address['private_key'], $asset->symbol . ' Service Wallet',
                    $asset->symbol, 99999999999999999999
                ],
                [
                    'asset_id', 'user_id', 'address', 'public_key', 'private_key', 'name', 'currency', 'balance',
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
