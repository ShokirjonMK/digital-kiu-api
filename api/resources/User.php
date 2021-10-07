<?php

namespace api\resources;

use Yii;
use api\resources\Profile;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class User extends CommonUser
{
    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    public $avatar;
    public $avatarMaxSize = 1024 * 200; // 200 Kb

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

        /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email', 'status', 'password_hash'], 'required'],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password_reset_token'], 'unique'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view'], 'default', 'value' => ''],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            'id',
            'username',
            'firstname' => function ($model) {
                return $model->profile->firstname ?? '';
            },
            'lastname' => function ($model) {
                return $model->profile->lastname ?? '';
            },
            'role' => function ($model) {
                return $model->roleItem ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
            'email',
            'status'
            
        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields =  [
            'created_at',
            'updated_at',
            'profile'
        ];

        return $extraFields;
    }

    public function getPermissions(){
        if($this->roleItem){
            $authItem = AuthItem::find()->where(['name' => $this->roleItem])->one();
            $perms = $authItem->permissions;
            $result = [];
            if($perms && is_array($perms)){
                foreach ($perms as $row) {
                    $result[] = $row['name'];
                }
            }
            return $result;
        }else{
            return [];
        }
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    public static function createItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if(!$post){
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if($post && !(isset($post['role']) && !empty($post['role']) && is_string($post['role']))){
            $errors[] = ['role' => [_e('Role is not valid.')]];
        }

        if (count($errors) == 0) {
            if(isset($post['password']) && !empty($post['password'])){
                $model->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
            }
            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            if ($model->save()) {
                $profile->user_id = $model->id;

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if($avatarUrl){
                        $profile->image = $avatarUrl;
                    }else{
                        $errors[] = $model->errors;
                    }
                    
                }
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                }else{
                    
                    // role ni userga assign qilish
                    $auth = Yii::$app->authManager;
                    $authorRole = $auth->getRole($post['role']);
                    if($authorRole){
                        $auth->assign($authorRole, $model->id);
                    }else{
                        $errors[] = ['role' => [_e('Role not found.')]];    
                    }

                }
            } else {
                $errors[] = $model->errors;
            }
        }
        
        if(count($errors) == 0){
            $transaction->commit();
            return true;
        }else{
            $transaction->rollBack();
            return $errors;
        } 
    }

    public static function updateItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if(!$post){
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if(isset($post['role'])){
            if(empty($post['role']) || !is_string($post['role'])){
                $errors[] = ['role' => [_e('Role is not valid.')]];    
            }
        }

        if (count($errors) == 0) {
            if(isset($post['password']) && !empty($post['password'])){
                $model->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
            }
            if ($model->save()) {

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if($avatarUrl){
                        $profile->image = $avatarUrl;
                    }else{
                        $errors[] = $model->errors;
                    }
                    
                }
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                }else{
                    if(isset($post['role'])){
                        $auth = Yii::$app->authManager;
                        $authorRole = $auth->getRole($post['role']);
                        if($authorRole){
                            // user ning eski rolini o'chirish
                            $auth->revokeAll($model->id);
                            // role ni userga assign qilish
                            $auth->assign($authorRole, $model->id);
                        }else{
                            $errors[] = ['role' => [_e('Role not found.')]];    
                        }
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }
            
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return $errors;
        }
    }

    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = User::findOne($id);
        if(!$model){
            $errors[] = [_e('Data not found.')];
        }
        if (count($errors) == 0) {
            
            // remove profile image
            // $filePath = assets_url($model->profile->image);
            // if(file_exists($filePath)){
            //     unlink($filePath);
            // }
            // remove profile
            $profileDeleted = Profile::deleteAll(['user_id' => $id]);
            if(!$profileDeleted){
                $errors[] = [_e('Error in profile deleting process.')];   
            }

            // remove model
            $userDeleted = User::findOne($id)->delete();
            if(!$userDeleted){
                $errors[] = [_e('Error in user deleting process.')];   
            }
        }
            
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => _e('Active'),
            self::STATUS_BANNED => _e('Banned'),
            self::STATUS_PENDING => _e('Pending'),
        ];
    }    
    
    public function upload()
    {
        if ($this->validate()) { 
            $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->avatar->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->avatar->saveAs($url);
            return assets_url($miniUrl);
        } else {
            return false;
        }
    }
}