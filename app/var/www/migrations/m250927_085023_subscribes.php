<?php

use yii\db\Migration;

class m250927_085023_subscribes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscriptions}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-subscriptions-author_id',
            '{{%subscriptions}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-subscriptions-author_id',
            '{{%subscriptions}}',
            'author_id'
        );

        $this->createIndex(
            'idx-subscriptions-author_phone-unique',
            '{{%subscriptions}}',
            ['author_id', 'phone'],
            true
        );

        $this->createIndex(
            'idx-subscriptions-phone',
            '{{%subscriptions}}',
            'phone'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subscriptions-author_id', '{{%subscriptions}}');
        $this->dropTable('{{%subscriptions}}');
    }
}
