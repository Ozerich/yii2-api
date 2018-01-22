<?php

use yii\db\Migration;

/**
 * Handles the creation of table `images`.
 * Has foreign keys to the tables:
 *
 * - `users`
 */
class m180120_135533_create_images_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(32)->notNull(),
            'ext' => $this->string(3)->notNull(),
            'size' => $this->integer()->notNull(),
            'mime' => $this->string()->notNull(),
            'width' => $this->integer()->notNull(),
            'height' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-images-user_id',
            '{{%images}}',
            'user_id'
        );

        try {
            // add foreign key for table `users`
            $this->addForeignKey(
                'fk-images-user_id',
                '{{%images}}',
                'user_id',
                '{{%users}}',
                'id'
            );
        } catch (\yii\db\Exception $e) {
            print 'Warning: The database does not contain \'users\' table';
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        try {
            // drops foreign key for table `users`
            $this->dropForeignKey(
                'fk-images-user_id',
                '{{%images}}'
            );
        } catch (\yii\db\Exception $e) {}

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-images-user_id',
            '{{%images}}'
        );

        $this->dropTable('{{%images}}');
    }
}
