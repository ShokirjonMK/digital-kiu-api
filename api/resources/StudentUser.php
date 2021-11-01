<?php

namespace api\resources;

use Yii;
use api\resources\Profile;
use common\models\Student;
use common\models\UserSubject;
use yii\web\UploadedFile;

class StudentUser extends ParentUser
{
    public static $roleList = ['student', 'master'];

    public static function createItem($model, $profile, $student, $post)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validatin input data

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (!(isset($post['role']) && !empty($post['role']) && is_string($post['role']))) {
            $errors[] = ['role' => [_e('Role is not valid.')]];
        }

        if (isset($post['role'])) {
            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        // **********

        if (count($errors) == 0) {
           
            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
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

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    $student->user_id = $model->id;
                    if (!$student->save()) {
                        $errors[] = $student->errors;
                    } else {
                        // role ni userga assign qilish
                        $auth->assign($authorRole, $model->id);
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

    public static function updateItem($model, $profile, $student, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        if (isset($post['role'])) {

            // role to'gri jo'natilganligini tekshirish
            if (empty($post['role']) || !is_string($post['role'])) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }

            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        if (count($errors) == 0) {
           
            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
            } else {
                $password = _random_string();
            }

            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            if ($model->save()) {

                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */

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

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    if ($student->save()) {
                        if (isset($post['role'])) {
                            // user ning eski rolini o'chirish
                            $auth->revokeAll($model->id);
                            // role ni userga assign qilish
                            $auth->assign($authorRole, $model->id);
                        }
                    } else {
                        $errors[] = $student->errors;
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

        $model = self::findStudent($id);
        if (!$model || !$model->student || !$model->profile) {
            $errors[] = [_e('Student not found.')];
        }

        if (count($errors) == 0) {

            // remove profile image
            $filePath = assets_url($model->profile->image);
            if(file_exists($filePath)){
                unlink($filePath);
            }

            // remove student
            $studentDeleted = Student::findOne(['user_id' => $id]);
            if (!$studentDeleted) {
                $errors[] = [_e('Error in student deleting process.')];
            }else{
                $studentDeleted->is_deleted = 1;
                $studentDeleted->save(false);
            }

            // remove profile
            $profileDeleted = Profile::findOne(['user_id' => $id]);
            if (!$profileDeleted) {
                $errors[] = [_e('Error in profile deleting process.')];
            }else{
                $profileDeleted->is_deleted = 1;
                $profileDeleted->save(false);
            }

            // remove model
            $userDeleted = User::findOne($id);
            if (!$userDeleted) {
                $errors[] = [_e('Error in user deleting process.')];
            }else{
                $userDeleted->status = 9;
                $userDeleted->save(false);
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

    public static function findStudent($id)
    {
        return self::find()
            ->with(['profile', 'student'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['and', ['id' => $id], ['in', 'auth_assignment.item_name', self::$roleList]])
            ->one();
    }
}
