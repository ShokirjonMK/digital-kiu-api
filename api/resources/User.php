<?php

namespace api\resources;

use common\models\AuthAssignment;
use common\models\model\TeacherAccess;
use common\models\model\PasswordEncrypts;
use Yii;
//use api\resources\Profile;
use common\models\model\Profile;
use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\Profile as ModelProfile;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class User extends CommonUser
{
    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    // const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';
    public $avatar;
    public $passport_file;
    public $avatarMaxSize = 1024 * 200; // 200 Kb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb


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
            [['passport_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png, jpg', 'maxSize' => $this->passportFileMaxSize],
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
        $fields = [
            'id',
            'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'role' => function ($model) {
                return $model->roles ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
            // 'passport_file' => function ($model) {
            //     return $model->profile->passport_file ?? '';
            // },
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
        $extraFields = [
            'created_at',
            'updated_at',
            'profile'
        ];

        return $extraFields;
    }

    public function getPermissions()
    {
        if ($this->roleItem) {
            $authItem = AuthItem::find()->where(['name' => $this->roleItem])->one();
            $perms = $authItem->permissions;
            $result = [];
            if ($perms && is_array($perms)) {
                foreach ($perms as $row) {
                    $result[] = $row['name'];
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getRoles()
    {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                $result[] = $authItem['item_name'];
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getProfile()
    {
        return $this->hasOne(ModelProfile::className(), ['user_id' => 'id']);
    }

    public static function createItem($model, $profile, $post)
    {


        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        $roles = $post['role'];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!(isset($role) && !empty($role) && is_string($role))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        } else {
            if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }
        }

        if (count($errors) == 0) {

            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    $password = $post['password'];
                } else {
                    $password = _random_string();
                }
            } else {
                $password = _random_string();
            }
            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            if ($model->save()) {

                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */

                $profile->user_id = $model->id;

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                // passport file saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    // role ni userga assign qilish
                    $auth = Yii::$app->authManager;
                    $roles = json_decode(str_replace("'", "", $post['role']));
                    if (is_array($roles)) {

                        foreach ($roles as $role) {
                            $authorRole = $auth->getRole($role);
                            if ($authorRole) {
                                $auth->assign($authorRole, $model->id);
                                if ($role == 'teacher' && isset($post['teacherAccess'])) {
                                    $teacherAccess = json_decode(str_replace("'", "", $post['teacherAccess']));
                                    foreach ($teacherAccess as $subjectIds => $subjectIdsValues) {
                                        if (is_array($subjectIdsValues)) {
                                            foreach ($subjectIdsValues as $langId) {
                                                $teacherAccessNew = new TeacherAccess();
                                                $teacherAccessNew->user_id = $model->id;
                                                $teacherAccessNew->subject_id = (int)$subjectIds;
                                                $teacherAccessNew->language_id = (int)$langId;
                                                $teacherAccessNew->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $errors[] = ['role' => [_e('Role not found.')]];
                            }
                        }
                    } else {
                        $errors[] = ['role' => [_e('Role is invalid')]];
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

    public static function updateItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        $roles = $post['role'];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!(isset($role) && !empty($role) && is_string($role))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        } else {
            if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }
        }

        if (count($errors) == 0) {
            /* * Password */
            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    $password = $post['password'];
                    //**parolni shifrlab saqlaymiz */
                    $model->savePassword($password, $model->id);
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                }
            }

            if ($model->save()) {

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                // passport file saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***


                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    $auth = Yii::$app->authManager;
                    $roles = json_decode(str_replace("'", "", $post['role']));

                    if (is_array($roles)) {
                        $auth->revokeAll($model->id);
                        foreach ($roles as $role) {
                            $authorRole = $auth->getRole($role);
                            if ($authorRole) {
                                $auth->assign($authorRole, $model->id);
                                if ($role == 'teacher' && isset($post['teacherAccess'])) {
                                    $teacherAccess = json_decode(str_replace("'", "", $post['teacherAccess']));
                                    TeacherAccess::deleteAll(['user_id' => $model->id]);
                                    foreach ($teacherAccess as $subjectIds => $subjectIdsValues) {
                                        if (is_array($subjectIdsValues)) {
                                            foreach ($subjectIdsValues as $langId) {
                                                $teacherAccessNew = new TeacherAccess();
                                                $teacherAccessNew->user_id = $model->id;
                                                $teacherAccessNew->subject_id = $subjectIds;
                                                $teacherAccessNew->language_id = $langId;
                                                $teacherAccessNew->save();
                                            }
                                        }
                                    }
                                }
                                //                                }
                            } else {
                                $errors[] = ['role' => [_e('Role not found.')]];
                            }
                        }
                    } else {
                        $errors[] = ['role' => [_e('Role is invalid')]];
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
        if (!$model) {
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
            if (!$profileDeleted) {
                $errors[] = [_e('Error in profile deleting process.')];
            }

            // remove model
            $userDeleted = User::findOne($id)->delete();
            if (!$userDeleted) {
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
            $this->avatar->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function uploadPassport()
    {
        if ($this->validate()) {
            $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->passport_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->passport_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    //**parolni shifrlab saqlaymiz */

    public function savePassword($password, $user_id)
    {
        // if exist delete and create new one 
        $oldPassword = PasswordEncrypts::find(['user_id', $user_id])->all();
        if (isset($oldPassword)) {
            foreach ($oldPassword as $pass){
                $pass->delete();
            }
        }

        $uu = new EncryptPass();
        $max = Keys::find()->count();
        $rand = rand(1, $max);
        $key = Keys::findOne($rand);
        $enc = $uu->encrypt($password, $key->name);
        $save_password = new PasswordEncrypts();
        $save_password->user_id = $user_id;
        $save_password->password = $enc;
        $save_password->key_id = $key->id;
        if ($save_password->save(false)) {
            return true;
        } else {
            return false;
        }
    }
}
