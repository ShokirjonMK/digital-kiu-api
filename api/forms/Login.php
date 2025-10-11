<?php

namespace api\forms;

use api\resources\User;
use common\models\model\LoginHistory;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class Login extends Model
{
    public $username;
    public $password;

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }


    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool|object whether the user is logged in successfully
     */
    public function authorize()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if ($user) {
                $user->generateAccessToken();
                $user->access_token_time = time();
                $user->save();
                return ['is_ok' => true, 'user' => $user];
            } else {
                return ['is_ok' => false, 'errors' => [_e('User not found')]];
            }
        } else {
            return ['is_ok' => false, 'errors' => $this->getErrorSummary(true)];
        }
    }

    public static function logout()
    {
        $user = User::findOne(current_user_id());
        if (isset($user)) {
            LoginHistory::createItemLogin(current_user_id(), LoginHistory::LOGOUT);
            Yii::$app->user->logout();
            $user->access_token = NULL;
            $user->access_token_time = NULL;
            $user->save(false);
            // $user->logout();
            return true;
        }

        return false;
    }

    public static function login($model, $post)
    {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->authorize();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $profile = $user->profile;
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        'role' => $user->getRoles(),
                        'oferta' => $user->getOfertaIsComformed(),
                        'permissions' => $user->permissionsAll,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $errors[] = [_e('User is not active.')];
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = [_e('Username and password cannot be blank.')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public static function loginMain($model, $post)
    {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->authorize();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $profile = $user->profile;
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        'role' => $user->getRolesNoStudent(),
                        'oferta' => $user->getOfertaIsComformed(),
                        'is_changed' => $user->is_changed,
                        'permissions' => $user->permissionsNoStudent,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $errors[] = [_e('User is not active.')];
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = [_e('Username and password cannot be blank.')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public static function loginStd($model, $post)
    {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->authorize();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $profile = $user->profile;
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        'role' => $user->getRolesStudent(),
                        'oferta' => $user->getOfertaIsComformed(),
                        'permissions' => $user->permissionsStudent,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $errors[] = [_e('User is not active.')];
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = [_e('Username and password cannot be blank.')];
        }

        // new LoginHistory();

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    /**
     * Finds user by [[username]] or passport credentials
     *
     * @return User|null
     */
    protected function getUser()
    {
        // Avval oddiy username orqali qidiramiz
        $user = User::findByUsername($this->username);
        
        if ($user) {
            return $user;
        }
        
        // Agar username topilmasa, passport ma'lumotlari bilan qidiramiz
        // Bu holatda username passport_pin bo'lishi mumkin
        $user = User::find()
            ->joinWith('profile')
            ->where(['profile.passport_pin' => $this->username])
            ->one();
            
        return $user;
    }
    
    /**
     * Validates password for both regular and passport login
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, _e('Incorrect login or password.'));
                return;
            }
            
            // Oddiy parol tekshiruvi
            if ($user->validatePassword($this->password)) {
                return;
            }
            
            // Agar oddiy parol mos kelmasa, passport ma'lumotlari bilan tekshiramiz
            if ($user->profile && 
                $user->profile->passport_seria && 
                $user->profile->passport_number) {
                $passportPassword = $user->profile->passport_seria . $user->profile->passport_number;
                if ($this->password === $passportPassword) {
                    return;
                }
            }
            
            $this->addError($attribute, _e('Incorrect login or password.'));
        }
    }
}
