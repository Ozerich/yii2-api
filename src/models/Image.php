<?php

namespace blakit\api\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%images}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $ext
 * @property string $size
 * @property string $mime
 * @property string $width
 * @property string $height
 * @property integer $created_at
 * @property integer $user_id
 *
 * @property User $user
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%images}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ext', 'size', 'mime', 'width', 'height'], 'required'],
            [['created_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['ext'], 'string', 'max' => 3],
            [['size'], 'integer', 'max' => 5 * 1024 * 1024],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Uploads folders for image
     * @return string
     */
    public function getUploadsFolderPath()
    {
        return implode('/', [mb_substr($this->name, 0, 2), mb_substr($this->name, 2, 2)]);
    }

    /**
     * Make dirs before upload image
     * @return $this
     */
    public function prepareMkdir()
    {
        $firstDirArray = explode('/', $this->getUploadsFolderSystemPath());
        unset($firstDirArray[count($firstDirArray)-1]);
        $firstDir = implode('/', $firstDirArray);
        if (!file_exists($firstDir)) {
            mkdir($firstDir);
        }
        $secondDir = $this->getUploadsFolderSystemPath();
        if (!file_exists($secondDir)) {
            mkdir($secondDir);
        }
        return $this;
    }

    /**
     * Uploads folder system path
     * @return string
     */
    public function getUploadsFolderSystemPath()
    {
        return Yii::getAlias('@webroot') . $this->getUploadDir() . $this->getUploadsFolderPath();
    }

    /**
     * Path to image
     * @return string
     */
    public function getFilepath()
    {
        return $this->getUploadDir() . $this->getUploadsFolderPath() . '/' . $this->name . '.' . $this->ext;
    }

    /**
     * Get full system path to image
     * @return string
     */
    public function getSystemPath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir() . $this->getUploadsFolderPath() . '/' . $this->name . '.' . $this->ext;
    }

    /**
     * Get URL for image
     * @return string
     */
    public function getUrl()
    {
        return Url::to($this->getFilepath(), true);
    }

    /**
     * Get upload dir from module params
     * @return string
     */
    public function getUploadDir()
    {
        $uploadDir = Yii::$app->controller->module->defaultUploadImagesDir;
        if ($uploadDir && $uploadDir[0] != '/') $uploadDir = '/' . $uploadDir;
        if ($uploadDir && $uploadDir[sizeof($uploadDir)-1] != '/') $uploadDir .= '/';
        return $uploadDir;
    }

    /**
     * Full image info in JSON format
     * @return array
     */
    public function toJSON()
    {
        return [
            'id' => $this->id,
            'url' => $this->getUrl(),
            'width' => $this->width,
            'height' => $this->height,
            'name' => $this->name,
            'ext' => $this->ext,
            'mime' => $this->mime,
            'size' => $this->size,
        ];
    }
}