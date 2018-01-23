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
        // drop indexes and foreign keys with images table, save info in array
        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%images}}');
        $restore = [];

        foreach (Yii::$app->db->schema->getTableNames() as $name) {
            $table = Yii::$app->db->schema->getTableSchema($name);
            foreach ($table->foreignKeys as $key => $value) {
                if ($value[0] == 'images') {
                    $from_id = ''; $to_id = '';
                    foreach ($value as $keyKey => $keyValue) {
                        if ($keyKey !== 0) {
                            $from_id = $keyKey;
                            $to_id = $keyValue;
                        }
                    }
                    $restore[] = [
                        'index' => $key,
                        'from_table' => $name,
                        'to_table' => 'images',
                        'from_id' => $from_id,
                        'to_id' => $to_id,
                    ];
                    try {
                        $this->dropForeignKey($key, $name);
                        $this->dropIndex($key, $name);
                    } catch (\yii\db\Exception $e) {}
                }
            }
        }

        // drop exists table
        if ($tableSchema) {
            $this->truncateTable('{{%images}}');
            foreach ($tableSchema->foreignKeys as $key => $value) {
                try {
                    $this->dropForeignKey($key, $value[0]);
                    $this->dropIndex($key, $value[0]);
                } catch (\yii\db\Exception $e) {}
            }
            $this->dropTable('{{%images}}');
        }

        // create table
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

        // restore indexes and foreign keys
        foreach ($restore as $value) {
            $this->addForeignKey($value['index'], $value['from_table'], $value['from_id'], $value['to_table'], $value['to_id']);
            $this->createIndex($value['index'], $value['from_table'], $value['from_id']);
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
